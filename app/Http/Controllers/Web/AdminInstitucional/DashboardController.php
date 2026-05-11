<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Models\Cupo;
use App\Models\EstadoPostulacion;
use App\Models\OfertaAcademica;
use App\Models\Postulacion;
use App\Models\Resultado;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends BaseInstitutionalController
{
    public function __invoke(Request $request): View
    {
        $unidadId = $this->unidadId($request);

        $postBase = Postulacion::query()
            ->whereHas('ofertaAcademica', fn ($q) => $q->where('id_ued_oac', $unidadId));

        $totalPostulaciones = (clone $postBase)->count();

        $cuposDisponibles = Cupo::query()
            ->whereHas('ofertaAcademica', fn ($q) => $q->where('id_ued_oac', $unidadId))
            ->sum('disponibles_cup');

        $aprobados = Resultado::query()
            ->whereHas('postulacion.ofertaAcademica', fn ($q) => $q->where('id_ued_oac', $unidadId))
            ->where('clasificacion_res', 'like', '%aprobad%')
            ->count();

        $countsByEstadoId = (clone $postBase)
            ->selectRaw('id_ept_pos, COUNT(*) as total')
            ->groupBy('id_ept_pos')
            ->pluck('total', 'id_ept_pos');

        $estadoNombres = EstadoPostulacion::query()
            ->whereIn('id_ept', $countsByEstadoId->keys())
            ->pluck('nombre_ept', 'id_ept');

        $porEstado = $countsByEstadoId->map(function (int|string $total, int|string $idEpt) use ($estadoNombres): array {
            return [
                'nombre' => (string) ($estadoNombres[(int) $idEpt] ?? '—'),
                'total' => (int) $total,
            ];
        })->values()->sortByDesc('total')->values();

        $recientes = (clone $postBase)
            ->with([
                'estadoPostulacion',
                'estudiante.persona',
                'ofertaAcademica.curso',
                'ofertaAcademica.paralelo',
            ])
            ->orderByDesc('id_pos')
            ->limit(6)
            ->get();

        $stats = [
            'ofertas'        => OfertaAcademica::query()->where('id_ued_oac', $unidadId)->count(),
            'postulaciones'  => $totalPostulaciones,
            'cupos'          => (int) $cuposDisponibles,
            'aprobados'      => $aprobados,
        ];

        return view('admin.institucional.dashboard', compact('stats', 'porEstado', 'recientes'));
    }
}

