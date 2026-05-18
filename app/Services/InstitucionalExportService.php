<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Documento;
use App\Models\OfertaAcademica;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

final class InstitucionalExportService
{
    public function __construct(
        private readonly PostulacionInstitucionalService $postulaciones,
        private readonly OfertaInstitucionalService $ofertas,
        private readonly ResultadoInstitucionalService $resultados,
        private readonly ListaEsperaInstitucionalService $listaEspera,
        private readonly HistorialInstitucionalService $historial,
        private readonly ReportDownloadService $downloader,
    ) {}

    public function exportPostulaciones(int $unidadId, Request $request): BinaryFileResponse|Response
    {
        $format = $this->downloader->resolveFormat($request);
        $headers = [
            'RUDE', 'Estudiante', 'CI', 'Curso', 'Paralelo', 'Gestión', 'Estado', 'Puntaje', 'Fecha', 'Observaciones',
        ];
        $rows = [];

        $this->postulaciones->queryParaUnidad($unidadId, $request)->chunk(200, function ($chunk) use (&$rows): void {
            foreach ($chunk as $p) {
                $est = $p->estudiante;
                $per = $est?->persona;
                $oac = $p->ofertaAcademica;
                $nombre = trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? '').' '.($per->ap_materno_per ?? ''));

                $rows[] = [
                    $est->rude_est ?? '',
                    $nombre,
                    $per->ci_per ?? '',
                    $oac->curso->nombre_cur ?? '',
                    $oac->paralelo->nombre_par ?? '',
                    $oac->gestion->nombre_ges ?? '',
                    $p->estadoPostulacion->nombre_ept ?? '',
                    $p->resultado->puntaje_total_res ?? '',
                    $p->fecha_pos?->format('d/m/Y H:i') ?? '',
                    $p->observaciones_pos ?? '',
                ];
            }
        });

        return $this->downloader->download('Postulaciones de la unidad', $headers, $rows, 'postulaciones_ue', $format);
    }

    public function exportOfertas(int $unidadId, Request $request): BinaryFileResponse|Response
    {
        $format = $this->downloader->resolveFormat($request);
        $headers = ['Gestión', 'Nivel', 'Curso', 'Paralelo', 'Descripción', 'Cupos total', 'Cupos disponibles', 'Postulaciones'];
        $rows = [];

        $this->ofertas->queryParaUnidad($unidadId, $request)->chunk(100, function ($chunk) use (&$rows): void {
            foreach ($chunk as $oac) {
                $cupo = $oac->cupos->first();
                $rows[] = [
                    $oac->gestion->nombre_ges ?? '',
                    $oac->nivel->nombre_niv ?? '',
                    $oac->curso->nombre_cur ?? '',
                    $oac->paralelo->nombre_par ?? '',
                    $oac->descripcion_oac ?? '',
                    $cupo->total_cup ?? '',
                    $cupo->disponibles_cup ?? '',
                    $oac->postulaciones_count ?? 0,
                ];
            }
        });

        return $this->downloader->download('Ofertas académicas', $headers, $rows, 'ofertas_ue', $format);
    }

    public function exportResultados(int $unidadId, Request $request): BinaryFileResponse|Response
    {
        $format = $this->downloader->resolveFormat($request);
        $headers = [
            'Orden oferta', 'Estudiante', 'RUDE', 'Gestión', 'Curso', 'Paralelo',
            'Puntaje calculado', 'Puntaje guardado', 'Clasificación', 'Estado postulación', 'Asignación',
        ];
        $rows = [];

        foreach ($this->resultados->filasRanking($unidadId, $request) as $fila) {
            $p = $fila->postulacion;
            $est = $p->estudiante;
            $per = $est?->persona;
            $oac = $p->ofertaAcademica;
            $res = $p->resultado;
            $nombre = trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? '').' '.($per->ap_materno_per ?? ''));

            $asignacion = 'Sin asignar';
            if ($p->asignaciones->where('estado_asi', 'asignado')->isNotEmpty()) {
                $asignacion = 'Asignado';
            } elseif ($p->listasEspera->isNotEmpty()) {
                $asignacion = 'Lista espera #'.$p->listasEspera->first()->orden_les;
            }

            $rows[] = [
                $fila->orden_oferta,
                $nombre,
                $est->rude_est ?? '',
                $oac->gestion->nombre_ges ?? '',
                $oac->curso->nombre_cur ?? '',
                $oac->paralelo->nombre_par ?? '',
                number_format($fila->puntaje, 2),
                $res ? number_format((float) $res->puntaje_total_res, 2) : '',
                $res->clasificacion_res ?? '',
                $p->estadoPostulacion->nombre_ept ?? '',
                $asignacion,
            ];
        }

        return $this->downloader->download('Resultados de admisión', $headers, $rows, 'resultados_ue', $format);
    }

    public function exportListaEspera(int $unidadId, Request $request): BinaryFileResponse|Response
    {
        $format = $this->downloader->resolveFormat($request);
        $headers = [
            'Orden', 'Estudiante', 'RUDE', 'Gestión', 'Curso', 'Paralelo',
            'Puntaje', 'Estado postulación', 'Cupos libres oferta',
        ];
        $rows = [];

        $this->listaEspera->queryParaUnidad($unidadId, $request)->chunk(200, function ($chunk) use (&$rows): void {
            foreach ($chunk as $les) {
                $p = $les->postulacion;
                $est = $p?->estudiante;
                $per = $est?->persona;
                $oac = $les->ofertaAcademica;
                $nombre = trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? '').' '.($per->ap_materno_per ?? ''));

                $rows[] = [
                    $les->orden_les,
                    $nombre,
                    $est->rude_est ?? '',
                    $oac->gestion->nombre_ges ?? '',
                    $oac->curso->nombre_cur ?? '',
                    $oac->paralelo->nombre_par ?? '',
                    $p?->resultado ? number_format((float) $p->resultado->puntaje_total_res, 2) : '',
                    $p->estadoPostulacion->nombre_ept ?? '',
                    $oac->cupos->first()->disponibles_cup ?? 0,
                ];
            }
        });

        return $this->downloader->download('Lista de espera', $headers, $rows, 'lista_espera_ue', $format);
    }

    public function exportHistorial(int $unidadId, Request $request): BinaryFileResponse|Response
    {
        $format = $this->downloader->resolveFormat($request);
        $headers = ['Fecha', 'Módulo', 'Acción', 'Descripción', 'Usuario', 'Origen'];
        $rows = [];

        foreach ($this->historial->construirTimeline($unidadId, $request) as $evento) {
            $rows[] = [
                $evento->fecha->format('d/m/Y H:i'),
                $evento->modulo_label,
                $evento->accion_label,
                $evento->descripcion,
                $evento->usuario ?? '—',
                $evento->origen === 'auditoria' ? 'Auditoría' : 'Sistema',
            ];
        }

        return $this->downloader->download('Historial de actividad', $headers, $rows, 'historial_ue', $format);
    }

    public function exportDocumentos(int $unidadId, Request $request): BinaryFileResponse|Response
    {
        $format = $this->downloader->resolveFormat($request);
        $headers = ['ID', 'Estudiante', 'RUDE', 'Tipo', 'Estado', 'Postulación', 'Fecha carga'];
        $rows = [];

        $query = Documento::query()
            ->with(['postulacion.estudiante.persona', 'tipoDocumento'])
            ->whereHas('postulacion.ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
            ->orderByDesc('id_doc');

        $estado = $request->query('estado');
        if (in_array($estado, ['pendiente', 'verificado', 'rechazado'], true)) {
            $query->where('estado_doc', $estado);
        }

        $query->chunk(200, function ($chunk) use (&$rows): void {
            foreach ($chunk as $doc) {
                $est = $doc->postulacion?->estudiante;
                $per = $est?->persona;
                $nombre = trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? ''));

                $rows[] = [
                    $doc->id_doc,
                    $nombre,
                    $est->rude_est ?? '',
                    $doc->tipoDocumento->nombre_tdo ?? '',
                    $doc->estado_doc ?? '',
                    $doc->id_pos_doc ?? '',
                    $doc->created_at?->format('d/m/Y H:i') ?? '',
                ];
            }
        });

        return $this->downloader->download('Documentos de postulación', $headers, $rows, 'documentos_ue', $format);
    }

    public function exportAsignaciones(int $unidadId, Request $request): BinaryFileResponse|Response
    {
        $format = $this->downloader->resolveFormat($request);
        $headers = ['Estudiante', 'RUDE', 'Gestión', 'Curso', 'Paralelo', 'Puntaje', 'Fecha asignación'];
        $rows = [];

        $this->resultados->queryAsignacionesUnidad($unidadId, $request)->chunk(200, function ($chunk) use (&$rows): void {
            foreach ($chunk as $asi) {
                $p = $asi->postulacion;
                $per = $p?->estudiante?->persona;
                $oac = $asi->cupo?->ofertaAcademica;
                $nombre = trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? '').' '.($per->ap_materno_per ?? ''));

                $rows[] = [
                    $nombre,
                    $p->estudiante->rude_est ?? '',
                    $oac->gestion->nombre_ges ?? '',
                    $oac->curso->nombre_cur ?? '',
                    $oac->paralelo->nombre_par ?? '',
                    $p?->resultado ? number_format((float) $p->resultado->puntaje_total_res, 2) : '',
                    $asi->fecha_asi?->format('d/m/Y H:i') ?? '',
                ];
            }
        });

        return $this->downloader->download('Cupos asignados', $headers, $rows, 'asignaciones_ue', $format);
    }

    public function exportResumenAdmision(int $unidadId, Request $request): BinaryFileResponse|Response
    {
        $format = $this->downloader->resolveFormat($request);
        $reportes = app(InstitucionalReporteService::class);
        $headers = [
            'Gestión', 'Nivel', 'Curso', 'Paralelo', 'Postulaciones',
            'Cupos total', 'Cupos disponibles', 'Cupos asignados', 'Lista de espera',
        ];
        $rows = [];

        foreach ($reportes->filasResumenPorOferta($unidadId) as $fila) {
            $rows[] = [
                $fila->gestion,
                $fila->nivel,
                $fila->curso,
                $fila->paralelo,
                $fila->postulaciones,
                $fila->cupos_total,
                $fila->cupos_disponibles,
                $fila->cupos_asignados,
                $fila->lista_espera,
            ];
        }

        return $this->downloader->download('Resumen de admisión por oferta', $headers, $rows, 'resumen_admision_ue', $format);
    }

}
