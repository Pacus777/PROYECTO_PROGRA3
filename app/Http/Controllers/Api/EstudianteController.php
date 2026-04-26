<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Estudiante\StoreEstudianteRequest;
use App\Http\Requests\Api\Estudiante\UpdateEstudianteRequest;
use App\Models\Estudiante;
use App\Services\EstudianteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    public function __construct(
        private readonly EstudianteService $service,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);

        return response()->json($this->service->listPaginated($perPage));
    }

    public function store(StoreEstudianteRequest $request): JsonResponse
    {
        $estudiante = $this->service->create($request->validated());

        return response()->json([
            'message' => 'Estudiante creado.',
            'data' => $estudiante->load('persona'),
        ], 201);
    }

    public function show(Estudiante $estudiante): JsonResponse
    {
        return response()->json([
            'data' => $estudiante->load('persona'),
        ]);
    }

    public function update(UpdateEstudianteRequest $request, Estudiante $estudiante): JsonResponse
    {
        $estudiante = $this->service->update($estudiante, $request->validated());

        return response()->json([
            'message' => 'Estudiante actualizado.',
            'data' => $estudiante,
        ]);
    }

    public function destroy(Estudiante $estudiante): JsonResponse
    {
        $this->service->delete($estudiante);

        return response()->json(['message' => 'Estudiante eliminado.']);
    }
}
