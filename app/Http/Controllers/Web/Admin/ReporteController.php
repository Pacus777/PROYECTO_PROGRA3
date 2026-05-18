<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardChartService;
use App\Services\AdminExportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ReporteController extends Controller
{
    public function __construct(
        private readonly AdminExportService $exportService,
        private readonly AdminDashboardChartService $dashboardCharts,
    ) {}

    public function index(): View
    {
        return view('admin.reportes.index', [
            'adminDashboard' => $this->dashboardCharts->build(),
        ]);
    }

    public function exportPostulaciones(Request $request): BinaryFileResponse|Response
    {
        return $this->exportService->exportPostulaciones($request);
    }

    public function exportPostulantes(Request $request): BinaryFileResponse|Response
    {
        return $this->exportService->exportPostulantes($request);
    }

    public function exportResumenUnidades(Request $request): BinaryFileResponse|Response
    {
        return $this->exportService->exportResumenUnidades($request);
    }

    public function exportUnidades(Request $request): BinaryFileResponse|Response
    {
        return $this->exportService->exportUnidades($request);
    }

    public function exportTutores(Request $request): BinaryFileResponse|Response
    {
        return $this->exportService->exportTutores($request);
    }

    public function exportUsuarios(Request $request): BinaryFileResponse|Response
    {
        return $this->exportService->exportUsuarios($request);
    }
}
