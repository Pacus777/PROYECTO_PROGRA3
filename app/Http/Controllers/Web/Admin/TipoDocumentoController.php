<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Catalogo\StoreTipoDocumentoRequest;
use App\Http\Requests\Web\Admin\Catalogo\UpdateTipoDocumentoRequest;
use App\Models\TipoDocumento;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;

class TipoDocumentoController extends Controller
{
    public function index(): View
    {
        $tipos = TipoDocumento::query()
            ->withCount('documentos')
            ->orderBy('nombre_tdo')
            ->get();

        return view('admin.catalogos.tipos-documento.index', compact('tipos'));
    }

    public function create(): View
    {
        return view('admin.catalogos.tipos-documento.create');
    }

    public function store(StoreTipoDocumentoRequest $request): RedirectResponse
    {
        TipoDocumento::query()->create($request->validated());

        return redirect()
            ->route('admin.tipos-documento.index')
            ->with('success', 'Tipo de documento creado.');
    }

    public function edit(TipoDocumento $tipoDocumento): View
    {
        return view('admin.catalogos.tipos-documento.edit', ['tipo' => $tipoDocumento]);
    }

    public function update(UpdateTipoDocumentoRequest $request, TipoDocumento $tipoDocumento): RedirectResponse
    {
        $tipoDocumento->update($request->validated());

        return redirect()
            ->route('admin.tipos-documento.index')
            ->with('success', 'Tipo de documento actualizado.');
    }

    public function destroy(TipoDocumento $tipoDocumento): RedirectResponse
    {
        if ($tipoDocumento->documentos()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay documentos de este tipo.');
        }

        try {
            $tipoDocumento->delete();
        } catch (QueryException) {
            return back()->with('error', 'No se puede eliminar este tipo.');
        }

        return redirect()
            ->route('admin.tipos-documento.index')
            ->with('success', 'Tipo eliminado.');
    }
}
