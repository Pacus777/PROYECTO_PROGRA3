<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asignacion;
use App\Models\Cupo;
use App\Models\EstadoPostulacion;
use App\Models\ListaEspera;
use App\Models\OfertaAcademica;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class ListaEsperaInstitucionalService
{
    public function queryParaUnidad(int $unidadId, Request $request): Builder
    {
        $query = ListaEspera::query()
            ->with([
                'postulacion.estudiante.persona',
                'postulacion.estadoPostulacion',
                'postulacion.resultado',
                'ofertaAcademica.gestion',
                'ofertaAcademica.curso',
                'ofertaAcademica.paralelo',
                'ofertaAcademica.cupos',
            ])
            ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId));

        if ($request->filled('id_ges_oac')) {
            $query->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ges_oac', (int) $request->input('id_ges_oac')));
        }

        if ($request->filled('id_cur_oac')) {
            $query->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_cur_oac', (int) $request->input('id_cur_oac')));
        }

        if ($request->filled('id_oac_pos')) {
            $query->where('id_oac_les', (int) $request->input('id_oac_pos'));
        }

        if ($request->filled('buscar')) {
            $term = '%'.trim((string) $request->input('buscar')).'%';
            $query->where(function (Builder $q) use ($term): void {
                $q->whereHas('postulacion.estudiante.persona', function (Builder $inner) use ($term): void {
                    $inner->where('nombres_per', 'ilike', $term)
                        ->orWhere('ap_paterno_per', 'ilike', $term)
                        ->orWhere('ap_materno_per', 'ilike', $term);
                })->orWhereHas('postulacion.estudiante', fn (Builder $inner) => $inner->where('rude_est', 'ilike', $term));
            });
        }

        return $query->orderBy('id_oac_les')->orderBy('orden_les');
    }

    /**
     * @return array{total: int, ofertas_con_espera: int, primeros_en_cola: int, cupos_libres_en_ofertas_con_espera: int}
     */
    public function resumen(int $unidadId): array
    {
        $base = ListaEspera::query()
            ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId));

        $ofertasConEspera = (clone $base)->distinct('id_oac_les')->count('id_oac_les');

        $primeros = ListaEspera::query()
            ->with('ofertaAcademica.cupos')
            ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
            ->whereRaw('orden_les = (SELECT MIN(l2.orden_les) FROM lista_espera l2 WHERE l2.id_oac_les = lista_espera.id_oac_les)')
            ->get();

        $cuposLibres = 0;
        foreach ($primeros as $les) {
            $cupo = $les->ofertaAcademica?->cupos()->first();
            if ($cupo !== null && (int) $cupo->disponibles_cup > 0) {
                $cuposLibres += (int) $cupo->disponibles_cup;
            }
        }

        return [
            'total' => (clone $base)->count(),
            'ofertas_con_espera' => $ofertasConEspera,
            'primeros_en_cola' => $primeros->count(),
            'cupos_libres_en_ofertas_con_espera' => $cuposLibres,
        ];
    }

    /**
     * @return Collection<int, object{oferta: OfertaAcademica, en_espera: int, cupos_disponibles: int, primero: ?ListaEspera}>
     */
    public function resumenPorOferta(int $unidadId, int $limit = 8): Collection
    {
        $conteos = ListaEspera::query()
            ->selectRaw('id_oac_les, COUNT(*) as total')
            ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
            ->groupBy('id_oac_les')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();

        return $conteos->map(function ($row) {
            $oferta = OfertaAcademica::query()
                ->with(['gestion', 'curso', 'paralelo', 'cupos'])
                ->find($row->id_oac_les);

            $primero = ListaEspera::query()
                ->with('postulacion.estudiante.persona')
                ->where('id_oac_les', $row->id_oac_les)
                ->orderBy('orden_les')
                ->first();

            return (object) [
                'oferta' => $oferta,
                'en_espera' => (int) $row->total,
                'cupos_disponibles' => (int) ($oferta?->cupos()->first()?->disponibles_cup ?? 0),
                'primero' => $primero,
            ];
        });
    }

    public function esPrimeroEnOferta(ListaEspera $listaEspera): bool
    {
        $minOrden = ListaEspera::query()
            ->where('id_oac_les', $listaEspera->id_oac_les)
            ->min('orden_les');

        return (int) $listaEspera->orden_les === (int) $minOrden;
    }

    public function puedeAsignarCupo(ListaEspera $listaEspera): bool
    {
        if (! $this->esPrimeroEnOferta($listaEspera)) {
            return false;
        }

        $cupo = $listaEspera->ofertaAcademica?->cupos()->first();

        return $cupo !== null && (int) $cupo->disponibles_cup > 0;
    }

    /** Asigna cupo al primero en cola (debe ser el registro indicado). */
    public function asignarCupoDesdeEspera(int $unidadId, ListaEspera $listaEspera): void
    {
        $listaEspera->loadMissing(['postulacion', 'ofertaAcademica.cupos']);

        if ((int) optional($listaEspera->ofertaAcademica)->id_ued_oac !== $unidadId) {
            throw new \InvalidArgumentException('La entrada no pertenece a su unidad educativa.');
        }

        if (! $this->puedeAsignarCupo($listaEspera)) {
            throw new \InvalidArgumentException('No hay cupo disponible o el postulante no es el primero en la cola.');
        }

        $postulacion = $listaEspera->postulacion;
        $oferta = $listaEspera->ofertaAcademica;
        $cupo = $oferta->cupos()->first();

        $aprobadaEstadoId = (int) EstadoPostulacion::query()
            ->whereRaw('LOWER(nombre_ept) LIKE ?', ['%aprobada%'])
            ->value('id_ept');

        DB::transaction(function () use ($listaEspera, $postulacion, $cupo, $aprobadaEstadoId): void {
            $ordenRemovido = (int) $listaEspera->orden_les;
            $ofertaId = (int) $listaEspera->id_oac_les;

            Asignacion::query()->updateOrCreate(
                ['id_pos_asi' => $postulacion->id_pos],
                [
                    'id_cup_asi' => $cupo->id_cup,
                    'estado_asi' => 'asignado',
                    'fecha_asi' => now(),
                ],
            );

            $listaEspera->delete();

            ListaEspera::query()
                ->where('id_oac_les', $ofertaId)
                ->where('orden_les', '>', $ordenRemovido)
                ->decrement('orden_les');

            $cupo->update(['disponibles_cup' => (int) $cupo->disponibles_cup - 1]);

            if ($aprobadaEstadoId > 0) {
                $postulacion->update(['id_ept_pos' => $aprobadaEstadoId]);
            }
        });
    }

    /** @return Collection<int, OfertaAcademica> */
    public function ofertasParaFiltro(int $unidadId): Collection
    {
        return OfertaAcademica::query()
            ->with(['gestion', 'curso', 'paralelo'])
            ->where('id_ued_oac', $unidadId)
            ->orderByDesc('id_ges_oac')
            ->get();
    }
}
