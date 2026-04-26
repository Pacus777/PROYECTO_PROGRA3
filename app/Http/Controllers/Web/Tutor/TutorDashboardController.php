<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Tutor\Concerns\ResolvesTutorContext;
use App\Models\Postulacion;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TutorDashboardController extends Controller
{
    use ResolvesTutorContext;

    public function __invoke(Request $request): View
    {
        $estudianteIds = $this->tutorEstudianteIds($request);
        $tutor = $this->tutorFromRequest($request);

        $totalPostulaciones = Postulacion::query()
            ->whereIn('id_est_pos', $estudianteIds)
            ->count();

        $postulacionesRecientes = Postulacion::query()
            ->whereIn('id_est_pos', $estudianteIds)
            ->with([
                'estadoPostulacion',
                'estudiante.persona',
                'ofertaAcademica.gestion',
                'ofertaAcademica.curso',
                'ofertaAcademica.paralelo',
            ])
            ->orderByDesc('fecha_pos')
            ->orderByDesc('id_pos')
            ->limit(6)
            ->get();

        return view('tutor.dashboard', [
            'tutor' => $tutor,
            'totalPostulaciones' => $totalPostulaciones,
            'postulacionesRecientes' => $postulacionesRecientes,
        ]);
    }
}
