<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Tutor\Concerns\ResolvesTutorContext;
use App\Models\Postulacion;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TutorResultadoController extends Controller
{
    use ResolvesTutorContext;

    public function index(Request $request): View
    {
        $estudianteIds = $this->tutorEstudianteIds($request);

        $postulaciones = Postulacion::query()
            ->whereIn('id_est_pos', $estudianteIds)
            ->with([
                'estudiante.persona',
                'ofertaAcademica.gestion',
                'ofertaAcademica.curso',
                'ofertaAcademica.paralelo',
                'estadoPostulacion',
                'resultado',
                'asignaciones.cupo.ofertaAcademica',
                'listasEspera.ofertaAcademica',
            ])
            ->orderByDesc('id_pos')
            ->paginate(20)
            ->withQueryString();

        return view('tutor.resultados.index', compact('postulaciones'));
    }
}
