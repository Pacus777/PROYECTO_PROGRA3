<?php

declare(strict_types=1);

namespace App\Services\Ocr;

use thiagoalessio\TesseractOCR\TesseractOCR;
use Throwable;

final class TesseractTextExtractor
{
    /** @var list<string> */
    private const WINDOWS_CANDIDATES = [
        'C:\\Program Files\\Tesseract-OCR\\tesseract.exe',
        'C:\\Program Files (x86)\\Tesseract-OCR\\tesseract.exe',
    ];

    public function isAvailable(): bool
    {
        return $this->resolveBinary() !== null;
    }

    public function resolvedBinary(): ?string
    {
        return $this->resolveBinary();
    }

    /**
     * @return array{text: string|null, confidence: float|null}
     */
    public function extract(string $absolutePath): array
    {
        if (! is_readable($absolutePath)) {
            return ['text' => null, 'confidence' => null];
        }

        $binary = $this->resolveBinary();
        if ($binary === null) {
            return ['text' => null, 'confidence' => null];
        }

        foreach ($this->languageCandidates() as $lang) {
            try {
                $ocr = new TesseractOCR($absolutePath);
                $ocr->executable($binary);
                $ocr->lang($lang);

                $text = trim($ocr->run());

                if ($text !== '') {
                    return [
                        'text' => $this->normalize($text),
                        'confidence' => $this->estimateConfidence($text),
                    ];
                }
            } catch (Throwable) {
                continue;
            }
        }

        return ['text' => null, 'confidence' => null];
    }

    private function resolveBinary(): ?string
    {
        $configured = config('ocr.tesseract_binary');
        if (is_string($configured) && $configured !== '' && is_file($configured)) {
            return $configured;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            foreach (self::WINDOWS_CANDIDATES as $path) {
                if (is_file($path)) {
                    return $path;
                }
            }
        }

        $which = PHP_OS_FAMILY === 'Windows' ? 'where tesseract 2>NUL' : 'which tesseract 2>/dev/null';
        exec($which, $output, $exitCode);

        if ($exitCode === 0 && isset($output[0]) && is_file(trim($output[0]))) {
            return trim($output[0]);
        }

        return null;
    }

    /** @return list<string> */
    private function languageCandidates(): array
    {
        $primary = (string) config('ocr.tesseract_lang', 'spa');
        $candidates = array_values(array_unique([$primary, 'spa+eng', 'eng']));

        return $candidates;
    }

    private function normalize(string $text): string
    {
        $text = preg_replace('/\r\n|\r/', "\n", $text) ?? $text;
        $text = preg_replace("/[ \t]+/", ' ', $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        return trim($text);
    }

    private function estimateConfidence(string $text): float
    {
        $len = mb_strlen($text);
        $alnum = preg_match_all('/[\p{L}\p{N}]/u', $text) ?: 0;
        $ratio = $len > 0 ? ($alnum / $len) : 0;

        return round(min(95.0, max(35.0, $ratio * 100)), 2);
    }
}
