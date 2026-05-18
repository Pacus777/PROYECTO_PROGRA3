<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Services\InstitucionalExportService;
use App\Services\InstitucionalReporteService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ReporteController extends BaseInstitutionalController
{
    public function __construct(
        private readonly InstitucionalExportService $exportService,
        private readonly InstitucionalReporteService $reportes,
    ) {}

    public function index(Request $request): View
    {
        $unidadId = $this->unidadId($request);
        $usuario = $this->webUsuario($request)->load('unidadEducativa');

        return view('admin.institucional.reportes.index', [
            'unidad' => $usuario->unidadEducativa,
            'indicadores' => $this->reportes->indicadores($unidadId),
        ]);
    }

    public function exportPostulaciones(Request $request): BinaryFileResponse|Response
    {
        return $this->exportService->exportPostulaciones($this->unidadId($request), $request);
    }

    public function exportOfertas(Request $request): BinaryFileResponse|Response
    {
        return $this->exportService->exportOfertas($this->unidadId($request), $request);
    }

    public function exportResultados(Request $request): BinaryFileResponse|Response
    {
        return $this->exportService->exportResultados($this->unidadId($request), $request);
    }

    public function exportListaEspera(Request $request): BinaryFileResponse|Response
    {
        return $this->exportService->exportListaEspera($this->unidadId($request), $request);
    }

    public function exportHistorial(Request $request): BinaryFileResponse|Response
    {
        return $this->exportService->exportHistorial($this->unidadId($request), $request);
    }

    public function exportDocumentos(Request $request): BinaryFileResponse|Response
    {
        return $this->exportService->exportDocumentos($this->unidadId($request), $request);
    }

    public function exportAsignaciones(Request $request): BinaryFileResponse|Response
    {
        return $this->exportService->exportAsignaciones($this->unidadId($request), $request);
    }

    public function exportResumenAdmision(Request $request): BinaryFileResponse|Response
    {
        return $this->exportService->exportResumenAdmision($this->unidadId($request), $request);
    }
}
