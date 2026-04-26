<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\AdminInstitucional;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEvaluacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'puntaje_eva' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'observaciones_eva' => ['nullable', 'string'],
        ];
    }
}

