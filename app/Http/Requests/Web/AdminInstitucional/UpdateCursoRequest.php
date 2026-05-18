<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\AdminInstitucional;

use App\Models\Curso;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCursoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Curso|null $curso */
        $curso = $this->route('curso');

        return [
            'id_niv_cur' => ['required', 'integer', 'exists:nivel,id_niv'],
            'nombre_cur' => [
                'required',
                'string',
                'max:80',
                Rule::unique('curso', 'nombre_cur')
                    ->where(fn ($q) => $q->where('id_niv_cur', $this->input('id_niv_cur')))
                    ->ignore($curso?->id_cur, 'id_cur'),
            ],
        ];
    }
}

