<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Tutor\Concerns\ResolvesTutorContext;
use App\Models\Postulacion;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TutorSeguimientoController extends Controller
{
    use ResolvesTutorContext;

    public function index(Request $request): View
    {
        $estudianteIds = $this->tutorEstudianteIds($request);

        $postulaciones = Postulacion::query()
            ->whereIn('id_est_pos', $estudianteIds)
            ->with([
                'estadoPostulacion',
                'estudiante.persona',
                'ofertaAcademica.curso',
                'ofertaAcademica.paralelo',
                'resultado',
                'listasEspera',
            ])
            ->orderByDesc('fecha_pos')
            ->orderByDesc('id_pos')
            ->get();

        return view('tutor.seguimiento.index', compact('postulaciones'));
    }
}
