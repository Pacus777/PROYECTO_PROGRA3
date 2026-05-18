<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\AdminInstitucional;

use App\Models\Nivel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNivelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Nivel|null $nivel */
        $nivel = $this->route('nivel');

        return [
            'nombre_niv' => [
                'required',
                'string',
                'max:80',
                Rule::unique('nivel', 'nombre_niv')->ignore($nivel?->id_niv, 'id_niv'),
            ],
        ];
    }
}

