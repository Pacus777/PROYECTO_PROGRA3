<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\AdminInstitucional;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_cri_eva' => ['required', 'integer', 'exists:criterio,id_cri'],
            'puntaje_eva' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'observaciones_eva' => ['nullable', 'string'],
        ];
    }
}

