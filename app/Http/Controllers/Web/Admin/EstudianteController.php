<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Estudiante\StoreEstudianteRequest;
use App\Http\Requests\Web\Admin\Estudiante\UpdateEstudianteRequest;
use App\Models\Estudiante;
use App\Services\EstudianteService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    public function __construct(
        private readonly EstudianteService $service,
    ) {}

    public function index(Request $request): View
    {
        $perPage = max(5, min(50, (int) $request->query('per_page', 15)));
        $search  = $request->query('q');

        $query = Estudiante::query()->with('persona')->orderByDesc('id_est');

        if ($search) {
            $query->whereHas('persona', function ($q) use ($search): void {
                $q->where('nombres_per', 'like', "%{$search}%")
                  ->orWhere('ap_paterno_per', 'like', "%{$search}%")
                  ->orWhere('ci_per', 'like', "%{$search}%");
            })->orWhere('codigo_est', 'like', "%{$search}%");
        }

        $estudiantes = $query->paginate($perPage)->withQueryString();

        return view('admin.estudiantes.index', compact('estudiantes', 'search'));
    }

    public function create(): View
    {
        return view('admin.estudiantes.create');
    }

    public function store(StoreEstudianteRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('admin.estudiantes.index')
            ->with('success', 'Estudiante creado correctamente.');
    }

    public function edit(Estudiante $estudiante): View
    {
        $estudiante->load('persona');

        return view('admin.estudiantes.edit', compact('estudiante'));
    }

    public function update(UpdateEstudianteRequest $request, Estudiante $estudiante): RedirectResponse
    {
        $this->service->update($estudiante, $request->validated());

        return redirect()
            ->route('admin.estudiantes.index')
            ->with('success', 'Estudiante actualizado.');
    }

    public function destroy(Estudiante $estudiante): RedirectResponse
    {
        try {
            $this->service->delete($estudiante);
        } catch (QueryException) {
            return redirect()
                ->route('admin.estudiantes.index')
                ->with('error', 'No se puede eliminar: el estudiante tiene datos relacionados.');
        }

        return redirect()
            ->route('admin.estudiantes.index')
            ->with('success', 'Estudiante eliminado.');
    }
}
