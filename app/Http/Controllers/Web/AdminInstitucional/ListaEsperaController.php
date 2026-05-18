<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Models\ListaEspera;
use App\Services\ListaEsperaInstitucionalService;
use App\Services\OfertaInstitucionalService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ListaEsperaController extends BaseInstitutionalController
{
    public function __construct(
        private readonly ListaEsperaInstitucionalService $service,
        private readonly OfertaInstitucionalService $ofertas,
    ) {}

    public function index(Request $request): View
    {
        $unidadId = $this->unidadId($request);
        $usuario = $this->webUsuario($request)->load('unidadEducativa');
        $filtros = $this->ofertas->catalogosAcademicos();

        $registros = $this->service->queryParaUnidad($unidadId, $request)->paginate(25)->withQueryString();

        return view('admin.institucional.lista-espera.index', [
            'unidad' => $usuario->unidadEducativa,
            'registros' => $registros,
            'resumen' => $this->service->resumen($unidadId),
            'porOferta' => $this->service->resumenPorOferta($unidadId),
            'gestiones' => $filtros['gestiones'],
            'cursos' => $filtros['cursos'],
            'ofertasUnidad' => $this->service->ofertasParaFiltro($unidadId),
            'listaEsperaService' => $this->service,
        ]);
    }

    public function asignarCupo(Request $request, ListaEspera $lista_espera): RedirectResponse
    {
        $unidadId = $this->unidadId($request);

        try {
            $this->service->asignarCupoDesdeEspera($unidadId, $lista_espera);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        $lista_espera->loadMissing('postulacion.estudiante.persona');
        $this->registrarActividad($request, 'lista_espera', (int) $lista_espera->id_les, 'cupo_desde_espera', [
            'descripcion' => 'Cupo asignado desde lista de espera al postulante #'.$lista_espera->id_pos_les,
            'url' => route('admin.institucional.lista-espera.index'),
        ]);

        return back()->with('success', 'Cupo asignado correctamente. Se actualizó la cola de espera.');
    }
}
