<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Http\Requests\Web\AdminInstitucional\StoreOfertaAcademicaRequest;
use App\Http\Requests\Web\AdminInstitucional\UpdateOfertaAcademicaRequest;
use App\Models\Cupo;
use App\Models\OfertaAcademica;
use App\Services\OfertaInstitucionalService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfertaController extends BaseInstitutionalController
{
    public function __construct(
        private readonly OfertaInstitucionalService $service,
    ) {}

    public function index(Request $request): View
    {
        $unidadId = $this->unidadId($request);
        $usuario = $this->webUsuario($request)->load('unidadEducativa');

        $catalogos = $this->service->catalogosAcademicos();
        $gestionActiva = $catalogos['gestiones']->firstWhere('activa_ges', true);

        $ofertas = $this->service
            ->queryParaUnidad($unidadId, $request)
            ->paginate(15)
            ->withQueryString();

        $resumen = $this->service->resumenParaUnidad($unidadId, $request);

        $cursosParaJs = $catalogos['cursos']->map(fn ($c) => [
            'id' => $c->id_cur,
            'nivel_id' => $c->id_niv_cur,
            'nombre' => $c->nombre_cur,
            'nivel' => $c->nivel->nombre_niv ?? '',
        ])->values();

        $paralelosParaJs = $catalogos['paralelos']->map(fn ($p) => [
            'id' => $p->id_par,
            'curso_id' => $p->id_cur_par,
            'nombre' => $p->nombre_par,
            'curso' => $p->curso->nombre_cur ?? '',
        ])->values();

        return view('admin.institucional.ofertas.index', [
            'unidad' => $usuario->unidadEducativa,
            'ofertas' => $ofertas,
            'gestiones' => $catalogos['gestiones'],
            'niveles' => $catalogos['niveles'],
            'cursos' => $catalogos['cursos'],
            'paralelos' => $catalogos['paralelos'],
            'gestionActiva' => $gestionActiva,
            'resumen' => $resumen,
            'cursosParaJs' => $cursosParaJs,
            'paralelosParaJs' => $paralelosParaJs,
        ]);
    }

    public function store(StoreOfertaAcademicaRequest $request): RedirectResponse
    {
        $unidadId = $this->unidadId($request);
        $data = $this->service->normalizarDatosOferta($request->validated());
        $data['id_ued_oac'] = $unidadId;

        $coherencia = $this->service->validarCoherenciaAcademica($data);
        if (! $coherencia['ok']) {
            return back()->with('error', $coherencia['message'])->withInput();
        }

        if ($this->service->existeCombinacionDuplicada($unidadId, $data)) {
            return back()
                ->with('error', 'Ya existe una oferta con la misma gestión, nivel, curso y paralelo en su unidad.')
                ->withInput();
        }

        $total = (int) $request->input('total_cup', 0);
        $disponibles = (int) $request->input('disponibles_cup', $total);

        if ($request->filled('total_cup') || $request->filled('disponibles_cup')) {
            if ($disponibles > $total) {
                return back()->with('error', 'Los cupos disponibles no pueden superar el total.')->withInput();
            }
        }

        DB::transaction(function () use ($data, $request, $total, $disponibles): void {
            $oferta = OfertaAcademica::query()->create(collect($data)->only([
                'id_ges_oac', 'id_ued_oac', 'id_niv_oac', 'id_cur_oac', 'id_par_oac', 'descripcion_oac',
            ])->all());

            if ($request->filled('total_cup') || $request->filled('disponibles_cup')) {
                Cupo::query()->create([
                    'id_oac_cup' => $oferta->id_oac,
                    'total_cup' => $total,
                    'disponibles_cup' => $disponibles,
                ]);
            }
        });

        return redirect()
            ->route('admin.institucional.ofertas.index')
            ->with('success', 'Oferta académica registrada correctamente.');
    }

    public function edit(Request $request, OfertaAcademica $oferta_academica): View
    {
        $unidadId = $this->unidadId($request);
        $this->assertOfertaBelongsToUnidad($oferta_academica, $unidadId);

        $oferta_academica->load(['cupos', 'gestion', 'nivel', 'curso', 'paralelo']);
        $oferta_academica->loadCount('postulaciones');

        $catalogos = $this->service->catalogosAcademicos();
        $gestionActiva = $catalogos['gestiones']->firstWhere('activa_ges', true);

        $cursosParaJs = $catalogos['cursos']->map(fn ($c) => [
            'id' => $c->id_cur,
            'nivel_id' => $c->id_niv_cur,
            'nombre' => $c->nombre_cur,
        ])->values();

        $paralelosParaJs = $catalogos['paralelos']->map(fn ($p) => [
            'id' => $p->id_par,
            'curso_id' => $p->id_cur_par,
            'nombre' => $p->nombre_par,
        ])->values();

        return view('admin.institucional.ofertas.edit', [
            'oferta_academica' => $oferta_academica,
            'gestiones' => $catalogos['gestiones'],
            'niveles' => $catalogos['niveles'],
            'cursos' => $catalogos['cursos'],
            'paralelos' => $catalogos['paralelos'],
            'gestionActiva' => $gestionActiva,
            'cursosParaJs' => $cursosParaJs,
            'paralelosParaJs' => $paralelosParaJs,
        ]);
    }

    public function update(UpdateOfertaAcademicaRequest $request, OfertaAcademica $oferta_academica): RedirectResponse
    {
        $unidadId = $this->unidadId($request);
        $this->assertOfertaBelongsToUnidad($oferta_academica, $unidadId);

        $data = $this->service->normalizarDatosOferta($request->validated());

        $coherencia = $this->service->validarCoherenciaAcademica($data);
        if (! $coherencia['ok']) {
            return back()->with('error', $coherencia['message'])->withInput();
        }

        if ($this->service->existeCombinacionDuplicada($unidadId, $data, $oferta_academica->id_oac)) {
            return back()
                ->with('error', 'Ya existe otra oferta con la misma combinación académica.')
                ->withInput();
        }

        $oferta_academica->update($data);

        return redirect()
            ->route('admin.institucional.ofertas.index')
            ->with('success', 'Oferta actualizada correctamente.');
    }

    public function destroy(Request $request, OfertaAcademica $oferta_academica): RedirectResponse
    {
        $unidadId = $this->unidadId($request);
        $this->assertOfertaBelongsToUnidad($oferta_academica, $unidadId);

        $check = $this->service->puedeEliminar($oferta_academica);
        if (! $check['ok']) {
            return back()->with('error', $check['message']);
        }

        $oferta_academica->delete();

        return redirect()
            ->route('admin.institucional.ofertas.index')
            ->with('success', 'Oferta eliminada.');
    }
}
