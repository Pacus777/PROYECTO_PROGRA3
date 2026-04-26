<?php

namespace App\Http\Requests\Api\UnidadEducativa;

use App\Models\UnidadEducativa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnidadEducativaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var UnidadEducativa|null $unidad */
        $unidad = $this->route('unidad_educativa');

        return [
            'nombre_ued' => ['sometimes', 'string', 'max:200'],
            'codigo_ued' => [
                'nullable',
                'string',
                'max:32',
                Rule::unique('unidad_educativa', 'codigo_ued')->ignore($unidad?->id_ued, 'id_ued'),
            ],
            'direccion_ued' => ['nullable', 'string', 'max:255'],
        ];
    }
}
