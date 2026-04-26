<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Tutor\Concerns\ResolvesTutorContext;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TutorPerfilController extends Controller
{
    use ResolvesTutorContext;

    public function index(Request $request): View
    {
        $usuario = $this->webUsuario($request);
        if ($usuario !== null) {
            $usuario->loadMissing(['persona', 'rol', 'unidadEducativa']);
        }
        $tutor = $this->tutorFromRequest($request);

        return view('tutor.perfil.index', compact('usuario', 'tutor'));
    }
}
