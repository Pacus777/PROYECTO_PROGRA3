<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Curso;
use App\Models\Nivel;
use App\Models\OfertaAcademica;
use App\Models\Paralelo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

final class AcademicInstitucionalService
{
    /**
     * @return array{
     *     niveles: Collection<int, Nivel>,
     *     cursos: Collection<int, Curso>,
     *     paralelos: Collection<int, Paralelo>,
     *     resumen: array{ofertas_unidad: int, niveles: int, cursos: int, paralelos: int}
     * }
     */
    public function datosParaUnidad(int $unidadId): array
    {
        $niveles = Nivel::query()
            ->withCount([
                'cursos',
                'ofertasAcademicas as ofertas_unidad_count' => fn ($q) => $q->where('id_ued_oac', $unidadId),
            ])
            ->orderBy('nombre_niv')
            ->get();

        $cursos = Curso::query()
            ->with('nivel')
            ->withCount([
                'paralelos',
                'ofertasAcademicas as ofertas_unidad_count' => fn ($q) => $q->where('id_ued_oac', $unidadId),
            ])
            ->orderBy('nombre_cur')
            ->get();

        $paralelos = Paralelo::query()
            ->with('curso.nivel')
            ->withCount([
                'ofertasAcademicas as ofertas_unidad_count' => fn ($q) => $q->where('id_ued_oac', $unidadId),
            ])
            ->orderBy('nombre_par')
            ->get();

        return [
            'niveles' => $niveles,
            'cursos' => $cursos,
            'paralelos' => $paralelos,
            'resumen' => [
                'ofertas_unidad' => OfertaAcademica::query()->where('id_ued_oac', $unidadId)->count(),
                'niveles' => $niveles->count(),
                'cursos' => $cursos->count(),
                'paralelos' => $paralelos->count(),
            ],
        ];
    }

    /** @return array{ok: bool, message: string} */
    public function puedeEliminarNivel(Nivel $nivel, int $unidadId): array
    {
        $ofertasUnidad = $nivel->ofertasAcademicas()->where('id_ued_oac', $unidadId)->count();
        if ($ofertasUnidad > 0) {
            return [
                'ok' => false,
                'message' => "No se puede eliminar: el nivel tiene {$ofertasUnidad} oferta(s) en su unidad educativa.",
            ];
        }

        $ofertasGlobal = $nivel->ofertasAcademicas()->count();
        if ($ofertasGlobal > 0) {
            return [
                'ok' => false,
                'message' => 'No se puede eliminar: el nivel está usado en ofertas de otras unidades educativas.',
            ];
        }

        if ($nivel->cursos()->exists()) {
            return [
                'ok' => false,
                'message' => 'No se puede eliminar: primero elimine o reasigne los cursos de este nivel.',
            ];
        }

        return ['ok' => true, 'message' => ''];
    }

    /** @return array{ok: bool, message: string} */
    public function puedeEliminarCurso(Curso $curso, int $unidadId): array
    {
        $ofertasUnidad = $curso->ofertasAcademicas()->where('id_ued_oac', $unidadId)->count();
        if ($ofertasUnidad > 0) {
            return [
                'ok' => false,
                'message' => "No se puede eliminar: el curso tiene {$ofertasUnidad} oferta(s) en su unidad.",
            ];
        }

        if ($curso->ofertasAcademicas()->exists()) {
            return [
                'ok' => false,
                'message' => 'No se puede eliminar: el curso está usado en ofertas de otras unidades.',
            ];
        }

        if ($curso->paralelos()->exists()) {
            return [
                'ok' => false,
                'message' => 'No se puede eliminar: primero elimine los paralelos de este curso.',
            ];
        }

        return ['ok' => true, 'message' => ''];
    }

    /** @return array{ok: bool, message: string} */
    public function puedeEliminarParalelo(Paralelo $paralelo, int $unidadId): array
    {
        $ofertasUnidad = $paralelo->ofertasAcademicas()->where('id_ued_oac', $unidadId)->count();
        if ($ofertasUnidad > 0) {
            return [
                'ok' => false,
                'message' => "No se puede eliminar: el paralelo tiene {$ofertasUnidad} oferta(s) en su unidad.",
            ];
        }

        if ($paralelo->ofertasAcademicas()->exists()) {
            return [
                'ok' => false,
                'message' => 'No se puede eliminar: el paralelo está usado en ofertas de otras unidades.',
            ];
        }

        return ['ok' => true, 'message' => ''];
    }

    /** @return array{ok: bool, message: string} */
    public function puedeCambiarNivelDeCurso(Curso $curso, int $nuevoNivelId): array
    {
        if ((int) $curso->id_niv_cur === $nuevoNivelId) {
            return ['ok' => true, 'message' => ''];
        }

        if ($curso->ofertasAcademicas()->exists()) {
            return [
                'ok' => false,
                'message' => 'No puede cambiar el nivel del curso: ya tiene ofertas académicas vinculadas (debe coincidir id_niv en la oferta).',
            ];
        }

        return ['ok' => true, 'message' => ''];
    }

    /** @return array{ok: bool, message: string} */
    public function puedeCambiarCursoDeParalelo(Paralelo $paralelo, int $nuevoCursoId): array
    {
        if ((int) $paralelo->id_cur_par === $nuevoCursoId) {
            return ['ok' => true, 'message' => ''];
        }

        if ($paralelo->ofertasAcademicas()->exists()) {
            return [
                'ok' => false,
                'message' => 'No puede cambiar el curso del paralelo: ya tiene ofertas académicas vinculadas.',
            ];
        }

        return ['ok' => true, 'message' => ''];
    }

    /**
     * Catálogo agrupado Nivel → Curso → Paralelo para la vista principal.
     *
     * @return SupportCollection<int, array{nivel: Nivel, cursos: SupportCollection<int, array{curso: Curso, paralelos: SupportCollection<int, Paralelo>}>}>
     */
    public function arbolCatalogo(int $unidadId): SupportCollection
    {
        $datos = $this->datosParaUnidad($unidadId);

        return $datos['niveles']->map(function (Nivel $nivel) use ($datos): array {
            $cursos = $datos['cursos']
                ->where('id_niv_cur', $nivel->id_niv)
                ->values()
                ->map(function (Curso $curso) use ($datos): array {
                    return [
                        'curso' => $curso,
                        'paralelos' => $datos['paralelos']
                            ->where('id_cur_par', $curso->id_cur)
                            ->values(),
                    ];
                });

            return [
                'nivel' => $nivel,
                'cursos' => $cursos,
            ];
        });
    }

    /** Jerarquía usada en ofertas de la unidad (vista resumida). */
    public function arbolOfertasUnidad(int $unidadId): SupportCollection
    {
        return OfertaAcademica::query()
            ->with(['gestion', 'nivel', 'curso', 'paralelo'])
            ->where('id_ued_oac', $unidadId)
            ->orderByDesc('id_ges_oac')
            ->get()
            ->groupBy(fn (OfertaAcademica $oac) => $oac->gestion->nombre_ges ?? 'Sin gestión')
            ->map(fn (SupportCollection $grupo) => $grupo->map(fn (OfertaAcademica $oac) => [
                'nivel' => $oac->nivel->nombre_niv ?? '—',
                'curso' => $oac->curso->nombre_cur ?? '—',
                'paralelo' => $oac->paralelo->nombre_par ?? '—',
            ]));
    }
}
