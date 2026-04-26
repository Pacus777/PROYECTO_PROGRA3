<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Tutor\Concerns\ResolvesTutorContext;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TutorEstudianteController extends Controller
{
    use ResolvesTutorContext;

    public function index(Request $request): View
    {
        $tutor = $this->tutorFromRequest($request);

        return view('tutor.estudiantes.index', [
            'tutor' => $tutor,
        ]);
    }
}
