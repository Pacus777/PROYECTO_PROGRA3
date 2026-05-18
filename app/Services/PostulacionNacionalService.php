<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Postulacion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final class PostulacionNacionalService
{
    public function queryFiltrada(Request $request): Builder
    {
        $query = Postulacion::query()
            ->with([
                'estadoPostulacion',
                'resultado',
                'ofertaAcademica.unidadEducativa.municipio.provincia.departamento',
                'ofertaAcademica.unidadEducativa.distritoEducativo',
                'ofertaAcademica.gestion',
                'ofertaAcademica.curso',
                'ofertaAcademica.paralelo',
                'estudiante.persona',
            ]);

        if ($request->filled('id_ued')) {
            $query->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', (int) $request->input('id_ued')));
        } else {
            $this->aplicarFiltroTerritorio($query, $request);
        }

        if ($request->filled('id_ept_pos')) {
            $query->where('id_ept_pos', (int) $request->input('id_ept_pos'));
        }

        if ($request->filled('id_ges')) {
            $query->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ges_oac', (int) $request->input('id_ges')));
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_pos', '>=', $request->input('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_pos', '<=', $request->input('fecha_hasta'));
        }

        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));
            $query->where(function (Builder $q) use ($term): void {
                $q->whereHas('estudiante', fn (Builder $e) => $e->where('rude_est', 'like', "%{$term}%")
                    ->orWhere('codigo_est', 'like', "%{$term}%"))
                    ->orWhereHas('estudiante.persona', function (Builder $p) use ($term): void {
                        $p->where('nombres_per', 'like', "%{$term}%")
                            ->orWhere('ap_paterno_per', 'like', "%{$term}%")
                            ->orWhere('ci_per', 'like', "%{$term}%");
                    });
            });
        }

        return $query->orderByDesc('fecha_pos')->orderByDesc('id_pos');
    }

    private function aplicarFiltroTerritorio(Builder $query, Request $request): void
    {
        if ($request->filled('id_dis')) {
            $idDis = (int) $request->input('id_dis');
            $query->whereHas(
                'ofertaAcademica.unidadEducativa',
                fn (Builder $q) => $q->where('id_dis_ued', $idDis),
            );

            return;
        }

        if ($request->filled('id_mun')) {
            $idMun = (int) $request->input('id_mun');
            $query->whereHas(
                'ofertaAcademica.unidadEducativa',
                fn (Builder $q) => $q->where('id_mun_ued', $idMun),
            );

            return;
        }

        if ($request->filled('id_prov')) {
            $idProv = (int) $request->input('id_prov');
            $query->whereHas(
                'ofertaAcademica.unidadEducativa.municipio',
                fn (Builder $q) => $q->where('id_prov_mun', $idProv),
            );

            return;
        }

        if ($request->filled('id_dep')) {
            $idDep = (int) $request->input('id_dep');
            $query->whereHas(
                'ofertaAcademica.unidadEducativa.municipio.provincia',
                fn (Builder $q) => $q->where('id_dep_prov', $idDep),
            );
        }
    }

    public function paginar(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        return $this->queryFiltrada($request)->paginate($perPage)->withQueryString();
    }
}
