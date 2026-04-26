<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Usuario\StoreUsuarioRequest;
use App\Http\Requests\Api\Usuario\UpdateUsuarioRequest;
use App\Models\Usuario;
use App\Services\UsuarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function __construct(
        private readonly UsuarioService $service,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);

        return response()->json($this->service->listPaginated($perPage));
    }

    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        $usuario = $this->service->create($request->validated());

        return response()->json([
            'message' => 'Usuario creado.',
            'data' => $usuario->load(['persona', 'rol', 'unidadEducativa']),
        ], 201);
    }

    public function show(Usuario $usuario): JsonResponse
    {
        return response()->json([
            'data' => $usuario->load(['persona', 'rol', 'unidadEducativa']),
        ]);
    }

    public function update(UpdateUsuarioRequest $request, Usuario $usuario): JsonResponse
    {
        $usuario = $this->service->update($usuario, $request->validated());

        return response()->json([
            'message' => 'Usuario actualizado.',
            'data' => $usuario,
        ]);
    }

    public function destroy(Usuario $usuario): JsonResponse
    {
        $this->service->delete($usuario);

        return response()->json(['message' => 'Usuario eliminado.']);
    }
}
