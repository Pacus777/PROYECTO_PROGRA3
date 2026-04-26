<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Usuario\StoreUsuarioRequest;
use App\Http\Requests\Web\Admin\Usuario\UpdateUsuarioRequest;
use App\Models\Rol;
use App\Models\UnidadEducativa;
use App\Models\Usuario;
use App\Services\UsuarioService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function __construct(
        private readonly UsuarioService $usuarioService,
    ) {}

    public function index(Request $request): View
    {
        $perPage = max(5, min(50, (int) $request->query('per_page', 15)));

        $usuarios = $this->usuarioService->listPaginated($perPage);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create(): View
    {
        $roles = Rol::query()->orderBy('nombre_rol')->get();
        $unidades = UnidadEducativa::query()->orderBy('nombre_ued')->get();

        return view('admin.usuarios.create', compact('roles', 'unidades'));
    }

    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $this->usuarioService->create($request->validated());

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function show(Usuario $usuario): View
    {
        $usuario->load(['persona', 'rol', 'unidadEducativa']);

        return view('admin.usuarios.show', compact('usuario'));
    }

    public function edit(Usuario $usuario): View
    {
        $usuario->load(['persona', 'rol', 'unidadEducativa']);
        $roles = Rol::query()->orderBy('nombre_rol')->get();
        $unidades = UnidadEducativa::query()->orderBy('nombre_ued')->get();

        return view('admin.usuarios.edit', compact('usuario', 'roles', 'unidades'));
    }

    public function update(UpdateUsuarioRequest $request, Usuario $usuario): RedirectResponse
    {
        $this->usuarioService->update($usuario, $request->validated());

        return redirect()
            ->route('admin.usuarios.show', $usuario)
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, Usuario $usuario): RedirectResponse
    {
        $currentId = (int) $request->session()->get('web_usuario_id');

        if ($currentId === $usuario->id_usu) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $this->usuarioService->delete($usuario);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado.');
    }

    public function toggleActivo(Request $request, Usuario $usuario): RedirectResponse
    {
        $currentId = (int) $request->session()->get('web_usuario_id');

        if ($currentId === $usuario->id_usu && $usuario->activo_usu) {
            return redirect()
                ->back()
                ->with('error', 'No puedes desactivar tu propia sesión.');
        }

        $usuario->update(['activo_usu' => ! $usuario->activo_usu]);

        return redirect()
            ->back()
            ->with('success', $usuario->activo_usu ? 'Usuario activado.' : 'Usuario desactivado.');
    }
}
