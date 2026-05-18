<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asignacion;
use App\Models\Cupo;
use App\Models\ListaEspera;
use App\Models\Postulacion;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TutorCupoService
{
    public const HORAS_LIMITE_RESPUESTA = 48;

    /**
     * @return array{accion: string, promovido: bool, postulado_promovido_id: int|null}
     */
    public function responderCupo(Postulacion $postulacion, string $accion): array
    {
        if (! in_array($accion, ['aceptar', 'rechazar'], true)) {
            throw new InvalidArgumentException('Acción no válida.');
        }

        return DB::transaction(function () use ($postulacion, $accion): array {
            /** @var Postulacion $postulacionBloqueada */
            $postulacionBloqueada = Postulacion::query()
                ->whereKey($postulacion->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($postulacionBloqueada->aceptacion_cupo !== null) {
                throw new InvalidArgumentException('Este cupo ya fue respondido anteriormente.');
            }

            /** @var Asignacion|null $asignacion */
            $asignacion = Asignacion::query()
                ->where('id_pos_asi', $postulacionBloqueada->id_pos)
                ->whereIn('estado_asi', ['pendiente', 'asignado'])
                ->lockForUpdate()
                ->orderByDesc('id_asi')
                ->first();

            if ($asignacion === null) {
                throw new InvalidArgumentException('No existe una asignación activa para responder.');
            }

            if ($asignacion->estaVencida()) {
                $promovido = $this->vencerAsignacionYLiberarCupo($postulacionBloqueada, $asignacion);

                return [
                    'accion' => 'vencido',
                    'promovido' => $promovido !== null,
                    'postulado_promovido_id' => $promovido?->id_pos_asi,
                ];
            }

            if ($accion === 'aceptar') {
                $postulacionBloqueada->update([
                    'aceptacion_cupo' => true,
                    'fecha_aceptacion_cupo' => now(),
                ]);

                $asignacion->update([
                    'estado_asi' => 'aceptado',
                    'fecha_asi' => $asignacion->fecha_asi ?? now(),
                ]);

                return [
                    'accion' => 'aceptar',
                    'promovido' => false,
                    'postulado_promovido_id' => null,
                ];
            }

            $postulacionBloqueada->update([
                'aceptacion_cupo' => false,
                'fecha_aceptacion_cupo' => now(),
            ]);

            $asignacion->update([
                'estado_asi' => 'rechazado',
                'fecha_asi' => $asignacion->fecha_asi ?? now(),
            ]);

            $cupo = $this->obtenerCupoBloqueado($asignacion);

            if ($cupo === null) {
                return [
                    'accion' => 'rechazar',
                    'promovido' => false,
                    'postulado_promovido_id' => null,
                ];
            }

            $this->liberarCupo($cupo);

            $asignacionPromovida = $this->promoverSiguienteDesdeListaEspera($cupo);

            return [
                'accion' => 'rechazar',
                'promovido' => $asignacionPromovida !== null,
                'postulado_promovido_id' => $asignacionPromovida?->id_pos_asi,
            ];
        });
    }

    /**
     * Procesa una asignación vencida cuando el tutor entra al detalle o cuando el admin revisa asignaciones.
     *
     * @return array{vencido: bool, promovido: bool, postulado_promovido_id: int|null}
     */
    public function procesarVencimientoSiCorresponde(Postulacion $postulacion): array
    {
        return DB::transaction(function () use ($postulacion): array {
            /** @var Postulacion $postulacionBloqueada */
            $postulacionBloqueada = Postulacion::query()
                ->whereKey($postulacion->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($postulacionBloqueada->aceptacion_cupo !== null) {
                return [
                    'vencido' => false,
                    'promovido' => false,
                    'postulado_promovido_id' => null,
                ];
            }

            /** @var Asignacion|null $asignacion */
            $asignacion = Asignacion::query()
                ->where('id_pos_asi', $postulacionBloqueada->id_pos)
                ->whereIn('estado_asi', ['pendiente', 'asignado'])
                ->lockForUpdate()
                ->orderByDesc('id_asi')
                ->first();

            if ($asignacion === null || ! $asignacion->estaVencida()) {
                return [
                    'vencido' => false,
                    'promovido' => false,
                    'postulado_promovido_id' => null,
                ];
            }

            $asignacionPromovida = $this->vencerAsignacionYLiberarCupo($postulacionBloqueada, $asignacion);

            return [
                'vencido' => true,
                'promovido' => $asignacionPromovida !== null,
                'postulado_promovido_id' => $asignacionPromovida?->id_pos_asi,
            ];
        });
    }

    public function procesarVencimientosPendientesPorUnidad(int $unidadId): int
    {
        $asignacionIds = Asignacion::query()
            ->whereIn('estado_asi', ['pendiente', 'asignado'])
            ->whereNotNull('fecha_limite_respuesta_asi')
            ->where('fecha_limite_respuesta_asi', '<', now())
            ->whereHas('postulacion.ofertaAcademica', fn ($q) => $q->where('id_ued_oac', $unidadId))
            ->pluck('id_asi');

        $procesadas = 0;

        foreach ($asignacionIds as $asignacionId) {
            $asignacion = Asignacion::query()
                ->with('postulacion')
                ->find($asignacionId);

            if ($asignacion?->postulacion !== null) {
                $resultado = $this->procesarVencimientoSiCorresponde($asignacion->postulacion);

                if ($resultado['vencido']) {
                    $procesadas++;
                }
            }
        }

        return $procesadas;
    }

    private function vencerAsignacionYLiberarCupo(Postulacion $postulacion, Asignacion $asignacion): ?Asignacion
    {
        $postulacion->update([
            'aceptacion_cupo' => false,
            'fecha_aceptacion_cupo' => $asignacion->fecha_limite_respuesta_asi ?? now(),
        ]);

        $asignacion->update([
            'estado_asi' => 'vencido',
            'fecha_asi' => $asignacion->fecha_asi ?? now(),
        ]);

        $cupo = $this->obtenerCupoBloqueado($asignacion);

        if ($cupo === null) {
            return null;
        }

        $this->liberarCupo($cupo);

        return $this->promoverSiguienteDesdeListaEspera($cupo);
    }

    private function obtenerCupoBloqueado(Asignacion $asignacion): ?Cupo
    {
        if ($asignacion->id_cup_asi === null) {
            return null;
        }

        return Cupo::query()
            ->whereKey($asignacion->id_cup_asi)
            ->lockForUpdate()
            ->first();
    }

    private function liberarCupo(Cupo $cupo): void
    {
        $cupo->update([
            'disponibles_cup' => min(
                (int) $cupo->total_cup,
                (int) $cupo->disponibles_cup + 1
            ),
        ]);
    }

    private function promoverSiguienteDesdeListaEspera(Cupo $cupo): ?Asignacion
    {
        if ((int) $cupo->disponibles_cup <= 0) {
            return null;
        }

        while (true) {
            /** @var ListaEspera|null $siguiente */
            $siguiente = ListaEspera::query()
                ->where('id_oac_les', $cupo->id_oac_cup)
                ->whereHas('postulacion', fn ($query) => $query->whereNull('aceptacion_cupo'))
                ->orderBy('orden_les')
                ->orderBy('id_les')
                ->lockForUpdate()
                ->first();

            if ($siguiente === null) {
                return null;
            }

            $yaTieneAsignacionActiva = Asignacion::query()
                ->where('id_pos_asi', $siguiente->id_pos_les)
                ->whereIn('estado_asi', ['pendiente', 'asignado', 'aceptado'])
                ->lockForUpdate()
                ->exists();

            if ($yaTieneAsignacionActiva) {
                $ofertaId = (int) $siguiente->id_oac_les;
                $siguiente->delete();
                $this->reordenarListaEspera($ofertaId);

                continue;
            }

            $asignacion = Asignacion::query()->create([
                'id_pos_asi' => $siguiente->id_pos_les,
                'id_cup_asi' => $cupo->id_cup,
                'estado_asi' => 'asignado',
                'fecha_asi' => now(),
                'fecha_limite_respuesta_asi' => now()->addHours(self::HORAS_LIMITE_RESPUESTA),
            ]);

            $ofertaId = (int) $siguiente->id_oac_les;
            $siguiente->delete();

            $cupo->update([
                'disponibles_cup' => max(0, (int) $cupo->disponibles_cup - 1),
            ]);

            $this->reordenarListaEspera($ofertaId);

            return $asignacion;
        }
    }

    private function reordenarListaEspera(int $ofertaId): void
    {
        $registros = ListaEspera::query()
            ->where('id_oac_les', $ofertaId)
            ->orderBy('orden_les')
            ->orderBy('id_les')
            ->get();

        $orden = 1;

        foreach ($registros as $registro) {
            $registro->update([
                'orden_les' => $orden,
            ]);

            $orden++;
        }
    }
}