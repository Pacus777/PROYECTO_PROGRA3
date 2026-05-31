<?php

declare(strict_types=1);

namespace App\Services\Ocr;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

final class OpenAiVisionTextExtractor
{
    public function isAvailable(): bool
    {
        $key = config('services.openai.key');

        return is_string($key) && $key !== '' && config('ocr.openai_fallback', true);
    }

    /**
     * @return array{text: string|null, confidence: float|null}
     */
    public function extract(string $absolutePath, string $mime): array
    {
        if (! $this->isAvailable() || ! is_readable($absolutePath)) {
            return ['text' => null, 'confidence' => null];
        }

        $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        if (! in_array($mime, $allowed, true)) {
            return ['text' => null, 'confidence' => null];
        }

        try {
            $bytes = file_get_contents($absolutePath);
            if ($bytes === false) {
                return ['text' => null, 'confidence' => null];
            }

            $dataUri = 'data:'.$mime.';base64,'.base64_encode($bytes);

            $response = Http::withToken((string) config('services.openai.key'))
                ->timeout(90)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('ocr.openai_model', 'gpt-4o-mini'),
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => 'Transcribe todo el texto visible de este documento escolar (boletín de notas, certificado, etc.). '
                                        .'Conserva nombres de materias, notas numéricas y promedios. '
                                        .'Responde solo con el texto transcrito, sin comentarios.',
                                ],
                                [
                                    'type' => 'image_url',
                                    'image_url' => ['url' => $dataUri],
                                ],
                            ],
                        ],
                    ],
                    'max_tokens' => 4096,
                ]);

            if (! $response->successful()) {
                Log::warning('OCR OpenAI Vision falló', ['status' => $response->status()]);

                return ['text' => null, 'confidence' => null];
            }

            $text = trim((string) data_get($response->json(), 'choices.0.message.content', ''));

            if ($text === '') {
                return ['text' => null, 'confidence' => null];
            }

            return ['text' => $text, 'confidence' => 88.0];
        } catch (Throwable $e) {
            Log::warning('OCR OpenAI Vision excepción', ['message' => $e->getMessage()]);

            return ['text' => null, 'confidence' => null];
        }
    }
}
