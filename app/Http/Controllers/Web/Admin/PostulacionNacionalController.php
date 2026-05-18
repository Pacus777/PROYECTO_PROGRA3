<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Models\EstadoPostulacion;
use App\Models\Gestion;
use App\Models\Postulacion;
use App\Services\PostulacionNacionalService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PostulacionNacionalController extends Controller
{
    public function __construct(
        private readonly PostulacionNacionalService $service,
    ) {}

    public function index(Request $request): View
    {
        $postulaciones = $this->service->paginar($request);
        $estados = EstadoPostulacion::query()->orderBy('nombre_ept')->get();
        $gestiones = Gestion::query()->orderByDesc('id_ges')->get();
        $departamentos = Departamento::query()->orderBy('nombre_dep')->get();

        return view('admin.postulaciones.index', compact(
            'postulaciones',
            'estados',
            'gestiones',
            'departamentos',
        ));
    }

    public function show(Postulacion $postulacion): View
    {
        $postulacion->load([
            'estadoPostulacion',
            'ofertaAcademica.unidadEducativa.municipio.provincia.departamento',
            'ofertaAcademica.unidadEducativa.distritoEducativo',
            'ofertaAcademica.gestion',
            'ofertaAcademica.nivel',
            'ofertaAcademica.curso',
            'ofertaAcademica.paralelo',
            'estudiante.persona',
            'estudiante.unidadMatriculaActual',
            'evaluaciones.criterio',
            'resultado',
            'documentos.tipoDocumento',
        ]);

        return view('admin.postulaciones.show', compact('postulacion'));
    }
}
