<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Tutor\Concerns\ResolvesTutorContext;
use App\Models\Estudiante;
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

    public function editDomicilio(Request $request, Estudiante $estudiante): View|RedirectResponse
    {
        $tutor = $this->tutorFromRequest($request);
        if ($tutor === null) {
            return redirect()->route('tutor.estudiantes.index')->with('error', 'No hay perfil de tutor.');
        }

        abort_unless(
            in_array((int) $estudiante->id_est, $this->tutorEstudianteIds($request), true),
            403,
        );

        $estudiante->load('persona');

        $returnUrl = $this->redirectSeguro($request->query('return'));

        return view('tutor.estudiantes.domicilio', compact('estudiante', 'tutor', 'returnUrl'));
    }

    private function redirectSeguro(mixed $url): ?string
    {
        if (! is_string($url) || $url === '') {
            return null;
        }

        if (! str_starts_with($url, url('/'))) {
            return null;
        }

        return $url;
    }

    public function updateDomicilio(Request $request, Estudiante $estudiante): RedirectResponse
    {
        $tutor = $this->tutorFromRequest($request);
        if ($tutor === null) {
            return back()->with('error', 'No hay perfil de tutor.');
        }

        abort_unless(
            in_array((int) $estudiante->id_est, $this->tutorEstudianteIds($request), true),
            403,
        );

        $validated = $request->validate([
            'direccion_est' => ['required', 'string', 'max:255'],
            'lat_est' => ['required', 'numeric', 'between:-90,90'],
            'lng_est' => ['required', 'numeric', 'between:-180,180'],
        ], [
            'direccion_est.required' => 'Indique la dirección del domicilio.',
            'lat_est.required' => 'Marque el punto en el mapa (latitud).',
            'lng_est.required' => 'Marque el punto en el mapa (longitud).',
        ]);

        $estudiante->update([
            'direccion_est' => $validated['direccion_est'],
            'lat_est' => $validated['lat_est'],
            'lng_est' => $validated['lng_est'],
        ]);

        $returnUrl = $this->redirectSeguro($request->input('return'));
        if ($returnUrl !== null) {
            return redirect()
                ->to($returnUrl)
                ->with('success', 'Domicilio registrado. Ya puede completar la postulación.');
        }

        return redirect()
            ->route('tutor.estudiantes.index')
            ->with('success', 'Domicilio del estudiante actualizado.');
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
