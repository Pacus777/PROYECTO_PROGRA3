<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Tutor\Concerns\ResolvesTutorContext;
use App\Support\EstudianteIdentificador;
use App\Services\TutorVinculoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TutorEstudianteController extends Controller
{
    use ResolvesTutorContext;

    public function __construct(
        private readonly TutorVinculoService $service,
    ) {}

    public function index(Request $request): View
    {
        $tutor = $this->tutorFromRequest($request);

        return view('tutor.estudiantes.index', [
            'tutor' => $tutor,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'codigo_est' => ['required', 'string', 'max:40'],
        ], [
            'codigo_est.required' => 'Ingresa el RUDE o el código del estudiante.',
        ]);

        $tutor = $this->tutorFromRequest($request);

        if ($tutor === null) {
            return back()->with('error', 'No hay perfil de tutor asociado a tu cuenta.');
        }

        $estudiante = EstudianteIdentificador::buscarPorCodigoOVinculo($request->input('codigo_est'));

        if ($estudiante === null) {
            return back()->with('error', 'No se encontró ningún estudiante con ese RUDE o código.');
        }

        try {
            $this->service->attach($tutor, $estudiante->id_est);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('tutor.estudiantes.index')
            ->with('success', 'Estudiante vinculado correctamente.');
    }

    public function destroy(Request $request, Estudiante $estudiante): RedirectResponse
    {
        $tutor = $this->tutorFromRequest($request);

        if ($tutor === null) {
            return back()->with('error', 'No hay perfil de tutor asociado a tu cuenta.');
        }

        $this->service->detach($tutor, $estudiante->id_est);

        return redirect()
            ->route('tutor.estudiantes.index')
            ->with('success', 'Estudiante desvinculado.');
    }
}
