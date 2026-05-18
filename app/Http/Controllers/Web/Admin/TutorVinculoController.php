<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Tutor;
use App\Services\TutorVinculoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TutorVinculoController extends Controller
{
    public function __construct(
        private readonly TutorVinculoService $service,
    ) {}

    public function listAll(Request $request): View
    {
        $perPage = max(5, min(50, (int) $request->query('per_page', 15)));

        $search = $request->query('q');

        $tutores = Tutor::query()
            ->with('persona')
            ->withCount('estudiantes')
            ->when($search, function ($q) use ($search): void {
                $q->whereHas('persona', function ($p) use ($search): void {
                    $p->where('nombres_per', 'like', "%{$search}%")
                        ->orWhere('ap_paterno_per', 'like', "%{$search}%")
                        ->orWhere('ci_per', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id_tut')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.tutores.index', compact('tutores', 'search'));
    }

    public function index(Tutor $tutor): View
    {
        $tutor->load('persona');

        $vinculados = $this->service->estudiantesVinculados($tutor);
        $disponibles = $this->service->estudiantesNoVinculados($tutor);

        return view('admin.tutores.estudiantes', compact('tutor', 'vinculados', 'disponibles'));
    }

    public function attach(Request $request, Tutor $tutor): RedirectResponse
    {
        $request->validate([
            'id_est' => ['required', 'integer', 'exists:estudiante,id_est'],
        ]);

        try {
            $this->service->attach($tutor, (int) $request->input('id_est'));
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.tutores.estudiantes.index', $tutor)
            ->with('success', 'Estudiante vinculado correctamente.');
    }

    public function detach(Tutor $tutor, Estudiante $estudiante): RedirectResponse
    {
        $this->service->detach($tutor, $estudiante->id_est);

        return redirect()
            ->route('admin.tutores.estudiantes.index', $tutor)
            ->with('success', 'Estudiante desvinculado.');
    }
}
