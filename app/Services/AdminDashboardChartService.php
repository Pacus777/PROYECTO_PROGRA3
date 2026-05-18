<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Documento;
use App\Models\Estudiante;
use App\Models\Gestion;
use App\Models\OfertaAcademica;
use App\Models\Postulacion;
use App\Models\Tutor;
use App\Models\UnidadEducativa;
use App\Models\Usuario;
use App\Support\Roles;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class AdminDashboardChartService
{
    /**
     * @return array{
     *     kpis: array<string, int|string|bool>,
     *     charts: array<string, array{labels: list<string>, data: list<int>}>
     * }
     */
    public function build(): array
    {
        $gestionActiva = Gestion::query()->where('activa_ges', true)->first();

        return [
            'kpis' => [
                'usuarios' => Usuario::query()->count(),
                'usuarios_activos' => Usuario::query()->where('activo_usu', true)->count(),
                'unidades' => UnidadEducativa::query()->count(),
                'gestiones' => Gestion::query()->count(),
                'gestion_activa' => $gestionActiva?->nombre_ges ?? 'Ninguna',
                'sin_gestion_activa' => $gestionActiva === null,
                'estudiantes' => Estudiante::query()->count(),
                'tutores' => Tutor::query()->count(),
                'postulaciones' => Postulacion::query()->count(),
                'ofertas' => OfertaAcademica::query()->count(),
                'documentos' => Documento::query()->count(),
            ],
            'charts' => [
                'usuarios_por_rol' => $this->usuariosPorRol(),
                'usuarios_estado' => $this->usuariosPorEstado(),
                'postulaciones_estado' => $this->postulacionesPorEstado(),
                'postulaciones_unidad' => $this->postulacionesPorUnidad(),
                'postulaciones_mes' => $this->postulacionesPorMes(),
                'estudiantes_tutor' => $this->estudiantesPorVinculoTutor(),
                'estudiantes_calidad' => $this->estudiantesCalidadDatos(),
                'documentos_estado' => $this->documentosPorEstado(),
            ],
        ];
    }

    /**
     * @return array{labels: list<string>, data: list<int>}
     */
    private function usuariosPorRol(): array
    {
        $rows = Usuario::query()
            ->join('rol', 'usuario.id_rol_usu', '=', 'rol.id_rol')
            ->selectRaw('rol.nombre_rol as rol, COUNT(*) as total')
            ->groupBy('rol.nombre_rol')
            ->orderByDesc('total')
            ->get();

        $labels = [];
        $data = [];

        foreach ($rows as $row) {
            $labels[] = $this->humanizeRole((string) $row->rol);
            $data[] = (int) $row->total;
        }

        return compact('labels', 'data');
    }

    /**
     * @return array{labels: list<string>, data: list<int>}
     */
    private function usuariosPorEstado(): array
    {
        $activos = Usuario::query()->where('activo_usu', true)->count();
        $inactivos = Usuario::query()->where('activo_usu', false)->count();

        return [
            'labels' => ['Activos', 'Inactivos'],
            'data' => [$activos, $inactivos],
        ];
    }

    /**
     * @return array{labels: list<string>, data: list<int>}
     */
    private function postulacionesPorEstado(): array
    {
        $rows = Postulacion::query()
            ->join('estado_postulacion', 'postulacion.id_ept_pos', '=', 'estado_postulacion.id_ept')
            ->selectRaw('estado_postulacion.nombre_ept as estado, COUNT(*) as total')
            ->groupBy('estado_postulacion.nombre_ept')
            ->orderByDesc('total')
            ->get();

        $labels = [];
        $data = [];

        foreach ($rows as $row) {
            $labels[] = $this->humanizeEstado((string) $row->estado);
            $data[] = (int) $row->total;
        }

        return compact('labels', 'data');
    }

    /**
     * @return array{labels: list<string>, data: list<int>}
     */
    private function postulacionesPorUnidad(): array
    {
        $rows = Postulacion::query()
            ->join('oferta_academica', 'postulacion.id_oac_pos', '=', 'oferta_academica.id_oac')
            ->join('unidad_educativa', 'oferta_academica.id_ued_oac', '=', 'unidad_educativa.id_ued')
            ->selectRaw('unidad_educativa.nombre_ued as unidad, COUNT(*) as total')
            ->groupBy('unidad_educativa.id_ued', 'unidad_educativa.nombre_ued')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $labels = [];
        $data = [];

        foreach ($rows as $row) {
            $labels[] = (string) $row->unidad;
            $data[] = (int) $row->total;
        }

        return compact('labels', 'data');
    }

    /**
     * @return array{labels: list<string>, data: list<int>}
     */
    private function postulacionesPorMes(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = ucfirst($month->locale('es')->isoFormat('MMM YYYY'));
            $data[] = Postulacion::query()
                ->whereNotNull('fecha_pos')
                ->whereYear('fecha_pos', $month->year)
                ->whereMonth('fecha_pos', $month->month)
                ->count();
        }

        return compact('labels', 'data');
    }

    /**
     * @return array{labels: list<string>, data: list<int>}
     */
    private function estudiantesPorVinculoTutor(): array
    {
        $conTutor = Estudiante::query()->whereHas('tutores')->count();
        $sinTutor = Estudiante::query()->whereDoesntHave('tutores')->count();

        return [
            'labels' => ['Con tutor', 'Sin tutor'],
            'data' => [$conTutor, $sinTutor],
        ];
    }

    /**
     * Incidencias de calidad de datos (mismos criterios que exportación de postulantes).
     *
     * @return array{labels: list<string>, data: list<int>}
     */
    private function estudiantesCalidadDatos(): array
    {
        $sinTutor = Estudiante::query()->doesntHave('tutores')->count();
        $sinRude = Estudiante::query()
            ->where(fn ($q) => $q->whereNull('rude_est')->orWhere('rude_est', ''))
            ->count();
        $sinMatricula = Estudiante::query()->whereNull('id_ued_mat_est')->count();
        $rudeDuplicado = Estudiante::query()
            ->whereNotNull('rude_est')
            ->where('rude_est', '!=', '')
            ->whereIn('rude_est', function ($sub): void {
                $sub->select('rude_est')
                    ->from('estudiante')
                    ->whereNotNull('rude_est')
                    ->where('rude_est', '!=', '')
                    ->groupBy('rude_est')
                    ->havingRaw('COUNT(*) > 1');
            })
            ->count();

        return [
            'labels' => ['Sin tutor', 'Sin RUDE', 'Sin matrícula', 'RUDE duplicado'],
            'data' => [$sinTutor, $sinRude, $sinMatricula, $rudeDuplicado],
        ];
    }

    /**
     * @return array{labels: list<string>, data: list<int>}
     */
    private function documentosPorEstado(): array
    {
        $rows = Documento::query()
            ->selectRaw("COALESCE(NULLIF(estado_doc, ''), 'pendiente') as estado, COUNT(*) as total")
            ->groupBy(DB::raw("COALESCE(NULLIF(estado_doc, ''), 'pendiente')"))
            ->orderByDesc('total')
            ->get();

        $labels = [];
        $data = [];

        foreach ($rows as $row) {
            $labels[] = $this->humanizeEstado((string) $row->estado);
            $data[] = (int) $row->total;
        }

        return compact('labels', 'data');
    }

    private function humanizeRole(string $role): string
    {
        return Roles::label($role);
    }

    private function humanizeEstado(string $estado): string
    {
        return ucfirst(str_replace('_', ' ', $estado));
    }
}
