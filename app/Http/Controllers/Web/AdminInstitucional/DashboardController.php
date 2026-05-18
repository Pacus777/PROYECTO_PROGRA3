<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Models\Postulacion;
use App\Services\InstitucionalDashboardChartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends BaseInstitutionalController
{
    public function __construct(
        private readonly InstitucionalDashboardChartService $dashboardCharts,
    ) {}

    public function __invoke(Request $request): View
    {
        $unidadId = $this->unidadId($request);
        $institucionalDashboard = $this->dashboardCharts->build($unidadId);

        $porEstado = $this->mapPorEstado($institucionalDashboard['charts']['postulaciones_estado'] ?? []);

        $recientes = Postulacion::query()
            ->whereHas('ofertaAcademica', fn ($q) => $q->where('id_ued_oac', $unidadId))
            ->with([
                'estadoPostulacion',
                'estudiante.persona',
                'ofertaAcademica.curso',
                'ofertaAcademica.paralelo',
            ])
            ->orderByDesc('id_pos')
            ->limit(6)
            ->get();

        $kpis = $institucionalDashboard['kpis'];

        $stats = [
            'ofertas' => $kpis['ofertas'],
            'postulaciones' => $kpis['postulaciones'],
            'cupos' => $kpis['cupos_disponibles'],
            'aprobados' => $kpis['cupos_asignados'],
        ];

        return view('admin.institucional.dashboard', compact(
            'stats',
            'porEstado',
            'recientes',
            'institucionalDashboard',
        ));
    }

    /**
     * @param array{labels?: list<string>, data?: list<int>} $chart
     *
     * @return Collection<int, array{nombre: string, total: int}>
     */
    private function mapPorEstado(array $chart): Collection
    {
        $labels = $chart['labels'] ?? [];
        $data = $chart['data'] ?? [];

        return collect($labels)
            ->zip($data)
            ->map(fn ($pair): array => [
                'nombre' => (string) ($pair[0] ?? '—'),
                'total' => (int) ($pair[1] ?? 0),
            ])
            ->sortByDesc('total')
            ->values();
    }
}
