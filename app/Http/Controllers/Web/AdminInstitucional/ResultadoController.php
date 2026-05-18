<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Services\OfertaInstitucionalService;
use App\Services\ResultadoInstitucionalService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ResultadoController extends BaseInstitutionalController
{
    public function __construct(
        private readonly ResultadoInstitucionalService $service,
        private readonly OfertaInstitucionalService $ofertas,
    ) {}

    public function index(Request $request): View
    {
        $unidadId = $this->unidadId($request);
        $usuario = $this->webUsuario($request)->load('unidadEducativa');

        $filas = $this->service->filasRanking($unidadId, $request);
        $ranking = $this->service->paginarRanking($filas, $request);

        $filtros = $this->ofertas->catalogosAcademicos();
        $ofertasUnidad = $this->service->ofertasParaFiltro($unidadId);

        return view('admin.institucional.resultados.index', [
            'unidad' => $usuario->unidadEducativa,
            'ranking' => $ranking,
            'resumen' => $this->service->resumen($unidadId),
            'gestiones' => $filtros['gestiones'],
            'cursos' => $filtros['cursos'],
            'ofertasUnidad' => $ofertasUnidad,
        ]);
    }

    public function sincronizar(Request $request): RedirectResponse
    {
        $unidadId = $this->unidadId($request);
        $total = $this->service->sincronizarResultados($unidadId);

        $this->registrarActividad($request, 'resultado', $unidadId, 'resultado_sync', [
            'descripcion' => "Ranking guardado en resultados: {$total} postulación(es) actualizadas.",
            'url' => route('admin.institucional.resultados.index'),
        ]);

        return back()->with('success', "Ranking guardado en resultados: {$total} postulación(es) actualizadas.");
    }
}
