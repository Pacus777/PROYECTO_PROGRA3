<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\AdminInstitucional;

use App\Models\Paralelo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateParaleloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Paralelo|null $paralelo */
        $paralelo = $this->route('paralelo');

        return [
            'id_cur_par' => ['required', 'integer', 'exists:curso,id_cur'],
            'nombre_par' => [
                'required',
                'string',
                'max:16',
                Rule::unique('paralelo', 'nombre_par')
                    ->where(fn ($q) => $q->where('id_cur_par', $this->input('id_cur_par')))
                    ->ignore($paralelo?->id_par, 'id_par'),
            ],
        ];
    }
}

