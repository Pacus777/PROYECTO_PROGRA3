<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Http\Requests\Web\AdminInstitucional\StoreCupoRequest;
use App\Http\Requests\Web\AdminInstitucional\UpdateCupoRequest;
use App\Models\Cupo;
use App\Models\OfertaAcademica;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CupoController extends BaseInstitutionalController
{
    public function store(StoreCupoRequest $request): RedirectResponse
    {
        $unidadId = $this->unidadId($request);
        $oferta = OfertaAcademica::query()->findOrFail((int) $request->validated('id_oac_cup'));
        $this->assertOfertaBelongsToUnidad($oferta, $unidadId);

        Cupo::query()->updateOrCreate(
            ['id_oac_cup' => $oferta->id_oac],
            [
                'total_cup' => (int) $request->validated('total_cup'),
                'disponibles_cup' => (int) $request->validated('disponibles_cup'),
            ],
        );

        return back()->with('success', 'Cupo guardado correctamente.');
    }

    public function update(UpdateCupoRequest $request, Cupo $cupo): RedirectResponse
    {
        $unidadId = $this->unidadId($request);
        $cupo->load('ofertaAcademica');
        $this->assertOfertaBelongsToUnidad($cupo->ofertaAcademica, $unidadId);
        $cupo->update($request->validated());

        return back()->with('success', 'Cupo actualizado.');
    }
}

