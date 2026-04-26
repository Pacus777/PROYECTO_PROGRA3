<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UnidadEducativa\StoreUnidadEducativaRequest;
use App\Http\Requests\Api\UnidadEducativa\UpdateUnidadEducativaRequest;
use App\Models\UnidadEducativa;
use App\Services\UnidadEducativaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnidadEducativaController extends Controller
{
    public function __construct(
        private readonly UnidadEducativaService $service,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);

        return response()->json($this->service->listPaginated($perPage));
    }

    public function store(StoreUnidadEducativaRequest $request): JsonResponse
    {
        $unidad = $this->service->create($request->validated());

        return response()->json([
            'message' => 'Unidad educativa creada.',
            'data' => $unidad,
        ], 201);
    }

    public function show(UnidadEducativa $unidad_educativa): JsonResponse
    {
        return response()->json(['data' => $unidad_educativa]);
    }

    public function update(UpdateUnidadEducativaRequest $request, UnidadEducativa $unidad_educativa): JsonResponse
    {
        $unidad = $this->service->update($unidad_educativa, $request->validated());

        return response()->json([
            'message' => 'Unidad educativa actualizada.',
            'data' => $unidad,
        ]);
    }

    public function destroy(UnidadEducativa $unidad_educativa): JsonResponse
    {
        $this->service->delete($unidad_educativa);

        return response()->json(['message' => 'Unidad educativa eliminada.']);
    }
}
