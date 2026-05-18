<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Services\InstitucionalExportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ReporteController extends BaseInstitutionalController
{
    public function __construct(
        private readonly InstitucionalExportService $exportService,
    ) {}

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

    public function exportDocumentos(Request $request): BinaryFileResponse|Response
    {
        return $this->exportService->exportDocumentos($this->unidadId($request), $request);
    }
}
