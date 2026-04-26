<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Admin\UnidadEducativa;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnidadEducativaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach (['codigo_ued', 'direccion_ued'] as $field) {
            if ($this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }
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
