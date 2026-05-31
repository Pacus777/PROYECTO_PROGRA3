<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Documento;
use App\Services\Ocr\DocumentOcrService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessDocumentOcrJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 120;

    public function __construct(
        public readonly int $documentoId,
    ) {}

    public function handle(DocumentOcrService $ocrService): void
    {
        $documento = Documento::query()->find($this->documentoId);

        if ($documento === null) {
            return;
        }

        $ocrService->process($documento);
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('ProcessDocumentOcrJob falló', [
            'documento_id' => $this->documentoId,
            'message' => $exception?->getMessage(),
        ]);
    }
}
