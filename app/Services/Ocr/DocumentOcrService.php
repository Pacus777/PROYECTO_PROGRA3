<?php

declare(strict_types=1);

namespace App\Services\Ocr;

use App\Models\Documento;
use App\Models\ProcesamientoOcr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class DocumentOcrService
{
    public function __construct(
        private readonly PdfTextExtractor $pdf,
        private readonly TesseractTextExtractor $tesseract,
        private readonly WindowsMediaOcrExtractor $windowsOcr,
        private readonly OpenAiVisionTextExtractor $openAi,
        private readonly BoletinTextParser $boletinParser,
    ) {}

    public function process(Documento $documento): ProcesamientoOcr
    {
        $documento->loadMissing(['procesamientoOcr', 'tipoDocumento']);

        $ocr = $documento->procesamientoOcr ?? ProcesamientoOcr::query()->create([
            'id_doc_poc' => $documento->id_doc,
            'estado_poc' => 'procesando',
        ]);

        $ocr->update(['estado_poc' => 'procesando']);

        if (! config('ocr.enabled', true)) {
            return $this->markSkipped($ocr, 'OCR deshabilitado en configuración.');
        }

        $path = Storage::disk('local')->path($documento->ruta_archivo_doc);
        if (! is_file($path)) {
            return $this->markError($ocr, 'Archivo no encontrado en almacenamiento.');
        }

        $mime = $this->detectMime($path);
        $minLen = (int) config('ocr.min_text_length', 40);

        $text = null;
        $confidence = null;
        $metodo = null;

        if ($mime === 'application/pdf') {
            $text = $this->pdf->extract($path);
            $metodo = 'pdf_text';
            $confidence = $text !== null && mb_strlen($text) >= $minLen ? 92.0 : null;

            if (($text === null || mb_strlen($text) < $minLen) && $this->tesseract->isAvailable()) {
                $result = $this->tesseract->extract($path);
                if ($result['text'] !== null && mb_strlen($result['text']) >= $minLen) {
                    $text = $result['text'];
                    $confidence = $result['confidence'];
                    $metodo = 'tesseract_pdf';
                }
            }

            if ($text === null || mb_strlen($text) < $minLen) {
                $result = $this->windowsOcr->extract($path);
                if ($result['text'] !== null && mb_strlen($result['text']) >= $minLen) {
                    $text = $result['text'];
                    $confidence = $result['confidence'];
                    $metodo = 'windows_ocr_pdf';
                }
            }
        }

        if (($text === null || mb_strlen($text) < $minLen) && in_array($mime, ['image/jpeg', 'image/png', 'image/jpg'], true)) {
            if ($this->tesseract->isAvailable()) {
                $result = $this->tesseract->extract($path);
                $text = $result['text'];
                $confidence = $result['confidence'];
                $metodo = 'tesseract';
            }

            if (($text === null || mb_strlen($text) < $minLen)) {
                $result = $this->windowsOcr->extract($path);
                if ($result['text'] !== null && mb_strlen($result['text']) >= $minLen) {
                    $text = $result['text'];
                    $confidence = $result['confidence'];
                    $metodo = 'windows_ocr';
                }
            }
        }

        if (($text === null || mb_strlen($text) < $minLen) && $this->openAi->isAvailable()) {
            $result = $this->openAi->extract($path, $mime);
            if ($result['text'] !== null && mb_strlen($result['text']) >= $minLen) {
                $text = $result['text'];
                $confidence = $result['confidence'];
                $metodo = 'openai_vision';
            }
        }

        if ($text === null || mb_strlen(trim($text)) < $minLen) {
            $motores = array_filter([
                $this->tesseract->isAvailable() ? 'Tesseract' : null,
                $this->windowsOcr->isAvailable() ? 'Windows OCR' : null,
                $this->openAi->isAvailable() ? 'OpenAI Vision' : null,
            ]);

            $hint = $mime === 'application/pdf'
                ? 'PDF escaneado: instale Tesseract, use Windows 10/11 con OCR habilitado, o configure OPENAI_API_KEY.'
                : 'Instale Tesseract OCR, use Windows OCR, o configure OPENAI_API_KEY.';

            if ($motores === []) {
                $hint = 'Sin motor OCR disponible. En Windows 10/11 se usa OCR nativo; también puede instalar Tesseract o configurar OPENAI_API_KEY.';
            }

            return $this->markError($ocr, 'No se pudo extraer texto suficiente. '.$hint);
        }

        $parseStats = $this->boletinParser->parseAndPersist($documento, $text);

        $ocr->update([
            'texto_extraido_poc' => $text,
            'confianza_poc' => $confidence,
            'estado_poc' => 'completado',
        ]);

        Log::info('OCR completado', [
            'documento_id' => $documento->id_doc,
            'metodo' => $metodo,
            'caracteres' => mb_strlen($text),
        ]);

        return $ocr->fresh();
    }

    public function dispatchProcessing(Documento $documento): void
    {
        if (! config('ocr.enabled', true)) {
            return;
        }

        if (config('ocr.sync', false)) {
            $this->process($documento);

            return;
        }

        \App\Jobs\ProcessDocumentOcrJob::dispatch($documento->id_doc);
    }

    private function detectMime(string $path): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $path) : false;
        if ($finfo) {
            finfo_close($finfo);
        }

        return is_string($mime) && $mime !== '' ? $mime : 'application/octet-stream';
    }

    private function markError(ProcesamientoOcr $ocr, string $mensaje): ProcesamientoOcr
    {
        $ocr->update([
            'estado_poc' => 'error',
            'texto_extraido_poc' => $mensaje,
            'confianza_poc' => null,
        ]);

        return $ocr->fresh();
    }

    private function markSkipped(ProcesamientoOcr $ocr, string $mensaje): ProcesamientoOcr
    {
        $ocr->update([
            'estado_poc' => 'omitido',
            'texto_extraido_poc' => $mensaje,
            'confianza_poc' => null,
        ]);

        return $ocr->fresh();
    }
}
