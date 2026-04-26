<?php

namespace App\Http\Requests\Api\UnidadEducativa;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnidadEducativaRequest extends FormRequest
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
        return [
            'nombre_ued' => ['required', 'string', 'max:200'],
            'codigo_ued' => ['nullable', 'string', 'max:32', 'unique:unidad_educativa,codigo_ued'],
            'direccion_ued' => ['nullable', 'string', 'max:255'],
        ];
    }
}
