<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\UnidadEducativa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final class UnidadEducativaQueryService
{
    public function queryFiltrada(Request $request): Builder
    {
        $query = UnidadEducativa::query()
            ->with(['municipio.provincia.departamento', 'distritoEducativo']);

        if ($request->filled('id_dis')) {
            $query->where('id_dis_ued', (int) $request->input('id_dis'));
        } elseif ($request->filled('id_mun')) {
            $query->where('id_mun_ued', (int) $request->input('id_mun'));
        } elseif ($request->filled('id_prov')) {
            $idProv = (int) $request->input('id_prov');
            $query->whereHas('municipio', fn (Builder $q) => $q->where('id_prov_mun', $idProv));
        } elseif ($request->filled('id_dep')) {
            $idDep = (int) $request->input('id_dep');
            $query->whereHas('municipio.provincia', fn (Builder $q) => $q->where('id_dep_prov', $idDep));
        }

        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));
            $query->where(function (Builder $q) use ($term): void {
                $q->where('nombre_ued', 'like', "%{$term}%")
                    ->orWhere('codigo_ued', 'like', "%{$term}%")
                    ->orWhere('direccion_ued', 'like', "%{$term}%");
            });
        }

        return $query->orderBy('nombre_ued');
    }
}
