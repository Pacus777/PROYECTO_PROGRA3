<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Gestion\StoreGestionRequest;
use App\Http\Requests\Api\Gestion\UpdateGestionRequest;
use App\Models\Gestion;
use App\Services\GestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GestionController extends Controller
{
    public function __construct(
        private readonly GestionService $service,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);

        return response()->json($this->service->listPaginated($perPage));
    }

    public function store(StoreGestionRequest $request): JsonResponse
    {
        $gestion = $this->service->create($request->validated());

        return response()->json([
            'message' => 'Gestión creada.',
            'data' => $gestion,
        ], 201);
    }

    public function show(Gestion $gestion): JsonResponse
    {
        return response()->json(['data' => $gestion]);
    }

    public function update(UpdateGestionRequest $request, Gestion $gestion): JsonResponse
    {
        $gestion = $this->service->update($gestion, $request->validated());

        return response()->json([
            'message' => 'Gestión actualizada.',
            'data' => $gestion,
        ]);
    }

    public function destroy(Gestion $gestion): JsonResponse
    {
        $this->service->delete($gestion);

        return response()->json(['message' => 'Gestión eliminada.']);
    }
}
