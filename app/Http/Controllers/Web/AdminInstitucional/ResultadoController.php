<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Models\Postulacion;
use App\Models\Resultado;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultadoController extends BaseInstitutionalController
{
    public function index(Request $request): View
    {
        $unidadId = $this->unidadId($request);

        // Si aún no hay resultados persistidos, se calculan al vuelo como respaldo.
        $resultados = Resultado::query()
            ->select('resultado.*')
            ->with(['postulacion.estudiante.persona', 'postulacion.ofertaAcademica.curso'])
            ->whereHas('postulacion.ofertaAcademica', fn ($q) => $q->where('id_ued_oac', $unidadId))
            ->orderByDesc('puntaje_total_res')
            ->paginate(20);

        $rankingPreview = Postulacion::query()
            ->select('postulacion.id_pos')
            ->selectRaw('COALESCE(SUM(evaluacion.puntaje_eva * COALESCE(criterio.peso_cri, 1)), 0) as puntaje_calc')
            ->leftJoin('evaluacion', 'evaluacion.id_pos_eva', '=', 'postulacion.id_pos')
            ->leftJoin('criterio', 'criterio.id_cri', '=', 'evaluacion.id_cri_eva')
            ->whereHas('ofertaAcademica', fn ($q) => $q->where('id_ued_oac', $unidadId))
            ->groupBy('postulacion.id_pos')
            ->with(['estudiante.persona', 'ofertaAcademica.curso'])
            ->orderByDesc('puntaje_calc')
            ->limit(50)
            ->get();

        return view('admin.institucional.resultados.index', compact('resultados', 'rankingPreview'));
    }
}

