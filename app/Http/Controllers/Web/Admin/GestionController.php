<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Gestion\StoreGestionRequest;
use App\Http\Requests\Web\Admin\Gestion\UpdateGestionRequest;
use App\Models\Gestion;
use App\Services\GestionService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GestionController extends Controller
{
    public function __construct(
        private readonly GestionService $service,
    ) {}

    public function index(Request $request): View
    {
        $perPage = max(5, min(50, (int) $request->query('per_page', 15)));
        $gestiones = $this->service->listPaginated($perPage);

        return view('admin.gestiones.index', compact('gestiones'));
    }

    public function create(): View
    {
        return view('admin.gestiones.create');
    }

    public function store(StoreGestionRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('admin.gestiones.index')
            ->with('success', 'Gestión creada correctamente.');
    }

    public function edit(Gestion $gestion): View
    {
        return view('admin.gestiones.edit', compact('gestion'));
    }

    public function update(UpdateGestionRequest $request, Gestion $gestion): RedirectResponse
    {
        $this->service->update($gestion, $request->validated());

        return redirect()
            ->route('admin.gestiones.index')
            ->with('success', 'Gestión actualizada.');
    }

    public function destroy(Gestion $gestion): RedirectResponse
    {
        try {
            $this->service->delete($gestion);
        } catch (QueryException) {
            return redirect()
                ->route('admin.gestiones.index')
                ->with('error', 'No se puede eliminar la gestión: existen datos relacionados.');
        }

        return redirect()
            ->route('admin.gestiones.index')
            ->with('success', 'Gestión eliminada.');
    }
}
