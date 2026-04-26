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
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TutorPostulacionController extends Controller
{
    use ResolvesTutorContext;

    public function __construct(
        private readonly PostulacionService $postulacionService,
    ) {}

    public function index(Request $request): View
    {
        $estudianteIds = $this->tutorEstudianteIds($request);

        $query = Postulacion::query()
            ->whereIn('id_est_pos', $estudianteIds)
            ->with([
                'estadoPostulacion',
                'resultado',
                'ofertaAcademica.curso',
                'ofertaAcademica.paralelo',
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
            ->with(['gestion', 'nivel', 'curso', 'paralelo', 'unidadEducativa'])
            ->orderByDesc('id_oac')
            ->get();

        return view('tutor.postulaciones.create', compact('estudiantes', 'ofertas'));
    }

    public function store(StoreTutorPostulacionRequest $request): RedirectResponse
    {
        $this->postulacionService->create($request->validated());

        return redirect()
            ->route('tutor.postulaciones.index')
            ->with('success', 'Postulación registrada correctamente.');
    }

    public function show(Request $request, Postulacion $postulacion): View
    {
        $this->assertPostulacionBelongsToTutor($request, $postulacion);

        $postulacion->load([
            'estadoPostulacion',
            'ofertaAcademica.gestion',
            'ofertaAcademica.nivel',
            'ofertaAcademica.curso',
            'ofertaAcademica.paralelo',
            'ofertaAcademica.unidadEducativa',
            'estudiante.persona',
            'evaluaciones.criterio',
            'resultado',
            'asignaciones.cupo',
            'listasEspera.ofertaAcademica.curso',
            'documentos.tipoDocumento',
        ]);

        return view('tutor.postulaciones.show', compact('postulacion'));
    }
}
