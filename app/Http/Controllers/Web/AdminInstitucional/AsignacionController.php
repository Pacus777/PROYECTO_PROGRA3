<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Services\ResultadoInstitucionalService;
use App\Services\TutorCupoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AsignacionController extends BaseInstitutionalController
{
    public function __construct(
        private readonly ResultadoInstitucionalService $service,
        private readonly TutorCupoService $tutorCupoService,
    ) {}

    public function index(Request $request): View
    {
        $unidadId = $this->unidadId($request);

        $this->tutorCupoService->procesarVencimientosPendientesPorUnidad($unidadId);

        $usuario = $this->webUsuario($request)->load('unidadEducativa');

        return view('admin.institucional.asignacion.index', [
            'unidad' => $usuario->unidadEducativa,
            'resumen' => $this->service->resumen($unidadId),
            'asignaciones' => $this->service->queryAsignacionesUnidad($unidadId, $request)->paginate(20)->withQueryString(),
            'ofertasUnidad' => $this->service->ofertasParaFiltro($unidadId),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $unidadId = $this->unidadId($request);

        $resumen = $this->service->resumen($unidadId);
        if ($resumen['ofertas_con_cupo'] === 0) {
            return back()->with('error', 'No hay ofertas con cupos definidos. Configure cupos en Ofertas académicas primero.');
        }

        if ($resumen['con_evaluacion'] === 0) {
            return back()->with('error', 'No hay evaluaciones registradas. Puntúe postulantes en Postulaciones o Criterios antes de asignar.');
        }

        $stats = $this->service->ejecutarAsignacion($unidadId);

        if ($stats['ofertas_procesadas'] === 0) {
            return back()->with('error', 'Ninguna oferta tenía cupos configurados para procesar.');
        }

        $this->registrarActividad($request, 'asignacion', $unidadId, 'asignacion_masiva', [
            'descripcion' => "Asignación masiva: {$stats['asignados']} cupo(s) asignados, {$stats['lista_espera']} en lista de espera.",
            'url' => route('admin.institucional.asignacion.index'),
        ]);

        return redirect()
            ->route('admin.institucional.asignacion.index')
            ->with(
                'success',
                "Asignación completada: {$stats['ofertas_procesadas']} oferta(s), "
                ."{$stats['asignados']} cupo(s) asignados, {$stats['lista_espera']} en lista de espera.",
            );
    }
}
