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
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AcademicController extends BaseInstitutionalController
{
    public function index(Request $request): View
    {
        $this->unidadId($request);

        $niveles = Nivel::query()->orderBy('nombre_niv')->get();
        $cursos = Curso::query()->with('nivel')->orderBy('nombre_cur')->get();
        $paralelos = Paralelo::query()->with('curso.nivel')->orderBy('nombre_par')->get();

        return view('admin.institucional.academic.index', compact('niveles', 'cursos', 'paralelos'));
    }

    public function storeNivel(StoreNivelRequest $request): RedirectResponse
    {
        Nivel::query()->create($request->validated());
        return back()->with('success', 'Nivel creado correctamente.');
    }

    public function updateNivel(UpdateNivelRequest $request, Nivel $nivel): RedirectResponse
    {
        $nivel->update($request->validated());
        return back()->with('success', 'Nivel actualizado.');
    }

    public function destroyNivel(Nivel $nivel): RedirectResponse
    {
        $nivel->delete();
        return back()->with('success', 'Nivel eliminado.');
    }

    public function storeCurso(StoreCursoRequest $request): RedirectResponse
    {
        Curso::query()->create($request->validated());
        return back()->with('success', 'Curso creado correctamente.');
    }

    public function updateCurso(UpdateCursoRequest $request, Curso $curso): RedirectResponse
    {
        $curso->update($request->validated());
        return back()->with('success', 'Curso actualizado.');
    }

    public function destroyCurso(Curso $curso): RedirectResponse
    {
        $curso->delete();
        return back()->with('success', 'Curso eliminado.');
    }

    public function storeParalelo(StoreParaleloRequest $request): RedirectResponse
    {
        Paralelo::query()->create($request->validated());
        return back()->with('success', 'Paralelo creado correctamente.');
    }

    public function updateParalelo(UpdateParaleloRequest $request, Paralelo $paralelo): RedirectResponse
    {
        $paralelo->update($request->validated());
        return back()->with('success', 'Paralelo actualizado.');
    }

    public function destroyParalelo(Paralelo $paralelo): RedirectResponse
    {
        $paralelo->delete();
        return back()->with('success', 'Paralelo eliminado.');
    }
}

