<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Tutor\Concerns\ResolvesTutorContext;
use App\Http\Requests\Web\Tutor\StoreTutorPostulacionRequest;
use App\Models\EstadoPostulacion;
use App\Models\Estudiante;
use App\Models\OfertaAcademica;
use App\Models\Postulacion;
use App\Models\UnidadEducativa;
use App\Services\PostulacionService;
use App\Services\ProximidadEvaluacionService;
use App\Services\TutorCupoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TutorPostulacionController extends Controller
{
    use ResolvesTutorContext;

    public function __construct(
        private readonly PostulacionService $postulacionService,
        private readonly TutorCupoService $tutorCupoService,
        private readonly ProximidadEvaluacionService $proximidad,
    ) {}

    public function index(Request $request): View
    {
        $estudianteIds = $this->tutorEstudianteIds($request);

        $query = Postulacion::query()
            ->whereIn('id_est_pos', $estudianteIds)
            ->with([
                'estadoPostulacion',
                'resultado',
                'asignaciones',
                'listasEspera',
                'documentos',
                'ofertaAcademica.curso',
                'ofertaAcademica.paralelo',
                'ofertaAcademica.tiposDocumentoRequeridos',
                'estudiante.persona',
            ]);

        if ($request->filled('id_ept_pos')) {
            $query->where('id_ept_pos', (int) $request->input('id_ept_pos'));
        }

        $postulaciones = $query->orderByDesc('fecha_pos')->orderByDesc('id_pos')->paginate(15)->withQueryString();
        $estados = EstadoPostulacion::query()->orderBy('nombre_ept')->get();

        return view('tutor.postulaciones.index', compact('postulaciones', 'estados'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $estudianteIds = $this->tutorEstudianteIds($request);

        $estudiantes = $estudianteIds === []
            ? collect()
            : Estudiante::query()
                ->whereIn('id_est', $estudianteIds)
                ->with('persona')
                ->orderBy('id_est')
                ->get();

        $ofertasQuery = OfertaAcademica::query()
            ->abiertasParaPostulacion()
            ->with(['gestion', 'nivel', 'curso', 'paralelo', 'unidadEducativa']);

        $unidadSeleccionada = null;
        if ($request->filled('colegio')) {
            $unidadSeleccionada = UnidadEducativa::query()
                ->where('codigo_ued', (string) $request->query('colegio'))
                ->first();

            if ($unidadSeleccionada !== null) {
                $ofertasQuery->where('id_ued_oac', $unidadSeleccionada->id_ued);
            }
        }

        $ofertas = $ofertasQuery->orderByDesc('id_oac')->get();
        $ofertaPreseleccionada = $request->integer('oac') ?: null;

        $estudiantesSinDomicilio = $estudiantes
            ->filter(fn (Estudiante $e) => ! $e->tieneDomicilioRegistrado())
            ->values();

        if ($estudiantesSinDomicilio->isNotEmpty()) {
            $est = $estudiantesSinDomicilio->first();

            return redirect()
                ->route('tutor.estudiantes.domicilio.edit', [
                    'estudiante' => $est,
                    'return' => route('tutor.postulaciones.create', $request->query()),
                ])
                ->with('warning', 'Antes de postular, indique en el mapa dónde vive el estudiante.');
        }

        if ($ofertas->isEmpty()) {
            return view('tutor.postulaciones.create', compact(
                'estudiantes', 'ofertas', 'unidadSeleccionada', 'ofertaPreseleccionada', 'estudiantesSinDomicilio',
            ))
                ->with('error', $unidadSeleccionada
                    ? 'No hay ofertas abiertas en '.$unidadSeleccionada->nombre_ued.' en este momento.'
                    : 'No hay ofertas abiertas para postulación en este momento.');
        }

        return view('tutor.postulaciones.create', compact(
            'estudiantes', 'ofertas', 'unidadSeleccionada', 'ofertaPreseleccionada', 'estudiantesSinDomicilio',
        ));
    }

    public function store(StoreTutorPostulacionRequest $request): RedirectResponse
    {
        $oferta = OfertaAcademica::query()->findOrFail($request->input('id_oac_pos'));

        abort_unless($oferta->estaAbiertaParaPostulacion(), 403, 'La oferta seleccionada no se encuentra dentro del periodo de postulación.');

        $postulacion = $this->postulacionService->create($request->validated());

        $proximidad = $this->proximidad->calcularParaPostulacion($postulacion);

        $mensaje = 'Postulación registrada correctamente.';
        if ($proximidad !== null && ($proximidad['ok'] ?? false)) {
            $mensaje .= sprintf(
                ' Proximidad al colegio: %.2f km (puntaje geográfico %.1f).',
                $proximidad['distancia_km'] ?? 0,
                $proximidad['puntaje'] ?? 0,
            );
        } elseif ($proximidad !== null && isset($proximidad['motivo'])) {
            $mensaje .= ' Proximidad: '.$proximidad['motivo'];
        }

        return redirect()
            ->route('tutor.postulaciones.index')
            ->with('success', $mensaje);
    }

    public function show(Request $request, Postulacion $postulacion): View
    {
        $this->assertPostulacionBelongsToTutor($request, $postulacion);

        $this->tutorCupoService->procesarVencimientoSiCorresponde($postulacion);
        $postulacion->refresh();

        $postulacion->load([
            'estadoPostulacion',
            'ofertaAcademica.gestion',
            'ofertaAcademica.nivel',
            'ofertaAcademica.curso',
            'ofertaAcademica.paralelo',
            'ofertaAcademica.unidadEducativa',
            'ofertaAcademica.tiposDocumentoRequeridos',
            'estudiante.persona',
            'evaluaciones.criterio',
            'resultado',
            'asignaciones.cupo',
            'listasEspera.ofertaAcademica.curso',
            'documentos.tipoDocumento',
        ]);

        return view('tutor.postulaciones.show', compact('postulacion'));
    }

    public function responderCupo(Request $request, Postulacion $postulacion): RedirectResponse
    {
        $this->assertPostulacionBelongsToTutor($request, $postulacion);

        $validated = $request->validate([
            'accion' => ['required', 'in:aceptar,rechazar'],
        ]);

        try {
            $resultado = $this->tutorCupoService->responderCupo($postulacion, $validated['accion']);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($resultado['accion'] === 'vencido') {
            $mensaje = $resultado['promovido']
                ? 'El plazo para responder el cupo venció. El cupo fue liberado y se promovió al siguiente postulante de la lista de espera.'
                : 'El plazo para responder el cupo venció. El cupo fue liberado.';
        } elseif ($validated['accion'] === 'aceptar') {
            $mensaje = 'Cupo aceptado correctamente.';
        } elseif ($resultado['promovido']) {
            $mensaje = 'Cupo rechazado correctamente. El cupo fue liberado y se promovió al siguiente postulante de la lista de espera.';
        } else {
            $mensaje = 'Cupo rechazado correctamente. El cupo fue liberado y no había postulantes en lista de espera.';
        }

        return redirect()
            ->route('tutor.postulaciones.show', $postulacion)
            ->with('success', $mensaje);
    }
}
