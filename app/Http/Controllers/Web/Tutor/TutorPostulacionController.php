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
use App\Services\PostulacionService;
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

    public function create(Request $request): View
    {
        $estudianteIds = $this->tutorEstudianteIds($request);

        $estudiantes = $estudianteIds === []
            ? collect()
            : Estudiante::query()
                ->whereIn('id_est', $estudianteIds)
                ->with('persona')
                ->orderBy('id_est')
                ->get();

        $ofertas = OfertaAcademica::query()
            ->abiertasParaPostulacion()
            ->with(['gestion', 'nivel', 'curso', 'paralelo', 'unidadEducativa'])
            ->orderByDesc('id_oac')
            ->get();

        if ($ofertas->isEmpty()) {
            return view('tutor.postulaciones.create', compact('estudiantes', 'ofertas'))
                ->with('error', 'No hay ofertas abiertas para postulación en este momento.');
        }

        return view('tutor.postulaciones.create', compact('estudiantes', 'ofertas'));
    }

    public function store(StoreTutorPostulacionRequest $request): RedirectResponse
    {
        $oferta = OfertaAcademica::query()->findOrFail($request->input('id_oac_pos'));

        abort_unless($oferta->estaAbiertaParaPostulacion(), 403, 'La oferta seleccionada no se encuentra dentro del periodo de postulación.');

        $this->postulacionService->create($request->validated());

        return redirect()
            ->route('tutor.postulaciones.index')
            ->with('success', 'Postulación registrada correctamente.');
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
