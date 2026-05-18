<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asignacion;
use App\Models\EstadoPostulacion;
use App\Models\ListaEspera;
use App\Models\OfertaAcademica;
use App\Models\Postulacion;
use App\Models\Resultado;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class ResultadoInstitucionalService
{
    /** @return array<int, float> id_pos => puntaje */
    public function puntajesCalculadosParaPostulaciones(Collection $postulacionIds): array
    {
        if ($postulacionIds->isEmpty()) {
            return [];
        }

        return DB::table('evaluacion')
            ->join('criterio', 'criterio.id_cri', '=', 'evaluacion.id_cri_eva')
            ->whereIn('evaluacion.id_pos_eva', $postulacionIds)
            ->groupBy('evaluacion.id_pos_eva')
            ->selectRaw('evaluacion.id_pos_eva as id_pos, COALESCE(SUM(evaluacion.puntaje_eva * COALESCE(criterio.peso_cri, 1)), 0) as puntaje')
            ->pluck('puntaje', 'id_pos')
            ->map(fn ($v) => (float) $v)
            ->all();
    }

    public function queryPostulacionesUnidad(int $unidadId, Request $request): Builder
    {
        $query = Postulacion::query()
            ->with([
                'estudiante.persona',
                'estadoPostulacion',
                'ofertaAcademica.gestion',
                'ofertaAcademica.nivel',
                'ofertaAcademica.curso',
                'ofertaAcademica.paralelo',
                'resultado',
                'asignaciones',
                'listasEspera',
            ])
            ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId));

        if ($request->filled('id_ges_oac')) {
            $query->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ges_oac', (int) $request->input('id_ges_oac')));
        }

        if ($request->filled('id_cur_oac')) {
            $query->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_cur_oac', (int) $request->input('id_cur_oac')));
        }

        if ($request->filled('id_oac_pos')) {
            $query->where('id_oac_pos', (int) $request->input('id_oac_pos'));
        }

        return $query;
    }

    /**
     * Ranking por oferta: orden dentro de cada oferta académica.
     *
     * @return Collection<int, object{postulacion: Postulacion, puntaje: float, orden_oferta: int}>
     */
    public function filasRanking(int $unidadId, Request $request): Collection
    {
        $postulaciones = $this->queryPostulacionesUnidad($unidadId, $request)->get();
        $puntajes = $this->puntajesCalculadosParaPostulaciones($postulaciones->pluck('id_pos'));

        $porOferta = $postulaciones->groupBy('id_oac_pos');
        $filas = collect();

        foreach ($porOferta as $grupo) {
            $ordenadas = $grupo
                ->map(function (Postulacion $p) use ($puntajes) {
                    return (object) [
                        'postulacion' => $p,
                        'puntaje' => $puntajes[$p->id_pos] ?? 0.0,
                    ];
                })
                ->sortByDesc('puntaje')
                ->values();

            foreach ($ordenadas as $index => $fila) {
                $fila->orden_oferta = $index + 1;
                $filas->push($fila);
            }
        }

        return $filas->sortByDesc('puntaje')->values();
    }

    public function paginarRanking(Collection $filas, Request $request, int $perPage = 25): LengthAwarePaginator
    {
        $page = max(1, (int) $request->input('page', 1));
        $total = $filas->count();
        $items = $filas->forPage($page, $perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );
    }

    /**
     * @return array{
     *     postulaciones: int,
     *     con_evaluacion: int,
     *     resultados_guardados: int,
     *     asignados: int,
     *     lista_espera: int,
     *     ofertas_con_cupo: int
     * }
     */
    public function resumen(int $unidadId): array
    {
        $postBase = Postulacion::query()
            ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId));

        $postulacionesIds = (clone $postBase)->pluck('id_pos');

        $conEvaluacion = (int) DB::table('evaluacion')
            ->whereIn('id_pos_eva', $postulacionesIds)
            ->distinct()
            ->count('id_pos_eva');

        return [
            'postulaciones' => $postulacionesIds->count(),
            'con_evaluacion' => $conEvaluacion,
            'resultados_guardados' => Resultado::query()
                ->whereHas('postulacion.ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
                ->count(),
            'asignados' => Asignacion::query()
                ->whereHas('postulacion.ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
                ->where('estado_asi', 'asignado')
                ->count(),
            'lista_espera' => ListaEspera::query()
                ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
                ->count(),
            'ofertas_con_cupo' => OfertaAcademica::query()
                ->where('id_ued_oac', $unidadId)
                ->whereHas('cupos')
                ->count(),
        ];
    }

    /** Solo persiste puntajes y orden en tabla resultado (sin asignar cupos). */
    public function sincronizarResultados(int $unidadId): int
    {
        $filas = $this->filasRanking($unidadId, new Request);
        $actualizados = 0;

        foreach ($filas as $fila) {
            Resultado::query()->updateOrCreate(
                ['id_pos_res' => $fila->postulacion->id_pos],
                [
                    'puntaje_total_res' => $fila->puntaje,
                    'clasificacion_res' => $fila->orden_oferta,
                ],
            );
            $actualizados++;
        }

        return $actualizados;
    }

    /**
     * Ranking por oferta, asignación de cupos, lista de espera y estados.
     *
     * @return array{ofertas_procesadas: int, asignados: int, lista_espera: int}
     */
    public function ejecutarAsignacion(int $unidadId): array
    {
        $aprobadaEstadoId = (int) EstadoPostulacion::query()
            ->whereRaw('LOWER(nombre_ept) LIKE ?', ['%aprobada%'])
            ->value('id_ept');

        $stats = ['ofertas_procesadas' => 0, 'asignados' => 0, 'lista_espera' => 0];

        DB::transaction(function () use ($unidadId, $aprobadaEstadoId, &$stats): void {
            $ofertas = OfertaAcademica::query()
                ->with('cupos')
                ->where('id_ued_oac', $unidadId)
                ->get();

            foreach ($ofertas as $oferta) {
                $cupo = $oferta->cupos()->first();
                if ($cupo === null) {
                    continue;
                }

                $stats['ofertas_procesadas']++;

                $postulaciones = Postulacion::query()
                    ->where('id_oac_pos', $oferta->id_oac)
                    ->pluck('id_pos');

                $puntajes = $this->puntajesCalculadosParaPostulaciones($postulaciones);

                $ranking = Postulacion::query()
                    ->with('estudiante.persona')
                    ->where('id_oac_pos', $oferta->id_oac)
                    ->get()
                    ->map(fn (Postulacion $p) => (object) [
                        'postulacion' => $p,
                        'puntaje' => $puntajes[$p->id_pos] ?? 0.0,
                    ])
                    ->sortByDesc('puntaje')
                    ->values();

                Asignacion::query()->whereIn('id_pos_asi', $ranking->pluck('postulacion.id_pos')->all())->delete();
                ListaEspera::query()->where('id_oac_les', $oferta->id_oac)->delete();

                $disponibles = (int) $cupo->disponibles_cup;
                $ordenEspera = 1;

                foreach ($ranking as $index => $fila) {
                    $postulacion = $fila->postulacion;
                    $puntaje = (float) $fila->puntaje;
                    $orden = $index + 1;

                    Resultado::query()->updateOrCreate(
                        ['id_pos_res' => $postulacion->id_pos],
                        [
                            'puntaje_total_res' => $puntaje,
                            'clasificacion_res' => $orden,
                        ],
                    );

                    if ($disponibles > 0) {
                        Asignacion::query()->create([
                            'id_pos_asi' => $postulacion->id_pos,
                            'id_cup_asi' => $cupo->id_cup,
                            'estado_asi' => 'asignado',
                            'fecha_asi' => now(),
                        ]);
                        $disponibles--;
                        $stats['asignados']++;

                        if ($aprobadaEstadoId > 0) {
                            $postulacion->update(['id_ept_pos' => $aprobadaEstadoId]);
                        }
                    } else {
                        ListaEspera::query()->create([
                            'id_pos_les' => $postulacion->id_pos,
                            'id_oac_les' => $oferta->id_oac,
                            'orden_les' => $ordenEspera++,
                        ]);
                        $stats['lista_espera']++;
                    }
                }

                $cupo->update(['disponibles_cup' => $disponibles]);
            }
        });

        return $stats;
    }

    /** @return Collection<int, OfertaAcademica> */
    public function ofertasParaFiltro(int $unidadId): Collection
    {
        return OfertaAcademica::query()
            ->with(['gestion', 'curso', 'paralelo'])
            ->where('id_ued_oac', $unidadId)
            ->orderByDesc('id_ges_oac')
            ->get();
    }

    public function queryAsignacionesUnidad(int $unidadId, Request $request): Builder
    {
        $query = Asignacion::query()
            ->with([
                'cupo.ofertaAcademica.gestion',
                'cupo.ofertaAcademica.curso',
                'cupo.ofertaAcademica.paralelo',
                'postulacion.estudiante.persona',
                'postulacion.resultado',
            ])
            ->where('estado_asi', 'asignado')
            ->whereHas('postulacion.ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId));

        if ($request->filled('id_oac_pos')) {
            $query->whereHas('cupo', fn (Builder $q) => $q->where('id_oac_cup', (int) $request->input('id_oac_pos')));
        }

        return $query->orderByDesc('fecha_asi');
    }

    public function queryListaEsperaUnidad(int $unidadId, Request $request): Builder
    {
        $query = ListaEspera::query()
            ->with([
                'postulacion.estudiante.persona',
                'postulacion.resultado',
                'ofertaAcademica.gestion',
                'ofertaAcademica.curso',
                'ofertaAcademica.paralelo',
            ])
            ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId));

        if ($request->filled('id_oac_pos')) {
            $query->where('id_oac_les', (int) $request->input('id_oac_pos'));
        }

        return $query->orderBy('id_oac_les')->orderBy('orden_les');
    }
}
