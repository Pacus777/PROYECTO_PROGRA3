<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EstadoPostulacion;
use App\Models\Postulacion;
use App\Models\Tutor;
use App\Models\Usuario;
use App\Support\Roles;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        /** @var int $usuarioId */
        $usuarioId = (int) $request->session()->get('web_usuario_id');

        $usuario = Usuario::query()
            ->with(['persona', 'rol'])
            ->findOrFail($usuarioId);

        $tutorDashboard = $this->buildTutorDashboardContext($usuario);

        return view('dashboard', [
            'usuario' => $usuario,
            'tutorDashboard' => $tutorDashboard,
        ]);
    }

    /**
     * @return array{
     *     active: bool,
     *     warning: string|null,
     *     estudiantes: Collection<int, \App\Models\Estudiante>,
     *     stats: array<string, int|float|Collection>,
     *     recent: Collection<int, \App\Models\Postulacion>
     * }
     */
    private function buildTutorDashboardContext(Usuario $usuario): array
    {
        $empty = [
            'active' => false,
            'warning' => null,
            'estudiantes' => collect(),
            'stats' => [
                'total_estudiantes' => 0,
                'total_postulaciones' => 0,
                'por_estado' => collect(),
                'en_evaluacion' => 0,
                'aprobadas' => 0,
                'rechazadas' => 0,
                'enviadas' => 0,
                'en_lista_espera' => 0,
                'con_resultado' => 0,
            ],
            'recent' => collect(),
        ];

        if (($usuario->rol->nombre_rol ?? '') !== Roles::TUTOR) {
            return $empty;
        }

        $tutor = Tutor::query()
            ->where('id_per_tut', $usuario->id_per_usu)
            ->with(['estudiantes.persona'])
            ->first();

        if ($tutor === null) {
            return [
                'active' => true,
                'warning' => 'sin_registro_tutor',
                'estudiantes' => collect(),
                'stats' => $empty['stats'],
                'recent' => collect(),
            ];
        }

        $estudiantes = $tutor->estudiantes;
        $estudianteIds = $estudiantes->pluck('id_est')->all();

        if ($estudianteIds === []) {
            return [
                'active' => true,
                'warning' => 'sin_estudiantes',
                'estudiantes' => $estudiantes,
                'stats' => $empty['stats'],
                'recent' => collect(),
            ];
        }

        $postBase = Postulacion::query()->whereIn('id_est_pos', $estudianteIds);

        $totalPostulaciones = (clone $postBase)->count();

        $countsByEstadoId = (clone $postBase)
            ->selectRaw('id_ept_pos, COUNT(*) as total')
            ->groupBy('id_ept_pos')
            ->pluck('total', 'id_ept_pos');

        $estadoNombres = EstadoPostulacion::query()
            ->whereIn('id_ept', $countsByEstadoId->keys())
            ->pluck('nombre_ept', 'id_ept');

        $porEstado = $countsByEstadoId->map(function (int|string $total, int|string $idEpt) use ($estadoNombres): array {
            $nombre = (string) ($estadoNombres[(int) $idEpt] ?? '—');

            return [
                'nombre' => $nombre,
                'total' => (int) $total,
            ];
        })->values()->sortByDesc('total')->values();

        $byNombre = $porEstado->keyBy(fn (array $row): string => strtolower($row['nombre']));

        $enListaEspera = (clone $postBase)->whereHas('listasEspera')->count();
        $conResultado = (clone $postBase)->whereHas('resultado')->count();

        $recent = Postulacion::query()
            ->whereIn('id_est_pos', $estudianteIds)
            ->with([
                'estadoPostulacion',
                'resultado',
                'ofertaAcademica.gestion',
                'ofertaAcademica.nivel',
                'ofertaAcademica.curso',
                'ofertaAcademica.paralelo',
                'ofertaAcademica.unidadEducativa',
                'estudiante.persona',
            ])
            ->orderByDesc('fecha_pos')
            ->orderByDesc('id_pos')
            ->limit(8)
            ->get();

        return [
            'active' => true,
            'warning' => null,
            'estudiantes' => $estudiantes,
            'stats' => [
                'total_estudiantes' => $estudiantes->count(),
                'total_postulaciones' => $totalPostulaciones,
                'por_estado' => $porEstado,
                'en_evaluacion' => (int) ($byNombre->get('en_evaluacion')['total'] ?? 0),
                'aprobadas' => (int) ($byNombre->get('aprobada')['total'] ?? 0),
                'rechazadas' => (int) ($byNombre->get('rechazada')['total'] ?? 0),
                'enviadas' => (int) ($byNombre->get('enviada')['total'] ?? 0),
                'en_lista_espera' => $enListaEspera,
                'con_resultado' => $conResultado,
            ],
            'recent' => $recent,
        ];
    }
}
