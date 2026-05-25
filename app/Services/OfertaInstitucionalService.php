<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Curso;
use App\Models\Gestion;
use App\Models\Nivel;
use App\Models\OfertaAcademica;
use App\Models\Paralelo;
use App\Models\Postulacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

final class OfertaInstitucionalService
{
    public function queryParaUnidad(int $unidadId, Request $request): Builder
    {
        $query = OfertaAcademica::query()
            ->with(['gestion', 'nivel', 'curso', 'paralelo', 'cupos', 'tiposDocumentoRequeridos'])
            ->withCount('postulaciones')
            ->where('id_ued_oac', $unidadId);

        if ($request->filled('id_ges_oac')) {
            $query->where('id_ges_oac', (int) $request->input('id_ges_oac'));
        }

        if ($request->filled('id_niv_oac')) {
            $query->where('id_niv_oac', (int) $request->input('id_niv_oac'));
        }

        if ($request->filled('id_cur_oac')) {
            $query->where('id_cur_oac', (int) $request->input('id_cur_oac'));
        }

        return $query->orderByDesc('id_ges_oac')->orderBy('id_niv_oac')->orderBy('id_cur_oac');
    }

    /** @return array{gestiones: Collection, niveles: Collection, cursos: Collection, paralelos: Collection} */
    public function catalogosAcademicos(): array
    {
        return [
            'gestiones' => Gestion::query()->orderByDesc('id_ges')->get(),
            'niveles' => Nivel::query()->orderBy('nombre_niv')->get(),
            'cursos' => Curso::query()->with('nivel')->orderBy('nombre_cur')->get(),
            'paralelos' => Paralelo::query()->with('curso.nivel')->orderBy('nombre_par')->get(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function existeCombinacionDuplicada(int $unidadId, array $data, ?int $excluirIdOac = null): bool
    {
        $query = OfertaAcademica::query()
            ->where('id_ued_oac', $unidadId)
            ->where('id_ges_oac', (int) $data['id_ges_oac'])
            ->where('id_niv_oac', (int) $data['id_niv_oac'])
            ->where('id_cur_oac', (int) $data['id_cur_oac'])
            ->where('id_par_oac', (int) $data['id_par_oac']);

        if ($excluirIdOac !== null) {
            $query->where('id_oac', '!=', $excluirIdOac);
        }

        return $query->exists();
    }

    /**
     * Alinea id_niv_oac con el nivel del curso (regla BD + trigger PostgreSQL).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function normalizarDatosOferta(array $data): array
    {
        $curso = Curso::query()->find((int) ($data['id_cur_oac'] ?? 0));
        if ($curso !== null) {
            $data['id_niv_oac'] = (int) $curso->id_niv_cur;
        }

        $gestion = Gestion::query()->find((int) ($data['id_ges_oac'] ?? 0));
        if ($gestion !== null) {
            $data['fecha_inicio_postulacion_oac'] = $gestion->fecha_inicio_postulacion_ges;
            $data['fecha_fin_postulacion_oac'] = $gestion->fecha_fin_postulacion_ges;
        }

        return $data;
    }

    /** @return array{ok: bool, message: string} */
    public function validarCoherenciaAcademica(array $data): array
    {
        $curso = Curso::query()->find((int) ($data['id_cur_oac'] ?? 0));
        if ($curso === null) {
            return ['ok' => false, 'message' => 'Curso no válido.'];
        }

        if ((int) $curso->id_niv_cur !== (int) ($data['id_niv_oac'] ?? 0)) {
            return ['ok' => false, 'message' => 'El nivel debe coincidir con el del curso seleccionado.'];
        }

        $paralelo = Paralelo::query()->find((int) ($data['id_par_oac'] ?? 0));
        if ($paralelo === null) {
            return ['ok' => false, 'message' => 'Paralelo no válido.'];
        }

        if ((int) $paralelo->id_cur_par !== (int) $data['id_cur_oac']) {
            return ['ok' => false, 'message' => 'El paralelo debe pertenecer al curso seleccionado.'];
        }

        return ['ok' => true, 'message' => ''];
    }

    /** @return array{ok: bool, message: string} */
    public function puedeEliminar(OfertaAcademica $oferta): array
    {
        $postulaciones = Postulacion::query()->where('id_oac_pos', $oferta->id_oac)->count();
        if ($postulaciones > 0) {
            return [
                'ok' => false,
                'message' => "No se puede eliminar: hay {$postulaciones} postulación(es) vinculada(s).",
            ];
        }

        return ['ok' => true, 'message' => ''];
    }

    /**
     * @return array{total: int, con_cupo: int, postulaciones: int, cupos_disponibles: int}
     */
    public function resumenParaUnidad(int $unidadId, Request $request): array
    {
        $base = $this->queryParaUnidad($unidadId, $request);

        $ofertas = (clone $base)->get();
        $conCupo = $ofertas->filter(fn (OfertaAcademica $o) => $o->cupos->isNotEmpty())->count();
        $disponibles = $ofertas->sum(fn (OfertaAcademica $o) => (int) ($o->cupos->first()?->disponibles_cup ?? 0));

        return [
            'total' => $ofertas->count(),
            'con_cupo' => $conCupo,
            'postulaciones' => (int) Postulacion::query()
                ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
                ->count(),
            'cupos_disponibles' => $disponibles,
        ];
    }
}
