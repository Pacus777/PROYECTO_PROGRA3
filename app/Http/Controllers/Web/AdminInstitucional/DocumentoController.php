<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AdminInstitucional;

use App\Models\Documento;
use App\Models\DetalleBoletin;
use App\Models\ResumenBoletin;
use App\Services\DocumentoService;
use App\Services\Ocr\BoletinLayoutParser;
use App\Services\Ocr\DocumentOcrService;
use App\Services\Ocr\OpenAiVisionTextExtractor;
use App\Services\Ocr\TesseractTextExtractor;
use App\Services\Ocr\WindowsMediaOcrExtractor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentoController extends BaseInstitutionalController
{
    public function __construct(
        private readonly DocumentoService $service,
        private readonly DocumentOcrService $ocrService,
        private readonly BoletinLayoutParser $boletinLayout,
    ) {}

    public function index(Request $request): View
    {
        $unidadId = $this->unidadId($request);

        $estado = $request->query('estado');

        $query = Documento::query()
            ->whereHas('postulacion.ofertaAcademica', fn ($q) => $q->where('id_ued_oac', $unidadId))
            ->with([
                'postulacion.estudiante.persona',
                'postulacion.ofertaAcademica.curso',
                'postulacion.ofertaAcademica.paralelo',
                'tipoDocumento',
                'procesamientoOcr',
            ])
            ->orderByDesc('id_doc');

        if (in_array($estado, ['pendiente', 'verificado', 'observado', 'rechazado'], true)) {
            $query->where('estado_doc', $estado);
        }

        $documentos = $query->paginate(20)->withQueryString();

        return view('admin.institucional.documentos.index', [
            'documentos' => $documentos,
            'estado' => $estado,
            'ocrMotores' => [
                'windows' => app(WindowsMediaOcrExtractor::class)->isAvailable(),
                'tesseract' => app(TesseractTextExtractor::class)->isAvailable(),
                'openai' => app(OpenAiVisionTextExtractor::class)->isAvailable(),
            ],
        ]);
    }

    public function updateEstado(Request $request, Documento $documento): RedirectResponse
    {
        $validated = $request->validate([
            'estado_doc' => ['required', Rule::in(['pendiente', 'verificado', 'observado', 'rechazado'])],
            'observacion_doc' => ['nullable', 'string', 'max:2000', 'required_if:estado_doc,observado,rechazado'],
        ], [
            'observacion_doc.required_if' => 'Debe escribir una observación cuando el documento sea observado o rechazado.',
        ]);

        $this->assertDocumentoBelongsToUnidad($documento, $this->unidadId($request));

        $estadoAnterior = $documento->estado_doc;
        $this->service->updateEstado(
            $documento,
            $validated['estado_doc'],
            $validated['observacion_doc'] ?? null,
        );
        $documento->refresh()->load('tipoDocumento', 'postulacion.estudiante.persona');

        $this->registrarActividad($request, 'documento', (int) $documento->id_doc, 'documento_estado', [
            'descripcion' => 'Documento '.($documento->tipoDocumento->nombre_tdo ?? '—').': '.$estadoAnterior.' → '.$documento->estado_doc,
            'url' => route('admin.institucional.documentos.index'),
        ]);

        return back()->with('success', 'Estado actualizado.');
    }

    public function show(Request $request, Documento $documento): View
    {
        $this->assertDocumentoBelongsToUnidad($documento, $this->unidadId($request));

        $documento->load([
            'postulacion.estudiante.persona',
            'postulacion.ofertaAcademica.curso',
            'postulacion.ofertaAcademica.paralelo',
            'postulacion.ofertaAcademica.gestion',
            'tipoDocumento',
            'procesamientoOcr',
        ]);

        $estudianteId = (int) ($documento->postulacion?->id_est_pos ?? 0);
        $gestionId = $documento->postulacion?->ofertaAcademica?->id_ges_oac;

        $detalleBoletin = $estudianteId > 0
            ? DetalleBoletin::query()
                ->where('id_est_dbo', $estudianteId)
                ->when($gestionId, fn ($q) => $q->where('id_ges_dbo', $gestionId))
                ->orderBy('materia_dbo')
                ->get()
            : collect();

        $resumenBoletin = ($estudianteId > 0 && $gestionId)
            ? ResumenBoletin::query()
                ->where('id_est_rbo', $estudianteId)
                ->where('id_ges_rbo', $gestionId)
                ->first()
            : null;

        $boletinVista = null;
        $textoOcr = $documento->procesamientoOcr?->texto_extraido_poc;
        if (is_string($textoOcr) && $textoOcr !== '' && ($documento->procesamientoOcr?->estado_poc ?? '') === 'completado') {
            $boletinVista = $this->boletinLayout->parse($textoOcr);
        }

        return view('admin.institucional.documentos.show', compact('documento', 'detalleBoletin', 'resumenBoletin', 'boletinVista'));
    }

    public function reprocessOcr(Request $request, Documento $documento): RedirectResponse
    {
        $this->assertDocumentoBelongsToUnidad($documento, $this->unidadId($request));

        $ocr = $this->ocrService->process($documento->fresh());

        $mensaje = ($ocr->estado_poc ?? '') === 'completado'
            ? 'OCR completado. Actualice la página para ver el texto y la vista en tabla.'
            : 'OCR finalizado con estado: '.($ocr->estado_poc ?? 'desconocido').'. Revise el detalle del documento.';

        return back()->with(($ocr->estado_poc ?? '') === 'completado' ? 'success' : 'error', $mensaje);
    }

    public function download(Request $request, Documento $documento): BinaryFileResponse
    {
        $this->assertDocumentoBelongsToUnidad($documento, $this->unidadId($request));

        $path = $this->service->diskPath($documento);

        abort_unless(file_exists($path), 404, 'Archivo no encontrado.');

        return response()->download($path);
    }

    private function assertDocumentoBelongsToUnidad(Documento $documento, int $unidadId): void
    {
        $documento->loadMissing('postulacion.ofertaAcademica');

        abort_unless(
            (int) $documento->postulacion?->ofertaAcademica?->id_ued_oac === $unidadId,
            403,
        );
    }
}
