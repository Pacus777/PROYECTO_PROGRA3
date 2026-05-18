<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\AdminInstitucional;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostulacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_ept_pos' => ['required', 'integer', 'exists:estado_postulacion,id_ept'],
            'observaciones_pos' => ['nullable', 'string'],
        ];
    }
}
