<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\AdminInstitucional;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreParaleloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_cur_par' => ['required', 'integer', 'exists:curso,id_cur'],
            'nombre_par' => [
                'required',
                'string',
                'max:16',
                Rule::unique('paralelo', 'nombre_par')->where(
                    fn ($q) => $q->where('id_cur_par', $this->input('id_cur_par')),
                ),
            ],
        ];
    }
}

