<?php

namespace App\Http\Requests\Api\Estudiante;

use App\Models\Estudiante;
use App\Support\EstudianteIdentificador;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEstudianteRequest extends FormRequest
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
        /** @var Estudiante|null $estudiante */
        $estudiante = $this->route('estudiante');

        return [
            'rude_est' => [
                'nullable',
                'string',
                'regex:'.EstudianteIdentificador::RUDE_REGEX,
                Rule::unique('estudiante', 'rude_est')->ignore($estudiante?->id_est, 'id_est'),
            ],
            'codigo_est' => [
                'nullable',
                'string',
                'max:40',
                Rule::unique('estudiante', 'codigo_est')->ignore($estudiante?->id_est, 'id_est'),
            ],
            'nombres_per' => ['sometimes', 'string', 'max:120'],
            'ap_paterno_per' => ['sometimes', 'string', 'max:80'],
            'ap_materno_per' => ['nullable', 'string', 'max:80'],
            'ci_per' => [
                'nullable',
                'string',
                'max:32',
                Rule::unique('persona', 'ci_per')->ignore($estudiante?->id_per_est, 'id_per'),
            ],
            'fecha_nac_per' => ['nullable', 'date'],
            'genero_per' => ['nullable', 'string', 'size:1'],
            'correo_per' => ['nullable', 'string', 'email', 'max:160'],
            'telefono_per' => ['nullable', 'string', 'max:40'],
        ];
    }
}
