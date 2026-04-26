<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Http\Requests\Web\AdminInstitucional\StoreOfertaAcademicaRequest;
use App\Http\Requests\Web\AdminInstitucional\UpdateOfertaAcademicaRequest;
use App\Models\Curso;
use App\Models\Cupo;
use App\Models\Gestion;
use App\Models\Nivel;
use App\Models\OfertaAcademica;
use App\Models\Paralelo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OfertaController extends BaseInstitutionalController
{
    public function index(Request $request): View
    {
        $unidadId = $this->unidadId($request);

        $ofertas = OfertaAcademica::query()
            ->with(['gestion', 'nivel', 'curso', 'paralelo', 'cupos'])
            ->where('id_ued_oac', $unidadId)
            ->orderByDesc('id_oac')
            ->paginate(15);

        $gestiones = Gestion::query()->orderByDesc('id_ges')->get();
        $niveles = Nivel::query()->orderBy('nombre_niv')->get();
        $cursos = Curso::query()->with('nivel')->orderBy('nombre_cur')->get();
        $paralelos = Paralelo::query()->with('curso')->orderBy('nombre_par')->get();

        return view('admin.institucional.ofertas.index', compact('ofertas', 'gestiones', 'niveles', 'cursos', 'paralelos'));
    }

    public function store(StoreOfertaAcademicaRequest $request): RedirectResponse
    {
        $unidadId = $this->unidadId($request);
        $data = $request->validated();
        $data['id_ued_oac'] = $unidadId;

        $oferta = OfertaAcademica::query()->create($data);

        if ($request->filled('total_cup') || $request->filled('disponibles_cup')) {
            Cupo::query()->create([
                'id_oac_cup' => $oferta->id_oac,
                'total_cup' => (int) $request->input('total_cup', 0),
                'disponibles_cup' => (int) $request->input('disponibles_cup', 0),
            ]);
        }

        return redirect()->route('admin.institucional.ofertas.index')->with('success', 'Oferta creada correctamente.');
    }

    public function edit(Request $request, OfertaAcademica $oferta_academica): View
    {
        $unidadId = $this->unidadId($request);
        $this->assertOfertaBelongsToUnidad($oferta_academica, $unidadId);

        $oferta_academica->load('cupos');
        $gestiones = Gestion::query()->orderByDesc('id_ges')->get();
        $niveles = Nivel::query()->orderBy('nombre_niv')->get();
        $cursos = Curso::query()->with('nivel')->orderBy('nombre_cur')->get();
        $paralelos = Paralelo::query()->with('curso')->orderBy('nombre_par')->get();

        return view('admin.institucional.ofertas.edit', compact('oferta_academica', 'gestiones', 'niveles', 'cursos', 'paralelos'));
    }

    public function update(UpdateOfertaAcademicaRequest $request, OfertaAcademica $oferta_academica): RedirectResponse
    {
        $unidadId = $this->unidadId($request);
        $this->assertOfertaBelongsToUnidad($oferta_academica, $unidadId);

        $oferta_academica->update($request->validated());

        return redirect()->route('admin.institucional.ofertas.index')->with('success', 'Oferta actualizada.');
    }

    public function destroy(Request $request, OfertaAcademica $oferta_academica): RedirectResponse
    {
        $unidadId = $this->unidadId($request);
        $this->assertOfertaBelongsToUnidad($oferta_academica, $unidadId);
        $oferta_academica->delete();

        return redirect()->route('admin.institucional.ofertas.index')->with('success', 'Oferta eliminada.');
    }
}

