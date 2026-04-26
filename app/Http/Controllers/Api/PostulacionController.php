<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Postulacion\StorePostulacionRequest;
use App\Http\Requests\Api\Postulacion\UpdatePostulacionRequest;
use App\Models\Postulacion;
use App\Services\PostulacionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostulacionController extends Controller
{
    public function __construct(
        private readonly PostulacionService $service,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);

        return response()->json($this->service->listPaginated($perPage));
    }

    public function store(StorePostulacionRequest $request): JsonResponse
    {
        $postulacion = $this->service->create($request->validated());

        return response()->json([
            'message' => 'Postulación creada.',
            'data' => $postulacion->load(['estudiante.persona', 'ofertaAcademica', 'estadoPostulacion']),
        ], 201);
    }

    public function show(Postulacion $postulacion): JsonResponse
    {
        return response()->json([
            'data' => $postulacion->load(['estudiante.persona', 'ofertaAcademica', 'estadoPostulacion']),
        ]);
    }

    public function update(UpdatePostulacionRequest $request, Postulacion $postulacion): JsonResponse
    {
        $postulacion = $this->service->update($postulacion, $request->validated());

        return response()->json([
            'message' => 'Postulación actualizada.',
            'data' => $postulacion,
        ]);
    }

    public function destroy(Postulacion $postulacion): JsonResponse
    {
        $this->service->delete($postulacion);

        return response()->json(['message' => 'Postulación eliminada.']);
    }
}
