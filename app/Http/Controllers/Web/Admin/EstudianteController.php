<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Estudiante\StoreEstudianteRequest;
use App\Http\Requests\Web\Admin\Estudiante\UpdateEstudianteRequest;
use App\Models\Estudiante;
use App\Models\Postulacion;
use App\Models\UnidadEducativa;
use App\Services\EstudianteQueryService;
use App\Services\EstudianteService;
use App\Services\TutorVinculoService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    public function __construct(
        private readonly EstudianteService $service,
        private readonly TutorVinculoService $vinculoService,
        private readonly EstudianteQueryService $queryService,
    ) {}

    public function index(Request $request): View
    {
        $perPage = max(5, min(50, (int) $request->query('per_page', 15)));
        $search = $request->query('q');
        $incidencia = $request->query('incidencia');

        $estudiantes = $this->queryService
            ->queryFiltrada($request)
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.estudiantes.index', compact('estudiantes', 'search', 'incidencia'));
    }

    public function create(): View
    {
        $unidades = UnidadEducativa::query()->orderBy('nombre_ued')->get();

        return view('admin.estudiantes.create', compact('unidades'));
    }

    public function store(StoreEstudianteRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('admin.estudiantes.index')
            ->with('success', 'Estudiante registrado correctamente.');
    }

    public function edit(Estudiante $estudiante): View
    {
        $estudiante->load(['persona', 'unidadMatriculaActual', 'tutores.persona']);
        $unidades = UnidadEducativa::query()->orderBy('nombre_ued')->get();
        $tutoresDisponibles = $this->vinculoService->tutoresParaVincular();

        $postulaciones = Postulacion::query()
            ->with(['estadoPostulacion', 'ofertaAcademica.unidadEducativa', 'ofertaAcademica.gestion', 'ofertaAcademica.curso'])
            ->where('id_est_pos', $estudiante->id_est)
            ->orderByDesc('fecha_pos')
            ->get();

        return view('admin.estudiantes.edit', compact(
            'estudiante',
            'unidades',
            'tutoresDisponibles',
            'postulaciones',
        ));
    }

    public function update(UpdateEstudianteRequest $request, Estudiante $estudiante): RedirectResponse
    {
        $this->service->update($estudiante, $request->validated());

        return redirect()
            ->route('admin.estudiantes.edit', $estudiante)
            ->with('success', 'Datos del estudiante actualizados.');
    }

    public function destroy(Estudiante $estudiante): RedirectResponse
    {
        try {
            $this->service->delete($estudiante);
        } catch (QueryException) {
            return redirect()
                ->route('admin.estudiantes.index')
                ->with('error', 'No se puede eliminar: existen postulaciones u otros datos vinculados.');
        }

        return redirect()
            ->route('admin.estudiantes.index')
            ->with('success', 'Estudiante eliminado.');
    }
}
