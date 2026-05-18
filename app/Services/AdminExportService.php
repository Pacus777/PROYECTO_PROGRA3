<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Postulacion;
use App\Models\Tutor;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

final class AdminExportService
{
    public function __construct(
        private readonly PostulacionNacionalService $postulaciones,
        private readonly EstudianteQueryService $estudiantes,
        private readonly UnidadEducativaQueryService $unidades,
        private readonly ReportDownloadService $downloader,
    ) {}

    public function exportPostulaciones(Request $request): BinaryFileResponse|Response
    {
        $format = $this->downloader->resolveFormat($request);
        $headers = [
            'ID', 'RUDE', 'Estudiante', 'Departamento', 'Municipio', 'Distrito educativo',
            'Unidad educativa', 'Código UE', 'Gestión', 'Curso', 'Estado', 'Fecha',
        ];
        $rows = [];

        $this->postulaciones->queryFiltrada($request)->chunk(200, function ($chunk) use (&$rows): void {
            foreach ($chunk as $p) {
                $rows[] = $this->rowPostulacion($p);
            }
        });

        return $this->downloader->download('Postulaciones', $headers, $rows, 'postulaciones', $format);
    }

    public function exportPostulantes(Request $request): BinaryFileResponse|Response
    {
        $format = $this->downloader->resolveFormat($request);
        $headers = [
            'ID', 'RUDE', 'CI', 'Nombres', 'Ap. paterno', 'Ap. materno',
            'UE matrícula', 'Código UE matrícula', 'Departamento matrícula', 'Municipio matrícula',
            'Código vínculo', 'Tutores', 'Postulaciones',
        ];
        $rows = [];

        $this->estudiantes->queryFiltrada($request)->chunk(200, function ($chunk) use (&$rows): void {
            foreach ($chunk as $est) {
                $p = $est->persona;
                $ueMat = $est->unidadMatriculaActual;
                $rows[] = [
                    $est->id_est,
                    $est->rude_est ?? '',
                    $p->ci_per ?? '',
                    $p->nombres_per ?? '',
                    $p->ap_paterno_per ?? '',
                    $p->ap_materno_per ?? '',
                    $ueMat->nombre_ued ?? '',
                    $ueMat->codigo_ued ?? '',
                    $ueMat?->municipio?->provincia?->departamento?->nombre_dep ?? '',
                    $ueMat?->municipio?->nombre_mun ?? '',
                    $est->codigo_est ?? '',
                    $est->tutores_count ?? 0,
                    $est->postulaciones_count ?? 0,
                ];
            }
        });

        return $this->downloader->download('Postulantes', $headers, $rows, 'postulantes', $format);
    }

    public function exportUnidades(Request $request): BinaryFileResponse|Response
    {
        $format = $this->downloader->resolveFormat($request);
        $headers = [
            'ID', 'Nombre', 'Código UE', 'Departamento', 'Provincia', 'Municipio',
            'Distrito educativo', 'Dirección',
        ];
        $rows = [];

        $this->unidades->queryFiltrada($request)->chunk(100, function ($chunk) use (&$rows): void {
            foreach ($chunk as $ue) {
                $rows[] = [
                    $ue->id_ued,
                    $ue->nombre_ued,
                    $ue->codigo_ued ?? '',
                    $ue->municipio?->provincia?->departamento?->nombre_dep ?? '',
                    $ue->municipio?->provincia?->nombre_prov ?? '',
                    $ue->municipio?->nombre_mun ?? '',
                    $ue->distritoEducativo?->nombre_dis ?? '',
                    $ue->direccion_ued ?? '',
                ];
            }
        });

        return $this->downloader->download('Unidades educativas', $headers, $rows, 'unidades_educativas', $format);
    }

    public function exportResumenUnidades(Request $request): BinaryFileResponse|Response
    {
        $format = $this->downloader->resolveFormat($request);
        $headers = [
            'Unidad', 'Código UE', 'Departamento', 'Municipio', 'Distrito',
            'Matriculados', 'Postulaciones', 'Ofertas',
        ];
        $rows = [];

        $this->unidades->queryFiltrada($request)
            ->withCount(['estudiantesMatriculados', 'ofertasAcademicas'])
            ->chunk(100, function ($chunk) use (&$rows): void {
                foreach ($chunk as $ue) {
                    $postCount = Postulacion::query()
                        ->whereHas('ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $ue->id_ued))
                        ->count();

                    $rows[] = [
                        $ue->nombre_ued,
                        $ue->codigo_ued ?? '',
                        $ue->municipio?->provincia?->departamento?->nombre_dep ?? '',
                        $ue->municipio?->nombre_mun ?? '',
                        $ue->distritoEducativo?->nombre_dis ?? '',
                        $ue->estudiantes_matriculados_count,
                        $postCount,
                        $ue->ofertas_academicas_count,
                    ];
                }
            });

        return $this->downloader->download('Resumen por unidad educativa', $headers, $rows, 'resumen_unidades', $format);
    }

    public function exportTutores(Request $request): BinaryFileResponse|Response
    {
        $format = $this->downloader->resolveFormat($request);
        $headers = ['ID', 'CI', 'Nombres', 'Ap. paterno', 'Ap. materno', 'Estudiantes vinculados'];
        $rows = [];

        Tutor::query()
            ->with('persona')
            ->withCount('estudiantes')
            ->when($request->filled('q'), function (Builder $q) use ($request): void {
                $term = trim((string) $request->input('q'));
                $q->whereHas('persona', function (Builder $p) use ($term): void {
                    $p->where('nombres_per', 'like', "%{$term}%")
                        ->orWhere('ap_paterno_per', 'like', "%{$term}%")
                        ->orWhere('ci_per', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('id_tut')
            ->chunk(200, function ($chunk) use (&$rows): void {
                foreach ($chunk as $tutor) {
                    $p = $tutor->persona;
                    $rows[] = [
                        $tutor->id_tut,
                        $p->ci_per ?? '',
                        $p->nombres_per ?? '',
                        $p->ap_paterno_per ?? '',
                        $p->ap_materno_per ?? '',
                        $tutor->estudiantes_count ?? 0,
                    ];
                }
            });

        return $this->downloader->download('Tutores', $headers, $rows, 'tutores', $format);
    }

    public function exportUsuarios(Request $request): BinaryFileResponse|Response
    {
        $format = $this->downloader->resolveFormat($request);
        $headers = ['ID', 'Correo', 'Rol', 'Unidad educativa', 'Activo', 'CI', 'Nombres'];
        $rows = [];

        Usuario::query()
            ->with(['persona', 'rol', 'unidadEducativa'])
            ->when($request->filled('rol'), fn (Builder $q) => $q->whereHas('rol', fn (Builder $r) => $r->where('nombre_rol', $request->input('rol'))))
            ->when($request->filled('activo'), fn (Builder $q) => $q->where('activo_usu', $request->boolean('activo')))
            ->when($request->filled('id_ued'), fn (Builder $q) => $q->where('id_ued_usu', (int) $request->input('id_ued')))
            ->when($request->filled('q'), function (Builder $q) use ($request): void {
                $term = trim((string) $request->input('q'));
                $q->where(function (Builder $w) use ($term): void {
                    $w->where('correo_usu', 'like', "%{$term}%")
                        ->orWhereHas('persona', fn (Builder $p) => $p->where('nombres_per', 'like', "%{$term}%")
                            ->orWhere('ci_per', 'like', "%{$term}%"));
                });
            })
            ->orderBy('correo_usu')
            ->chunk(200, function ($chunk) use (&$rows): void {
                foreach ($chunk as $usu) {
                    $rows[] = [
                        $usu->id_usu,
                        $usu->correo_usu,
                        $usu->rol->nombre_rol ?? '',
                        $usu->unidadEducativa->nombre_ued ?? '',
                        $usu->activo_usu ? 'Sí' : 'No',
                        $usu->persona->ci_per ?? '',
                        trim(($usu->persona->nombres_per ?? '').' '.($usu->persona->ap_paterno_per ?? '')),
                    ];
                }
            });

        return $this->downloader->download('Usuarios del sistema', $headers, $rows, 'usuarios', $format);
    }

    /**
     * @return list<string|int|float|null>
     */
    private function rowPostulacion(Postulacion $p): array
    {
        $est = $p->estudiante;
        $per = $est?->persona;
        $oac = $p->ofertaAcademica;
        $ue = $oac?->unidadEducativa;
        $nombre = trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? '').' '.($per->ap_materno_per ?? ''));
        $mun = $ue?->municipio;

        return [
            $p->id_pos,
            $est->rude_est ?? '',
            $nombre,
            $mun?->provincia?->departamento?->nombre_dep ?? '',
            $mun?->nombre_mun ?? '',
            $ue?->distritoEducativo?->nombre_dis ?? '',
            $ue->nombre_ued ?? '',
            $ue->codigo_ued ?? '',
            $oac->gestion->nombre_ges ?? '',
            $oac->curso->nombre_cur ?? '',
            $p->estadoPostulacion->nombre_ept ?? '',
            $p->fecha_pos?->format('d/m/Y H:i') ?? '',
        ];
    }
}
