<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | OCR habilitado
    |--------------------------------------------------------------------------
    */
    'enabled' => env('OCR_ENABLED', true),

    /*
    | Procesar en la misma petición (útil en desarrollo sin queue worker).
    */
    'sync' => env('OCR_SYNC', false),

    /*
    | Idioma(s) Tesseract, p. ej. spa, spa+eng
    */
    'tesseract_lang' => env('OCR_TESSERACT_LANG', 'spa'),

    /*
    | Ruta al ejecutable tesseract (Windows Laragon: C:\Program Files\Tesseract-OCR\tesseract.exe)
    */
    'tesseract_binary' => env('OCR_TESSERACT_BINARY'),

    /*
    | Ejecutable OCR nativo Windows (WinOcr.exe) o comando dotnet.
    */
    'windows_binary' => env('OCR_WINDOWS_BINARY'),

    /*
    | Usar OpenAI Vision como respaldo si PDF/imagen no arrojan texto suficiente.
    */
    'openai_fallback' => env('OCR_OPENAI_FALLBACK', true),

    'openai_model' => env('OCR_OPENAI_MODEL', 'gpt-4o-mini'),

    /*
    | Mínimo de caracteres para considerar la extracción exitosa.
    */
    'min_text_length' => (int) env('OCR_MIN_TEXT_LENGTH', 40),

];
