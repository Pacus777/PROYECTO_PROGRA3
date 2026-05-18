<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Curso;
use App\Models\EstadoPostulacion;
use App\Models\Gestion;
use App\Models\OfertaAcademica;
use App\Models\Postulacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

final class PostulacionInstitucionalService
{
    public function queryParaUnidad(int $unidadId, Request $request): Builder
    {
        $query = Postulacion::query()
            ->with([
                'estadoPostulacion',
                'resultado',
                'ofertaAcademica.gestion',
                'ofertaAcademica.nivel',
                'ofertaAcademica.curso',
                'ofertaAcademica.paralelo',
                'estudiante.persona',
                'estudiante.unidadMatriculaActual',
            ])
            ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId));

        if ($request->filled('id_ept_pos')) {
            $query->where('id_ept_pos', (int) $request->input('id_ept_pos'));
        }

        if ($request->filled('id_ges_oac')) {
            $gestionId = (int) $request->input('id_ges_oac');
            $query->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ges_oac', $gestionId));
        }

        if ($request->filled('id_cur_oac')) {
            $cursoId = (int) $request->input('id_cur_oac');
            $query->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_cur_oac', $cursoId));
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_pos', '>=', $request->input('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_pos', '<=', $request->input('fecha_hasta'));
        }

        $buscar = trim((string) $request->input('buscar', $request->input('q', '')));
        if ($buscar !== '') {
            $query->where(function (Builder $q) use ($buscar): void {
                $q->whereHas('estudiante', fn (Builder $e) => $e->where('rude_est', 'like', "%{$buscar}%")
                    ->orWhere('codigo_est', 'like', "%{$buscar}%"))
                    ->orWhereHas('estudiante.persona', function (Builder $p) use ($buscar): void {
                        $p->where('nombres_per', 'like', "%{$buscar}%")
                            ->orWhere('ap_paterno_per', 'like', "%{$buscar}%")
                            ->orWhere('ap_materno_per', 'like', "%{$buscar}%")
                            ->orWhere('ci_per', 'like', "%{$buscar}%");
                    });
            });
        }

        return $query->orderByDesc('fecha_pos')->orderByDesc('id_pos');
    }

    /** @return array{gestiones: Collection, cursos: Collection} */
    public function filtrosParaUnidad(int $unidadId): array
    {
        $ofertas = OfertaAcademica::query()
            ->where('id_ued_oac', $unidadId);

        $gestionIds = (clone $ofertas)->distinct()->pluck('id_ges_oac')->filter();
        $cursoIds = (clone $ofertas)->distinct()->pluck('id_cur_oac')->filter();

        return [
            'gestiones' => Gestion::query()
                ->whereIn('id_ges', $gestionIds)
                ->orderBy('nombre_ges')
                ->get(['id_ges', 'nombre_ges']),
            'cursos' => Curso::query()
                ->whereIn('id_cur', $cursoIds)
                ->orderBy('nombre_cur')
                ->get(['id_cur', 'nombre_cur']),
        ];
    }

    /**
     * Totales por estado (mismos filtros que el listado, sin filtro de estado).
     *
     * @return array{total: int, por_estado: Collection<int, array{nombre: string, total: int}>}
     */
    public function resumenParaUnidad(int $unidadId, Request $request): array
    {
        $filtros = $request->duplicate();
        $filtros->request->remove('id_ept_pos');

        $base = $this->queryParaUnidad($unidadId, $filtros);

        $countsByEstado = (clone $base)
            ->reorder()
            ->selectRaw('id_ept_pos, COUNT(*) as total')
            ->groupBy('id_ept_pos')
            ->pluck('total', 'id_ept_pos');

        $nombres = EstadoPostulacion::query()
            ->whereIn('id_ept', $countsByEstado->keys())
            ->pluck('nombre_ept', 'id_ept');

        $porEstado = $countsByEstado->map(function (int|string $total, int|string $idEpt) use ($nombres): array {
            return [
                'nombre' => (string) ($nombres[(int) $idEpt] ?? '—'),
                'total' => (int) $total,
            ];
        })->values()->sortByDesc('total')->values();

        return [
            'total' => (int) (clone $base)->reorder()->count(),
            'por_estado' => $porEstado,
        ];
    }
}
