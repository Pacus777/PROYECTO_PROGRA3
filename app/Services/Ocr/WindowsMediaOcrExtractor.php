<?php

declare(strict_types=1);

namespace App\Services\Ocr;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

final class WindowsMediaOcrExtractor
{
    private ?bool $available = null;

    private ?string $binaryPath = null;

    public function isAvailable(): bool
    {
        if ($this->available !== null) {
            return $this->available;
        }

        if (PHP_OS_FAMILY !== 'Windows') {
            return $this->available = false;
        }

        return $this->available = $this->resolveBinary() !== null;
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
        $binary = $this->resolveBinary();

        if ($binary === null || ! is_readable($absolutePath)) {
            return ['text' => null, 'confidence' => null];
        }

        if ($binary === 'powershell-fallback') {
            $process = new Process([
                'powershell.exe',
                '-NoProfile',
                '-NonInteractive',
                '-ExecutionPolicy', 'Bypass',
                '-File', base_path('scripts/ocr-windows.ps1'),
                '-InputPath', $absolutePath,
            ], base_path(), null, null, 120);
        } elseif (str_starts_with($binary, 'dotnet ')) {
            $process = Process::fromShellCommandline($binary.' '.escapeshellarg($absolutePath), base_path(), null, null, 120);
        } else {
            $process = new Process([$binary, $absolutePath], base_path(), null, null, 120);
        }

        try {
            $process->run();

            if (! $process->isSuccessful()) {
                Log::debug('Windows OCR falló', [
                    'exit' => $process->getExitCode(),
                    'error' => $process->getErrorOutput(),
                ]);

                return ['text' => null, 'confidence' => null];
            }

            $text = trim($process->getOutput());
            if ($text === '') {
                return ['text' => null, 'confidence' => null];
            }

            return [
                'text' => $this->normalize($text),
                'confidence' => $this->estimateConfidence($text),
            ];
        } catch (\Throwable $e) {
            Log::debug('Windows OCR excepción', ['message' => $e->getMessage()]);

            return ['text' => null, 'confidence' => null];
        }
    }

    private function resolveBinary(): ?string
    {
        if ($this->binaryPath !== null) {
            return $this->binaryPath !== '' ? $this->binaryPath : null;
        }

        $configured = config('ocr.windows_binary');
        if (is_string($configured) && $configured !== '') {
            if (is_file($configured)) {
                return $this->binaryPath = $configured;
            }

            if (str_starts_with($configured, 'dotnet ') && is_dir(base_path('tools/WinOcr'))) {
                return $this->binaryPath = $configured;
            }
        }

        $published = base_path('tools/WinOcr/publish/WinOcr.exe');
        if (is_file($published)) {
            return $this->binaryPath = $published;
        }

        $debug = base_path('tools/WinOcr/bin/Release/net8.0-windows10.0.19041.0/win-x64/WinOcr.exe');
        if (is_file($debug)) {
            return $this->binaryPath = $debug;
        }

        if (is_file(base_path('tools/WinOcr/WinOcr.csproj')) && $this->findDotnet() !== null) {
            return $this->binaryPath = 'dotnet run --project '.escapeshellarg(base_path('tools/WinOcr')).' --';
        }

        $script = base_path('scripts/ocr-windows.ps1');
        if (is_file($script)) {
            return $this->binaryPath = 'powershell-fallback';
        }

        return $this->binaryPath = '';
    }

    private function findDotnet(): ?string
    {
        exec(PHP_OS_FAMILY === 'Windows' ? 'where dotnet 2>NUL' : 'which dotnet 2>/dev/null', $output, $code);

        if ($code === 0 && isset($output[0]) && trim($output[0]) !== '') {
            return trim($output[0]);
        }

        return null;
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

        return round(min(90.0, max(40.0, $ratio * 100)), 2);
    }
}
