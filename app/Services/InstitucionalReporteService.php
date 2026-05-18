<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asignacion;
use App\Models\Cupo;
use App\Models\Documento;
use App\Models\ListaEspera;
use App\Models\OfertaAcademica;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class InstitucionalReporteService
{
    public function __construct(
        private readonly PostulacionInstitucionalService $postulaciones,
        private readonly ResultadoInstitucionalService $resultados,
        private readonly ListaEsperaInstitucionalService $listaEspera,
    ) {}

    /**
     * @return array{
     *     postulaciones: int,
     *     ofertas: int,
     *     cupos_total: int,
     *     cupos_disponibles: int,
     *     cupos_asignados: int,
     *     lista_espera: int,
     *     documentos_pendientes: int,
     *     con_evaluacion: int,
     *     por_estado: Collection<int, array{nombre: string, total: int}>
     * }
     */
    public function indicadores(int $unidadId): array
    {
        $resumenResultados = $this->resultados->resumen($unidadId);
        $resumenLista = $this->listaEspera->resumen($unidadId);

        $cupos = Cupo::query()
            ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
            ->selectRaw('COALESCE(SUM(total_cup), 0) as total, COALESCE(SUM(disponibles_cup), 0) as disponibles')
            ->first();

        $documentosPendientes = Documento::query()
            ->where('estado_doc', 'pendiente')
            ->whereHas('postulacion.ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
            ->count();

        $porEstado = collect($this->postulaciones->resumenParaUnidad($unidadId, new \Illuminate\Http\Request)['por_estado'] ?? [])
            ->take(6);

        return [
            'postulaciones' => $resumenResultados['postulaciones'],
            'ofertas' => OfertaAcademica::query()->where('id_ued_oac', $unidadId)->count(),
            'cupos_total' => (int) ($cupos->total ?? 0),
            'cupos_disponibles' => (int) ($cupos->disponibles ?? 0),
            'cupos_asignados' => $resumenResultados['asignados'],
            'lista_espera' => $resumenLista['total'],
            'documentos_pendientes' => $documentosPendientes,
            'con_evaluacion' => $resumenResultados['con_evaluacion'],
            'por_estado' => $porEstado,
        ];
    }

    /**
     * Resumen por oferta para exportación consolidada.
     *
     * @return Collection<int, object>
     */
    public function filasResumenPorOferta(int $unidadId): Collection
    {
        return OfertaAcademica::query()
            ->with(['gestion', 'nivel', 'curso', 'paralelo', 'cupos'])
            ->withCount('postulaciones')
            ->where('id_ued_oac', $unidadId)
            ->orderByDesc('id_ges_oac')
            ->get()
            ->map(function (OfertaAcademica $oac) {
                $cupo = $oac->cupos->first();
                $totalCup = (int) ($cupo->total_cup ?? 0);
                $disponibles = (int) ($cupo->disponibles_cup ?? 0);
                $asignados = Asignacion::query()
                    ->where('estado_asi', 'asignado')
                    ->whereHas('cupo', fn (Builder $q) => $q->where('id_oac_cup', $oac->id_oac))
                    ->count();
                $enEspera = ListaEspera::query()->where('id_oac_les', $oac->id_oac)->count();

                return (object) [
                    'gestion' => $oac->gestion->nombre_ges ?? '',
                    'nivel' => $oac->nivel->nombre_niv ?? '',
                    'curso' => $oac->curso->nombre_cur ?? '',
                    'paralelo' => $oac->paralelo->nombre_par ?? '',
                    'postulaciones' => $oac->postulaciones_count,
                    'cupos_total' => $totalCup,
                    'cupos_disponibles' => $disponibles,
                    'cupos_asignados' => $asignados,
                    'lista_espera' => $enEspera,
                ];
            });
    }
}
