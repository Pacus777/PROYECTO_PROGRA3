<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\UnidadEducativa\StoreUnidadEducativaRequest;
use App\Http\Requests\Web\Admin\UnidadEducativa\UpdateUnidadEducativaRequest;
use App\Models\UnidadEducativa;
use App\Services\UnidadEducativaService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UnidadEducativaController extends Controller
{
    public function __construct(
        private readonly UnidadEducativaService $unidadService,
    ) {}

    public function index(Request $request): View
    {
        $perPage = max(5, min(50, (int) $request->query('per_page', 15)));
        $unidades = $this->unidadService->listPaginated($perPage);

        return view('admin.unidades.index', compact('unidades'));
    }

    public function create(): View
    {
        return view('admin.unidades.create');
    }

    public function store(StoreUnidadEducativaRequest $request): RedirectResponse
    {
        $this->unidadService->create($request->validated());

        return redirect()
            ->route('admin.unidades.index')
            ->with('success', 'Unidad educativa creada correctamente.');
    }

    public function show(UnidadEducativa $unidad_educativa): View
    {
        $unidad_educativa->loadCount('usuarios');

        return view('admin.unidades.show', ['unidad' => $unidad_educativa]);
    }

    public function edit(UnidadEducativa $unidad_educativa): View
    {
        return view('admin.unidades.edit', ['unidad' => $unidad_educativa]);
    }

    public function update(UpdateUnidadEducativaRequest $request, UnidadEducativa $unidad_educativa): RedirectResponse
    {
        $this->unidadService->update($unidad_educativa, $request->validated());

        return redirect()
            ->route('admin.unidades.show', $unidad_educativa)
            ->with('success', 'Unidad educativa actualizada.');
    }

    public function destroy(UnidadEducativa $unidad_educativa): RedirectResponse
    {
        try {
            $this->unidadService->delete($unidad_educativa);
        } catch (QueryException) {
            return redirect()
                ->route('admin.unidades.index')
                ->with('error', 'No se puede eliminar la unidad: existen datos relacionados.');
        }

        return redirect()
            ->route('admin.unidades.index')
            ->with('success', 'Unidad educativa eliminada.');
    }
}
