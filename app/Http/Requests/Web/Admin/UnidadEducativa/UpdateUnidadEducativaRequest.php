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
        foreach (['codigo_ued', 'direccion_ued', 'id_dis_ued', 'lat_ued', 'lng_ued', 'telefono_ued', 'correo_ued', 'turno_ued', 'niveles_ued', 'imagen_portada_ued'] as $field) {
            if ($this->has($field) && $this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }

        if ($this->has('galeria_ued_text')) {
            $lineas = preg_split('/\r\n|\r|\n/', (string) $this->input('galeria_ued_text')) ?: [];
            $urls = array_values(array_filter(array_map('trim', $lineas)));
            $this->merge(['galeria_ued' => $urls !== [] ? $urls : null]);
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
            'descripcion_ued' => ['nullable', 'string', 'max:5000'],
            'telefono_ued' => ['nullable', 'string', 'max:40'],
            'correo_ued' => ['nullable', 'email', 'max:120'],
            'turno_ued' => ['nullable', 'string', 'max:80'],
            'niveles_ued' => ['nullable', 'string', 'max:160'],
            'imagen_portada_ued' => ['nullable', 'string', 'max:500'],
            'galeria_ued' => ['nullable', 'array', 'max:12'],
            'galeria_ued.*' => ['string', 'max:500'],
            'galeria_ued_text' => ['nullable', 'string', 'max:6000'],
            'lat_ued' => ['nullable', 'numeric', 'between:-90,90'],
            'lng_ued' => ['nullable', 'numeric', 'between:-180,180'],
            'id_mun_ued' => ['sometimes', 'required', 'integer', 'exists:municipio,id_mun'],
            'id_dis_ued' => ['nullable', 'integer', 'exists:distrito_educativo,id_dis'],
        ];
    }
}
