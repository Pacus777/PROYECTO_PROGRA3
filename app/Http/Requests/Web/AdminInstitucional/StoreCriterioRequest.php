<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\AdminInstitucional;

use Illuminate\Foundation\Http\FormRequest;

class StoreCriterioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_tic_cri' => ['required', 'integer', 'exists:tipo_criterio,id_tic'],
            'nombre_cri' => ['required', 'string', 'max:160'],
            'peso_cri' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }
}

