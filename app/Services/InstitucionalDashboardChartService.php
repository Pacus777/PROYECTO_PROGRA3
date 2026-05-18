<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asignacion;
use App\Models\Cupo;
use App\Models\Documento;
use App\Models\ListaEspera;
use App\Models\Postulacion;
use App\Models\Resultado;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class InstitucionalDashboardChartService
{
    public function __construct(
        private readonly InstitucionalReporteService $reportes,
    ) {}

    /**
     * @return array{
     *     kpis: array<string, int|string>,
     *     charts: array<string, array{labels: list<string>, data: list<int>}>
     * }
     */
    public function build(int $unidadId): array
    {
        $indicadores = $this->reportes->indicadores($unidadId);

        return [
            'kpis' => [
                'postulaciones' => $indicadores['postulaciones'],
                'ofertas' => $indicadores['ofertas'],
                'cupos_total' => $indicadores['cupos_total'],
                'cupos_disponibles' => $indicadores['cupos_disponibles'],
                'cupos_asignados' => $indicadores['cupos_asignados'],
                'lista_espera' => $indicadores['lista_espera'],
                'documentos_pendientes' => $indicadores['documentos_pendientes'],
                'con_evaluacion' => $indicadores['con_evaluacion'],
            ],
            'charts' => [
                'postulaciones_estado' => $this->postulacionesPorEstado($unidadId),
                'postulaciones_oferta' => $this->postulacionesPorOferta($unidadId),
                'postulaciones_mes' => $this->postulacionesPorMes($unidadId),
                'cupos_resumen' => $this->cuposResumen($unidadId),
                'documentos_estado' => $this->documentosPorEstado($unidadId),
                'evaluacion_avance' => $this->evaluacionAvance($unidadId),
            ],
        ];
    }

    private function postulacionesBase(int $unidadId): Builder
    {
        return Postulacion::query()
            ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId));
    }

    /**
     * @return array{labels: list<string>, data: list<int>}
     */
    private function postulacionesPorEstado(int $unidadId): array
    {
        $rows = $this->postulacionesBase($unidadId)
            ->join('estado_postulacion', 'postulacion.id_ept_pos', '=', 'estado_postulacion.id_ept')
            ->selectRaw('estado_postulacion.nombre_ept as estado, COUNT(*) as total')
            ->groupBy('estado_postulacion.nombre_ept')
            ->orderByDesc('total')
            ->get();

        $labels = [];
        $data = [];

        foreach ($rows as $row) {
            $labels[] = $this->humanize((string) $row->estado);
            $data[] = (int) $row->total;
        }

        return compact('labels', 'data');
    }

    /**
     * @return array{labels: list<string>, data: list<int>}
     */
    private function postulacionesPorOferta(int $unidadId): array
    {
        $rows = $this->postulacionesBase($unidadId)
            ->join('oferta_academica', 'postulacion.id_oac_pos', '=', 'oferta_academica.id_oac')
            ->leftJoin('curso', 'oferta_academica.id_cur_oac', '=', 'curso.id_cur')
            ->leftJoin('paralelo', 'oferta_academica.id_par_oac', '=', 'paralelo.id_par')
            ->selectRaw("TRIM(CONCAT(COALESCE(curso.nombre_cur, 'Oferta'), ' ', COALESCE(paralelo.nombre_par, ''))) as oferta, COUNT(*) as total")
            ->groupBy('oferta_academica.id_oac', 'curso.nombre_cur', 'paralelo.nombre_par')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $labels = [];
        $data = [];

        foreach ($rows as $row) {
            $labels[] = (string) $row->oferta;
            $data[] = (int) $row->total;
        }

        return compact('labels', 'data');
    }

    /**
     * @return array{labels: list<string>, data: list<int>}
     */
    private function postulacionesPorMes(int $unidadId): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = ucfirst($month->locale('es')->isoFormat('MMM YYYY'));
            $data[] = $this->postulacionesBase($unidadId)
                ->whereNotNull('fecha_pos')
                ->whereYear('fecha_pos', $month->year)
                ->whereMonth('fecha_pos', $month->month)
                ->count();
        }

        return compact('labels', 'data');
    }

    /**
     * @return array{labels: list<string>, data: list<int>}
     */
    private function cuposResumen(int $unidadId): array
    {
        $asignados = Asignacion::query()
            ->where('estado_asi', 'asignado')
            ->whereHas('cupo.ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
            ->count();

        $disponibles = (int) Cupo::query()
            ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
            ->sum('disponibles_cup');

        $listaEspera = ListaEspera::query()
            ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
            ->count();

        return [
            'labels' => ['Cupos asignados', 'Cupos disponibles', 'Lista de espera'],
            'data' => [$asignados, $disponibles, $listaEspera],
        ];
    }

    /**
     * @return array{labels: list<string>, data: list<int>}
     */
    private function documentosPorEstado(int $unidadId): array
    {
        $rows = Documento::query()
            ->whereHas('postulacion.ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
            ->selectRaw("COALESCE(NULLIF(estado_doc, ''), 'pendiente') as estado, COUNT(*) as total")
            ->groupBy(DB::raw("COALESCE(NULLIF(estado_doc, ''), 'pendiente')"))
            ->orderByDesc('total')
            ->get();

        $labels = [];
        $data = [];

        foreach ($rows as $row) {
            $labels[] = $this->humanize((string) $row->estado);
            $data[] = (int) $row->total;
        }

        return compact('labels', 'data');
    }

    /**
     * @return array{labels: list<string>, data: list<int>}
     */
    private function evaluacionAvance(int $unidadId): array
    {
        $total = $this->postulacionesBase($unidadId)->count();

        $conEvaluacion = Resultado::query()
            ->whereHas('postulacion.ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
            ->whereNotNull('puntaje_total_res')
            ->count();

        $sinEvaluacion = max(0, $total - $conEvaluacion);

        return [
            'labels' => ['Con evaluación', 'Sin evaluación'],
            'data' => [$conEvaluacion, $sinEvaluacion],
        ];
    }

    private function humanize(string $value): string
    {
        return ucfirst(str_replace('_', ' ', $value));
    }
}
