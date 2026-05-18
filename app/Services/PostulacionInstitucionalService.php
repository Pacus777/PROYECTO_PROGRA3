<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Postulacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final class PostulacionInstitucionalService
{
    public function queryParaUnidad(int $unidadId, Request $request): Builder
    {
        $query = Postulacion::query()
            ->with([
                'estadoPostulacion',
                'resultado',
                'ofertaAcademica.curso',
                'ofertaAcademica.paralelo',
                'ofertaAcademica.gestion',
                'estudiante.persona',
            ])
            ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId));

        if ($request->filled('id_ept_pos')) {
            $query->where('id_ept_pos', (int) $request->input('id_ept_pos'));
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
}
