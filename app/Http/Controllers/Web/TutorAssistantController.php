<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Services\TutorAssistantService;
use App\Support\Roles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TutorAssistantController extends Controller
{
    public function __construct(
        private readonly TutorAssistantService $assistant,
    ) {}

    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:500'],
            'context' => ['required', 'in:landing,tutor'],
        ]);

        $context = $validated['context'];
        $message = $validated['message'] ?? '';

        if ($context === 'tutor') {
            $usuarioId = session('web_usuario_id');
            $usuario = $usuarioId
                ? Usuario::query()->with('rol')->find($usuarioId)
                : null;
            if ($usuario?->rol?->nombre_rol !== Roles::TUTOR) {
                return response()->json(['message' => 'No autorizado.'], 403);
            }
        }

        if ($message === '' || $message === '__welcome__') {
            $payload = $this->assistant->welcome($context);
        } else {
            $payload = $this->assistant->reply($message, $context);
        }

        return response()->json($payload);
    }
}
