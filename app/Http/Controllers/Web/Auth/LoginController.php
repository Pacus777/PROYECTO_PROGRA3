<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'correo_usu' => ['required', 'string', 'email', 'max:160'],
            'password_usu' => ['required', 'string'],
        ]);

        $usuario = Usuario::query()
            ->where('correo_usu', $credentials['correo_usu'])
            ->first();

        if ($usuario === null || ! Hash::check($credentials['password_usu'], $usuario->password_usu)) {
            return back()
                ->withErrors(['correo_usu' => 'Credenciales incorrectas.'])
                ->onlyInput('correo_usu');
        }

        if (! $usuario->activo_usu) {
            return back()
                ->withErrors(['correo_usu' => 'El usuario se encuentra inactivo.'])
                ->onlyInput('correo_usu');
        }

        $request->session()->regenerate();
        $request->session()->put('web_usuario_id', $usuario->id_usu);

        return redirect()->route('dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->forget('web_usuario_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
