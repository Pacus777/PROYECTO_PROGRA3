<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autorización por rol para la sesión web personalizada (web_usuario_id).
 * No usa Auth::user(); debe ejecutarse después de web.auth.
 */
class EnsureWebUserHasRole
{
    public function handle(Request $request, Closure $next, string $rolesCsv): Response
    {
        $usuarioId = $request->session()->get('web_usuario_id');

        if ($usuarioId === null) {
            return redirect()->route('login.show');
        }

        /** @var Usuario|null $usuario */
        $usuario = Usuario::query()->with('rol')->find($usuarioId);

        if ($usuario === null) {
            $request->session()->forget('web_usuario_id');

            return redirect()->route('login.show');
        }

        $allowed = array_map('trim', explode(',', $rolesCsv));
        $nombreRol = $usuario->rol?->nombre_rol;

        if ($nombreRol === null || ! in_array($nombreRol, $allowed, true)) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        $request->attributes->set('web_usuario', $usuario);

        return $next($request);
    }
}
