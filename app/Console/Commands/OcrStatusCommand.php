<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Ocr\OpenAiVisionTextExtractor;
use App\Services\Ocr\TesseractTextExtractor;
use App\Services\Ocr\WindowsMediaOcrExtractor;
use Illuminate\Console\Command;

class OcrStatusCommand extends Command
{
    protected $signature = 'ocr:status';

    protected $description = 'Verifica motores OCR disponibles (Tesseract, OpenAI)';

    public function handle(TesseractTextExtractor $tesseract, WindowsMediaOcrExtractor $windowsOcr, OpenAiVisionTextExtractor $openAi): int
    {
        $this->line('OCR habilitado: '.(config('ocr.enabled') ? 'sí' : 'no'));
        $this->line('OCR sync: '.(config('ocr.sync') ? 'sí' : 'no'));
        $this->newLine();

        if ($windowsOcr->isAvailable()) {
            $this->info('Windows OCR: disponible (WinOcr nativo)');
            $resolved = $windowsOcr->resolvedBinary();
            if ($resolved) {
                $this->line('  Ruta: '.$resolved);
            }
        } else {
            $this->warn('Windows OCR: no disponible (solo Windows con scripts/ocr-windows.ps1)');
        }

        $this->newLine();

        if ($tesseract->isAvailable()) {
            $this->info('Tesseract: disponible');
            $this->line('  Ruta: '.$tesseract->resolvedBinary());
        } else {
            $this->error('Tesseract: no instalado');
            $this->line('  Instale: winget install UB-Mannheim.TesseractOCR');
            $this->line('  O defina OCR_TESSERACT_BINARY en .env');
        }

        $this->newLine();

        if ($openAi->isAvailable()) {
            $this->info('OpenAI Vision: disponible (respaldo)');
        } else {
            $this->warn('OpenAI Vision: no configurado');
            $this->line('  Defina OPENAI_API_KEY en .env para respaldo en imágenes');
        }

        return self::SUCCESS;
    }
}
