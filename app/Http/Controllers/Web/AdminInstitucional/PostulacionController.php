<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Http\Requests\Web\AdminInstitucional\UpdatePostulacionRequest;
use App\Models\Criterio;
use App\Models\EstadoPostulacion;
use App\Models\Postulacion;
use App\Services\PostulacionInstitucionalService;
use App\Services\ProximidadEvaluacionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostulacionController extends BaseInstitutionalController
{
    public function __construct(
        private readonly PostulacionInstitucionalService $service,
        private readonly ProximidadEvaluacionService $proximidad,
    ) {}

    public function index(Request $request): View
    {
        $unidadId = $this->unidadId($request);
        $usuario = $this->webUsuario($request)->load('unidadEducativa');

        $filtros = $this->service->filtrosParaUnidad($unidadId);

        $postulaciones = $this->service
            ->queryParaUnidad($unidadId, $request)
            ->paginate(20)
            ->withQueryString();

        $resumen = $this->service->resumenParaUnidad($unidadId, $request);
        $estados = EstadoPostulacion::query()->orderBy('nombre_ept')->get();

        return view('admin.institucional.postulaciones.index', [
            'postulaciones' => $postulaciones,
            'estados' => $estados,
            'gestiones' => $filtros['gestiones'],
            'cursos' => $filtros['cursos'],
            'resumen' => $resumen,
            'unidad' => $usuario->unidadEducativa,
        ]);
    }

    public function show(Request $request, Postulacion $postulacion): View
    {
        $unidadId = $this->unidadId($request);

        $postulacion->load([
            'estadoPostulacion',
            'ofertaAcademica.gestion',
            'ofertaAcademica.nivel',
            'ofertaAcademica.curso',
            'ofertaAcademica.paralelo',
            'ofertaAcademica.unidadEducativa',
            'estudiante.persona',
            'estudiante.unidadMatriculaActual',
            'evaluaciones.criterio',
            'resultado',
            'asignaciones.cupo',
            'listasEspera',
            'documentos.tipoDocumento',
            'documentos.procesamientoOcr',
        ]);
        $this->assertPostulacionBelongsToUnidad($postulacion, $unidadId);

        $estados = EstadoPostulacion::query()->orderBy('nombre_ept')->get();
        $criterios = Criterio::query()->orderBy('nombre_cri')->get();

        $proximidadPreview = null;
        $est = $postulacion->estudiante;
        $ue = $postulacion->ofertaAcademica?->unidadEducativa;
        if ($est?->lat_est && $est?->lng_est && $ue?->lat_ued && $ue?->lng_ued) {
            $proximidadPreview = $this->proximidad->preview(
                (float) $est->lat_est,
                (float) $est->lng_est,
                (float) $ue->lat_ued,
                (float) $ue->lng_ued,
            );
        }

        return view('admin.institucional.postulaciones.show', compact(
            'postulacion',
            'estados',
            'criterios',
            'proximidadPreview',
        ));
    }

    public function calcularProximidad(Request $request, Postulacion $postulacion): RedirectResponse
    {
        $unidadId = $this->unidadId($request);
        $postulacion->loadMissing('ofertaAcademica');
        $this->assertPostulacionBelongsToUnidad($postulacion, $unidadId);

        $resultado = $this->proximidad->calcularParaPostulacion($postulacion);

        if ($resultado === null || ! ($resultado['ok'] ?? false)) {
            return back()->with('error', $resultado['motivo'] ?? 'No se pudo calcular la proximidad.');
        }

        $this->registrarActividad($request, 'evaluacion', (int) ($resultado['evaluacion_id'] ?? 0), 'evaluacion', [
            'descripcion' => sprintf(
                'Proximidad A*: %.2f km → puntaje %.1f',
                $resultado['distancia_km'] ?? 0,
                $resultado['puntaje'] ?? 0,
            ),
            'url' => route('admin.institucional.postulaciones.show', $postulacion),
        ]);

        return back()->with(
            'success',
            sprintf(
                'Proximidad calculada con A*: %.2f km, puntaje %.1f/100 en criterio Distancia domicilio.',
                $resultado['distancia_km'] ?? 0,
                $resultado['puntaje'] ?? 0,
            ),
        );
    }

    public function update(UpdatePostulacionRequest $request, Postulacion $postulacion): RedirectResponse
    {
        $unidadId = $this->unidadId($request);
        $postulacion->loadMissing('ofertaAcademica');
        $this->assertPostulacionBelongsToUnidad($postulacion, $unidadId);

        $estadoAnterior = $postulacion->estadoPostulacion?->nombre_ept;
        $postulacion->update($request->validated());
        $postulacion->load('estadoPostulacion', 'estudiante.persona');

        $this->registrarActividad($request, 'postulacion', (int) $postulacion->id_pos, 'cambio_estado', [
            'id_pos' => $postulacion->id_pos,
            'descripcion' => 'Estado de postulación: '.($estadoAnterior ?? '—').' → '.($postulacion->estadoPostulacion->nombre_ept ?? '—'),
            'url' => route('admin.institucional.postulaciones.show', $postulacion),
        ]);

        return redirect()
            ->route('admin.institucional.postulaciones.show', $postulacion)
            ->with('success', 'Postulación actualizada correctamente.');
    }
}
