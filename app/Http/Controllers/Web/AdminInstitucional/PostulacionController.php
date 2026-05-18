<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Models\Curso;
use App\Models\EstadoPostulacion;
use App\Models\Postulacion;
use App\Services\PostulacionInstitucionalService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PostulacionController extends BaseInstitutionalController
{
    public function __construct(
        private readonly PostulacionInstitucionalService $service,
    ) {}

    public function index(Request $request): View
    {
        $unidadId = $this->unidadId($request);

        $postulaciones = $this->service
            ->queryParaUnidad($unidadId, $request)
            ->paginate(20)
            ->withQueryString();

        $estados = EstadoPostulacion::query()->orderBy('nombre_ept')->get();
        $cursos = Curso::query()->orderBy('nombre_cur')->get();

        return view('admin.institucional.postulaciones.index', compact('postulaciones', 'estados', 'cursos'));
    }

    public function show(Request $request, Postulacion $postulacion): View
    {
        $unidadId = $this->unidadId($request);
        $postulacion->load([
            'estadoPostulacion',
            'ofertaAcademica.gestion',
            'ofertaAcademica.nivel',
            'ofertaAcademica.curso',
            'ofertaAcademica.paralelo',
            'estudiante.persona',
            'evaluaciones.criterio',
            'resultado',
            'asignaciones.cupo',
            'listasEspera',
        ]);
        $this->assertPostulacionBelongsToUnidad($postulacion, $unidadId);

        return view('admin.institucional.postulaciones.show', compact('postulacion'));
    }
}
