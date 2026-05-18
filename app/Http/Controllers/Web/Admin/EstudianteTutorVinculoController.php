<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Tutor;
use App\Services\TutorVinculoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EstudianteTutorVinculoController extends Controller
{
    public function __construct(
        private readonly TutorVinculoService $service,
    ) {}

    public function attach(Request $request, Estudiante $estudiante): RedirectResponse
    {
        $request->validate([
            'id_tut' => ['required', 'integer', 'exists:tutor,id_tut'],
        ]);

        try {
            $tutor = Tutor::query()->findOrFail((int) $request->input('id_tut'));
            $this->service->attach($tutor, $estudiante->id_est);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.estudiantes.edit', $estudiante)
            ->with('success', 'Tutor vinculado al estudiante.');
    }

    public function detach(Estudiante $estudiante, Tutor $tutor): RedirectResponse
    {
        $this->service->detach($tutor, $estudiante->id_est);

        return redirect()
            ->route('admin.estudiantes.edit', $estudiante)
            ->with('success', 'Tutor desvinculado.');
    }
}
