<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Documento;
use App\Models\OfertaAcademica;
use App\Models\Resultado;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

final class InstitucionalExportService
{
    public function __construct(
        private readonly PostulacionInstitucionalService $postulaciones,
        private readonly ReportDownloadService $downloader,
    ) {}

    public function exportPostulaciones(int $unidadId, Request $request): BinaryFileResponse|Response
    {
        $format = $this->downloader->resolveFormat($request);
        $headers = [
            'ID', 'RUDE', 'Estudiante', 'CI', 'Curso', 'Paralelo', 'Gestión', 'Estado', 'Puntaje', 'Fecha',
        ];
        $rows = [];

        $this->postulaciones->queryParaUnidad($unidadId, $request)->chunk(200, function ($chunk) use (&$rows): void {
            foreach ($chunk as $p) {
                $est = $p->estudiante;
                $per = $est?->persona;
                $oac = $p->ofertaAcademica;
                $nombre = trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? '').' '.($per->ap_materno_per ?? ''));

                $rows[] = [
                    $p->id_pos,
                    $est->rude_est ?? '',
                    $nombre,
                    $per->ci_per ?? '',
                    $oac->curso->nombre_cur ?? '',
                    $oac->paralelo->nombre_par ?? '',
                    $oac->gestion->nombre_ges ?? '',
                    $p->estadoPostulacion->nombre_ept ?? '',
                    $p->resultado->puntaje_total_res ?? '',
                    $p->fecha_pos?->format('d/m/Y H:i') ?? '',
                ];
            }
        });

        return $this->downloader->download('Postulaciones de la unidad', $headers, $rows, 'postulaciones_ue', $format);
    }

    public function exportOfertas(int $unidadId, Request $request): BinaryFileResponse|Response
    {
        $format = $this->downloader->resolveFormat($request);
        $headers = ['ID', 'Gestión', 'Nivel', 'Curso', 'Paralelo', 'Cupos total', 'Cupos disponibles'];
        $rows = [];

        $this->queryOfertas($unidadId, $request)->chunk(100, function ($chunk) use (&$rows): void {
            foreach ($chunk as $oac) {
                $cupo = $oac->cupos->first();
                $rows[] = [
                    $oac->id_oac,
                    $oac->gestion->nombre_ges ?? '',
                    $oac->nivel->nombre_niv ?? '',
                    $oac->curso->nombre_cur ?? '',
                    $oac->paralelo->nombre_par ?? '',
                    $cupo->total_cup ?? '',
                    $cupo->disponibles_cup ?? '',
                ];
            }
        });

        return $this->downloader->download('Ofertas académicas', $headers, $rows, 'ofertas_ue', $format);
    }

    public function exportResultados(int $unidadId, Request $request): BinaryFileResponse|Response
    {
        $format = $this->downloader->resolveFormat($request);
        $headers = ['ID resultado', 'ID postulación', 'Estudiante', 'RUDE', 'Curso', 'Puntaje total', 'Estado postulación'];
        $rows = [];

        Resultado::query()
            ->with(['postulacion.estudiante.persona', 'postulacion.estadoPostulacion', 'postulacion.ofertaAcademica.curso'])
            ->whereHas('postulacion.ofertaAcademica', fn (Builder $q) => $q->where('id_ued_oac', $unidadId))
            ->when($request->filled('id_cur_oac'), function (Builder $q) use ($request): void {
                $q->whereHas('postulacion.ofertaAcademica', fn (Builder $o) => $o->where('id_cur_oac', (int) $request->input('id_cur_oac')));
            })
            ->orderByDesc('puntaje_total_res')
            ->chunk(200, function ($chunk) use (&$rows): void {
                foreach ($chunk as $res) {
                    $p = $res->postulacion;
                    $est = $p?->estudiante;
                    $per = $est?->persona;
                    $nombre = trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? '').' '.($per->ap_materno_per ?? ''));

                    $rows[] = [
                        $res->id_res,
                        $p->id_pos ?? '',
                        $nombre,
                        $est->rude_est ?? '',
                        $p->ofertaAcademica->curso->nombre_cur ?? '',
                        $res->puntaje_total_res,
                        $p->estadoPostulacion->nombre_ept ?? '',
                    ];
                }
            });

        return $this->downloader->download('Resultados de admisión', $headers, $rows, 'resultados_ue', $format);
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

    private function queryOfertas(int $unidadId, Request $request): Builder
    {
        return OfertaAcademica::query()
            ->with(['gestion', 'nivel', 'curso', 'paralelo', 'cupos'])
            ->where('id_ued_oac', $unidadId)
            ->when($request->filled('id_ges'), fn (Builder $q) => $q->where('id_ges_oac', (int) $request->input('id_ges')))
            ->when($request->filled('id_cur_oac'), fn (Builder $q) => $q->where('id_cur_oac', (int) $request->input('id_cur_oac')))
            ->orderByDesc('id_oac');
    }
}
