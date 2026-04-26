<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Models\Curso;
use App\Models\EstadoPostulacion;
use App\Models\Postulacion;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PostulacionController extends BaseInstitutionalController
{
    public function index(Request $request): View
    {
        $unidadId = $this->unidadId($request);

        $query = Postulacion::query()
            ->with([
                'estadoPostulacion',
                'resultado',
                'ofertaAcademica.curso',
                'estudiante.persona',
            ])
            ->whereHas('ofertaAcademica', function ($q) use ($unidadId): void {
                $q->where('id_ued_oac', $unidadId);
            });

        if ($request->filled('id_ept_pos')) {
            $query->where('id_ept_pos', (int) $request->input('id_ept_pos'));
        }
        if ($request->filled('id_cur_oac')) {
            $cursoId = (int) $request->input('id_cur_oac');
            $query->whereHas('ofertaAcademica', fn ($q) => $q->where('id_cur_oac', $cursoId));
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_pos', '>=', $request->input('fecha_desde'));
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_pos', '<=', $request->input('fecha_hasta'));
        }

        $postulaciones = $query->orderByDesc('id_pos')->paginate(20)->withQueryString();

        $estados = EstadoPostulacion::query()->orderBy('nombre_ept')->get();
        $cursos = Curso::query()->orderBy('nombre_cur')->get();

        return view('admin.institucional.postulaciones.index', compact('postulaciones', 'estados', 'cursos'));
    }

    public function show(Request $request, Postulacion $postulacion): View
    {
        $unidadId = $this->unidadId($request);
        $postulacion->load([
            'estadoPostulacion',
            'ofertaAcademica.gestion',
            'ofertaAcademica.nivel',
            'ofertaAcademica.curso',
            'ofertaAcademica.paralelo',
            'estudiante.persona',
            'evaluaciones.criterio',
            'resultado',
            'asignaciones.cupo',
            'listasEspera',
        ]);
        $this->assertPostulacionBelongsToUnidad($postulacion, $unidadId);

        return view('admin.institucional.postulaciones.show', compact('postulacion'));
    }
}

