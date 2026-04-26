<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\AdminInstitucional;

use Illuminate\Foundation\Http\FormRequest;

class StoreCupoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_oac_cup' => ['required', 'integer', 'exists:oferta_academica,id_oac'],
            'total_cup' => ['required', 'integer', 'min:0'],
            'disponibles_cup' => ['required', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ((int) $this->input('disponibles_cup') > (int) $this->input('total_cup')) {
                $validator->errors()->add('disponibles_cup', 'Disponibles no puede superar total.');
            }
        });
    }
}

