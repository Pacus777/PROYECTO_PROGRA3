<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $rolesCsv): Response
    {
        $usuario = $request->user();

        if ($usuario === null) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $roles = array_map('trim', explode(',', $rolesCsv));

        $usuario->loadMissing('rol');
        $nombre = $usuario->rol?->nombre_rol;

        if ($nombre === null || ! in_array($nombre, $roles, true)) {
            return response()->json(['message' => 'No autorizado para este recurso.'], 403);
        }

        return $next($request);
    }
}
