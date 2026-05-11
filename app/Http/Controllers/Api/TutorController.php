<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tutor\AttachEstudianteRequest;
use App\Models\Estudiante;
use App\Models\Tutor;
use App\Services\TutorVinculoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TutorController extends Controller
{
    public function __construct(
        private readonly TutorVinculoService $service,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);

        return response()->json($this->service->listTutores($perPage));
    }

    public function show(Tutor $tutor): JsonResponse
    {
        $tutor->load('persona');

        return response()->json(['data' => $tutor]);
    }

    public function estudiantes(Tutor $tutor): JsonResponse
    {
        return response()->json([
            'data' => $this->service->estudiantesVinculados($tutor),
        ]);
    }

    public function attach(AttachEstudianteRequest $request, Tutor $tutor): JsonResponse
    {
        try {
            $this->service->attach($tutor, (int) $request->validated()['id_est']);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Estudiante vinculado al tutor.'], 201);
    }

    public function detach(Tutor $tutor, Estudiante $estudiante): JsonResponse
    {
        $this->service->detach($tutor, $estudiante->id_est);

        return response()->json(['message' => 'Vínculo eliminado.']);
    }
}
