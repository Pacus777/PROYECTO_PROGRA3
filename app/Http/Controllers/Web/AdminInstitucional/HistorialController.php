<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Services\HistorialInstitucionalService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HistorialController extends BaseInstitutionalController
{
    public function __construct(
        private readonly HistorialInstitucionalService $service,
    ) {}

    public function index(Request $request): View
    {
        $unidadId = $this->unidadId($request);
        $usuario = $this->webUsuario($request)->load('unidadEducativa');

        return view('admin.institucional.historial.index', [
            'unidad' => $usuario->unidadEducativa,
            'eventos' => $this->service->paginarTimeline($unidadId, $request),
            'resumen' => $this->service->resumen($unidadId, $request),
            'modulos' => $this->service->modulosParaFiltro(),
            'acciones' => $this->service->accionesParaFiltro(),
        ]);
    }
}
