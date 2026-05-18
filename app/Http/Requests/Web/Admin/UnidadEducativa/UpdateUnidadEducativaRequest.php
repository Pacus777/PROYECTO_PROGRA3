<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Admin\UnidadEducativa;

use App\Models\UnidadEducativa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnidadEducativaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach (['codigo_ued', 'direccion_ued', 'id_dis_ued', 'lat_ued', 'lng_ued'] as $field) {
            if ($this->has($field) && $this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }
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
            'lat_ued' => ['nullable', 'numeric', 'between:-90,90'],
            'lng_ued' => ['nullable', 'numeric', 'between:-180,180'],
            'id_mun_ued' => ['sometimes', 'required', 'integer', 'exists:municipio,id_mun'],
            'id_dis_ued' => ['nullable', 'integer', 'exists:distrito_educativo,id_dis'],
        ];
    }
}
