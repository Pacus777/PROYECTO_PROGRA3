<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWebAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('web_usuario_id')) {
            return redirect()->route('login.show');
        }

        return $next($request);
    }
}
