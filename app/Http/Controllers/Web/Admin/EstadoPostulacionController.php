<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Catalogo\StoreEstadoPostulacionRequest;
use App\Http\Requests\Web\Admin\Catalogo\UpdateEstadoPostulacionRequest;
use App\Models\EstadoPostulacion;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;

class EstadoPostulacionController extends Controller
{
    public function index(): View
    {
        $estados = EstadoPostulacion::query()
            ->withCount('postulaciones')
            ->orderBy('nombre_ept')
            ->get();

        return view('admin.catalogos.estados.index', compact('estados'));
    }

    public function create(): View
    {
        return view('admin.catalogos.estados.create');
    }

    public function store(StoreEstadoPostulacionRequest $request): RedirectResponse
    {
        EstadoPostulacion::query()->create($request->validated());

        return redirect()
            ->route('admin.estados-postulacion.index')
            ->with('success', 'Estado creado correctamente.');
    }

    public function edit(EstadoPostulacion $estadoPostulacion): View
    {
        return view('admin.catalogos.estados.edit', ['estado' => $estadoPostulacion]);
    }

    public function update(UpdateEstadoPostulacionRequest $request, EstadoPostulacion $estadoPostulacion): RedirectResponse
    {
        $estadoPostulacion->update($request->validated());

        return redirect()
            ->route('admin.estados-postulacion.index')
            ->with('success', 'Estado actualizado.');
    }

    public function destroy(EstadoPostulacion $estadoPostulacion): RedirectResponse
    {
        if ($estadoPostulacion->postulaciones()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay postulaciones con este estado.');
        }

        try {
            $estadoPostulacion->delete();
        } catch (QueryException) {
            return back()->with('error', 'No se puede eliminar este estado.');
        }

        return redirect()
            ->route('admin.estados-postulacion.index')
            ->with('success', 'Estado eliminado.');
    }
}
