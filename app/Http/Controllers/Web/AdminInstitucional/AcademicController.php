<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Http\Requests\Web\AdminInstitucional\StoreCursoRequest;
use App\Http\Requests\Web\AdminInstitucional\StoreNivelRequest;
use App\Http\Requests\Web\AdminInstitucional\StoreParaleloRequest;
use App\Http\Requests\Web\AdminInstitucional\UpdateCursoRequest;
use App\Http\Requests\Web\AdminInstitucional\UpdateNivelRequest;
use App\Http\Requests\Web\AdminInstitucional\UpdateParaleloRequest;
use App\Models\Curso;
use App\Models\Nivel;
use App\Models\Paralelo;
use App\Services\AcademicInstitucionalService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AcademicController extends BaseInstitutionalController
{
    public function __construct(
        private readonly AcademicInstitucionalService $service,
    ) {}

    public function index(Request $request): View
    {
        $unidadId = $this->unidadId($request);
        $usuario = $this->webUsuario($request)->load('unidadEducativa');

        $datos = $this->service->datosParaUnidad($unidadId);
        $arbolCatalogo = $this->service->arbolCatalogo($unidadId);
        $arbolOfertas = $this->service->arbolOfertasUnidad($unidadId);

        return view('admin.institucional.academic.index', [
            'unidad' => $usuario->unidadEducativa,
            'niveles' => $datos['niveles'],
            'cursos' => $datos['cursos'],
            'paralelos' => $datos['paralelos'],
            'resumen' => $datos['resumen'],
            'arbolCatalogo' => $arbolCatalogo,
            'arbolOfertas' => $arbolOfertas,
        ]);
    }

    public function storeNivel(StoreNivelRequest $request): RedirectResponse
    {
        $this->unidadId($request);
        Nivel::query()->create($request->validated());

        return back()->with('success', 'Nivel registrado en el catálogo académico.');
    }

    public function updateNivel(UpdateNivelRequest $request, Nivel $nivel): RedirectResponse
    {
        $this->unidadId($request);
        $nivel->update($request->validated());

        return back()->with('success', 'Nivel actualizado.');
    }

    public function destroyNivel(Request $request, Nivel $nivel): RedirectResponse
    {
        $unidadId = $this->unidadId($request);
        $check = $this->service->puedeEliminarNivel($nivel, $unidadId);

        if (! $check['ok']) {
            return back()->with('error', $check['message']);
        }

        $nivel->delete();

        return back()->with('success', 'Nivel eliminado.');
    }

    public function storeCurso(StoreCursoRequest $request): RedirectResponse
    {
        $this->unidadId($request);
        Curso::query()->create($request->validated());

        return back()->with('success', 'Curso registrado.');
    }

    public function updateCurso(UpdateCursoRequest $request, Curso $curso): RedirectResponse
    {
        $unidadId = $this->unidadId($request);
        $nuevoNivelId = (int) $request->validated('id_niv_cur');

        $check = $this->service->puedeCambiarNivelDeCurso($curso, $nuevoNivelId);
        if (! $check['ok']) {
            return back()->with('error', $check['message']);
        }

        $curso->update($request->validated());

        return back()->with('success', 'Curso actualizado.');
    }

    public function destroyCurso(Request $request, Curso $curso): RedirectResponse
    {
        $unidadId = $this->unidadId($request);
        $check = $this->service->puedeEliminarCurso($curso, $unidadId);

        if (! $check['ok']) {
            return back()->with('error', $check['message']);
        }

        $curso->delete();

        return back()->with('success', 'Curso eliminado.');
    }

    public function storeParalelo(StoreParaleloRequest $request): RedirectResponse
    {
        $this->unidadId($request);
        Paralelo::query()->create($request->validated());

        return back()->with('success', 'Paralelo registrado.');
    }

    public function updateParalelo(UpdateParaleloRequest $request, Paralelo $paralelo): RedirectResponse
    {
        $this->unidadId($request);
        $nuevoCursoId = (int) $request->validated('id_cur_par');

        $check = $this->service->puedeCambiarCursoDeParalelo($paralelo, $nuevoCursoId);
        if (! $check['ok']) {
            return back()->with('error', $check['message']);
        }

        $paralelo->update($request->validated());

        return back()->with('success', 'Paralelo actualizado.');
    }

    public function destroyParalelo(Request $request, Paralelo $paralelo): RedirectResponse
    {
        $unidadId = $this->unidadId($request);
        $check = $this->service->puedeEliminarParalelo($paralelo, $unidadId);

        if (! $check['ok']) {
            return back()->with('error', $check['message']);
        }

        $paralelo->delete();

        return back()->with('success', 'Paralelo eliminado.');
    }
}
