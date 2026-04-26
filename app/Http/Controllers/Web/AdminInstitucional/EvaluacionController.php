<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Http\Requests\Web\AdminInstitucional\StoreCriterioRequest;
use App\Http\Requests\Web\AdminInstitucional\StoreEvaluacionRequest;
use App\Http\Requests\Web\AdminInstitucional\UpdateCriterioRequest;
use App\Http\Requests\Web\AdminInstitucional\UpdateEvaluacionRequest;
use App\Models\Criterio;
use App\Models\Evaluacion;
use App\Models\Postulacion;
use App\Models\TipoCriterio;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EvaluacionController extends BaseInstitutionalController
{
    public function index(Request $request): View
    {
        $this->unidadId($request);

        $criterios = Criterio::query()->with('tipoCriterio')->orderBy('id_cri')->paginate(20);
        $tipos = TipoCriterio::query()->orderBy('nombre_tic')->get();

        return view('admin.institucional.evaluacion.criterios', compact('criterios', 'tipos'));
    }

    public function store(StoreCriterioRequest $request): RedirectResponse
    {
        Criterio::query()->create($request->validated());
        return back()->with('success', 'Criterio creado.');
    }

    public function update(UpdateCriterioRequest $request, Criterio $criterio): RedirectResponse
    {
        $criterio->update($request->validated());
        return back()->with('success', 'Criterio actualizado.');
    }

    public function destroy(Criterio $criterio): RedirectResponse
    {
        $criterio->delete();
        return back()->with('success', 'Criterio eliminado.');
    }

    public function storeEvaluacion(StoreEvaluacionRequest $request, Postulacion $postulacion): RedirectResponse
    {
        $unidadId = $this->unidadId($request);
        $postulacion->loadMissing('ofertaAcademica');
        $this->assertPostulacionBelongsToUnidad($postulacion, $unidadId);

        Evaluacion::query()->updateOrCreate(
            [
                'id_pos_eva' => $postulacion->id_pos,
                'id_cri_eva' => (int) $request->validated('id_cri_eva'),
            ],
            [
                'puntaje_eva' => $request->validated('puntaje_eva'),
                'observaciones_eva' => $request->validated('observaciones_eva'),
            ],
        );

        return back()->with('success', 'Evaluación guardada.');
    }

    public function updateEvaluacion(UpdateEvaluacionRequest $request, Evaluacion $evaluacion): RedirectResponse
    {
        $unidadId = $this->unidadId($request);
        $evaluacion->loadMissing('postulacion.ofertaAcademica');
        $this->assertPostulacionBelongsToUnidad($evaluacion->postulacion, $unidadId);
        $evaluacion->update($request->validated());

        return back()->with('success', 'Evaluación actualizada.');
    }

    public function destroyEvaluacion(Request $request, Evaluacion $evaluacion): RedirectResponse
    {
        $unidadId = $this->unidadId($request);
        $evaluacion->loadMissing('postulacion.ofertaAcademica');
        $this->assertPostulacionBelongsToUnidad($evaluacion->postulacion, $unidadId);
        $evaluacion->delete();

        return back()->with('success', 'Evaluación eliminada.');
    }
}

