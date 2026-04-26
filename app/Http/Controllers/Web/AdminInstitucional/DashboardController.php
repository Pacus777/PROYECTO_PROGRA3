<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Models\OfertaAcademica;
use App\Models\Postulacion;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends BaseInstitutionalController
{
    public function __invoke(Request $request): View
    {
        $unidadId = $this->unidadId($request);

        $stats = [
            'ofertas' => OfertaAcademica::query()->where('id_ued_oac', $unidadId)->count(),
            'postulaciones' => Postulacion::query()
                ->whereHas('ofertaAcademica', fn ($q) => $q->where('id_ued_oac', $unidadId))
                ->count(),
        ];

        return view('admin.institucional.dashboard', compact('stats'));
    }
}

