<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Estudiante;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final class EstudianteQueryService
{
    public function queryFiltrada(Request $request): Builder
    {
        $query = Estudiante::query()
            ->with(['persona', 'unidadMatriculaActual.municipio.provincia.departamento'])
            ->withCount(['tutores', 'postulaciones']);

        if ($request->filled('incidencia')) {
            match ($request->input('incidencia')) {
                'sin_tutor' => $query->doesntHave('tutores'),
                'sin_rude' => $query->where(fn (Builder $q) => $q->whereNull('rude_est')->orWhere('rude_est', '')),
                'sin_matricula' => $query->whereNull('id_ued_mat_est'),
                'rude_duplicado' => $query->whereNotNull('rude_est')
                    ->where('rude_est', '!=', '')
                    ->whereIn('rude_est', function ($sub): void {
                        $sub->select('rude_est')
                            ->from('estudiante')
                            ->whereNotNull('rude_est')
                            ->where('rude_est', '!=', '')
                            ->groupBy('rude_est')
                            ->havingRaw('COUNT(*) > 1');
                    }),
                default => null,
            };
        }

        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));
            $query->where(function (Builder $q) use ($term): void {
                $q->where('rude_est', 'like', "%{$term}%")
                    ->orWhere('codigo_est', 'like', "%{$term}%")
                    ->orWhereHas('persona', function (Builder $p) use ($term): void {
                        $p->where('nombres_per', 'like', "%{$term}%")
                            ->orWhere('ap_paterno_per', 'like', "%{$term}%")
                            ->orWhere('ci_per', 'like', "%{$term}%");
                    });
            });
        }

        return $query->orderByDesc('id_est');
    }
}
