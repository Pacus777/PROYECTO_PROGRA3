<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Documento;
use App\Models\ProcesamientoOcr;
use App\Services\Ocr\DocumentOcrService;
use Illuminate\Console\Command;

class ProcessPendingOcrCommand extends Command
{
    protected $signature = 'ocr:process-pending {--limit=50 : Máximo de documentos a procesar}';

    protected $description = 'Procesa OCR de documentos con estado pendiente o error';

    public function handle(DocumentOcrService $ocrService): int
    {
        $limit = max(1, (int) $this->option('limit'));

        $ids = ProcesamientoOcr::query()
            ->whereIn('estado_poc', ['pendiente', 'error'])
            ->orderBy('id_poc')
            ->limit($limit)
            ->pluck('id_doc_poc');

        if ($ids->isEmpty()) {
            $this->info('No hay documentos pendientes de OCR.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($ids->count());
        $bar->start();

        foreach ($ids as $docId) {
            $documento = Documento::query()->find($docId);
            if ($documento) {
                $ocrService->process($documento);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('OCR finalizado.');

        return self::SUCCESS;
    }
}
