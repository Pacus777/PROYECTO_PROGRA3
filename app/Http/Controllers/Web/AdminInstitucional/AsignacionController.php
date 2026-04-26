<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Models\Asignacion;
use App\Models\EstadoPostulacion;
use App\Models\ListaEspera;
use App\Models\OfertaAcademica;
use App\Models\Postulacion;
use App\Models\Resultado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsignacionController extends BaseInstitutionalController
{
    public function store(Request $request): RedirectResponse
    {
        $unidadId = $this->unidadId($request);

        $aprobadoEstadoId = (int) EstadoPostulacion::query()
            ->whereRaw('LOWER(nombre_ept) = ?', ['aprobado'])
            ->value('id_ept');

        DB::transaction(function () use ($unidadId, $aprobadoEstadoId): void {
            $ofertas = OfertaAcademica::query()
                ->with('cupos')
                ->where('id_ued_oac', $unidadId)
                ->get();

            foreach ($ofertas as $oferta) {
                $cupo = $oferta->cupos()->first();
                if (! $cupo) {
                    continue;
                }

                $ranking = Postulacion::query()
                    ->select('postulacion.*')
                    ->selectRaw('COALESCE(SUM(evaluacion.puntaje_eva * COALESCE(criterio.peso_cri, 1)), 0) as puntaje_total')
                    ->leftJoin('evaluacion', 'evaluacion.id_pos_eva', '=', 'postulacion.id_pos')
                    ->leftJoin('criterio', 'criterio.id_cri', '=', 'evaluacion.id_cri_eva')
                    ->where('postulacion.id_oac_pos', $oferta->id_oac)
                    ->groupBy('postulacion.id_pos')
                    ->orderByDesc('puntaje_total')
                    ->orderBy('postulacion.id_pos')
                    ->get();

                Asignacion::query()->whereIn('id_pos_asi', $ranking->pluck('id_pos')->all())->delete();
                ListaEspera::query()->where('id_oac_les', $oferta->id_oac)->delete();

                $disponibles = (int) $cupo->disponibles_cup;
                $ordenEspera = 1;

                foreach ($ranking as $index => $postulacion) {
                    $puntaje = (float) $postulacion->puntaje_total;
                    Resultado::query()->updateOrCreate(
                        ['id_pos_res' => $postulacion->id_pos],
                        [
                            'puntaje_total_res' => $puntaje,
                            'clasificacion_res' => $index + 1,
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

                        if ($aprobadoEstadoId > 0) {
                            Postulacion::query()
                                ->where('id_pos', $postulacion->id_pos)
                                ->update(['id_ept_pos' => $aprobadoEstadoId]);
                        }
                    } else {
                        ListaEspera::query()->create([
                            'id_pos_les' => $postulacion->id_pos,
                            'id_oac_les' => $oferta->id_oac,
                            'orden_les' => $ordenEspera++,
                        ]);
                    }
                }

                $cupo->update(['disponibles_cup' => $disponibles]);
            }
        });

        return back()->with('success', 'Asignación ejecutada correctamente. Se actualizó ranking, cupos y lista de espera.');
    }
}

