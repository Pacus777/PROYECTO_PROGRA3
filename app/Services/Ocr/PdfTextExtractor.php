<?php

declare(strict_types=1);

namespace App\Services\Ocr;

use Smalot\PdfParser\Parser;
use Throwable;

final class PdfTextExtractor
{
    public function extract(string $absolutePath): ?string
    {
        if (! is_readable($absolutePath)) {
            return null;
        }

        try {
            $parser = new Parser;
            $pdf = $parser->parseFile($absolutePath);
            $text = trim((string) $pdf->getText());

            return $text !== '' ? $this->normalize($text) : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function normalize(string $text): string
    {
        $text = preg_replace('/\r\n|\r/', "\n", $text) ?? $text;
        $text = preg_replace("/[ \t]+/", ' ', $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        return trim($text);
    }
}
