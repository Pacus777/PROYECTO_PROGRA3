<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\AdminInstitucional;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCursoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_niv_cur' => ['required', 'integer', 'exists:nivel,id_niv'],
            'nombre_cur' => [
                'required',
                'string',
                'max:80',
                Rule::unique('curso', 'nombre_cur')->where(
                    fn ($q) => $q->where('id_niv_cur', $this->input('id_niv_cur')),
                ),
            ],
        ];
    }
}

