<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Auth\StoreTutorRegistroRequest;
use App\Models\Estudiante;
use App\Services\TutorRegistroService;
use Illuminate\Http\RedirectResponse;

class TutorRegistroController extends Controller
{
    public function __construct(
        private readonly TutorRegistroService $registro,
    ) {}

    public function store(StoreTutorRegistroRequest $request): RedirectResponse
    {
        $result = $this->registro->registrar($request->validated());

        $request->session()->regenerate();
        $request->session()->put('web_usuario_id', $result['usuario']->id_usu);

        $mensaje = '¡Bienvenido! Tu cuenta de tutor fue creada.';
        if ($result['vinculados'] > 0) {
            $mensaje .= ' Se vincularon '.$result['vinculados'].' estudiante(s)';
            if ($result['nombres_vinculados'] !== []) {
                $mensaje .= ': '.implode(', ', $result['nombres_vinculados']);
            }
            $mensaje .= '.';
        }

        $postularColegio = $request->session()->pull('postular_colegio');
        $request->session()->forget('postular_colegio_nombre');

        /** @var list<Estudiante> $estudiantesVinculados */
        $estudiantesVinculados = $result['estudiantes_vinculados'] ?? [];

        $estudianteSinDomicilio = collect($estudiantesVinculados)
            ->first(fn (Estudiante $e) => ! $e->tieneDomicilioRegistrado());

        if ($estudianteSinDomicilio !== null) {
            $returnUrl = is_string($postularColegio) && $postularColegio !== ''
                ? route('tutor.postulaciones.create', ['colegio' => $postularColegio])
                : route('tutor.postulaciones.create');

            return redirect()
                ->route('tutor.estudiantes.domicilio.edit', [
                    'estudiante' => $estudianteSinDomicilio,
                    'return' => $returnUrl,
                ])
                ->with('success', $mensaje)
                ->with('warning', 'Registre el domicilio del estudiante en el mapa para evaluar la cercanía al colegio.');
        }

        if (is_string($postularColegio) && $postularColegio !== '') {
            return redirect()
                ->route('tutor.postulaciones.create', ['colegio' => $postularColegio])
                ->with('success', $mensaje);
        }

        return redirect()
            ->route('tutor.estudiantes.index')
            ->with('success', $mensaje);
    }
}
