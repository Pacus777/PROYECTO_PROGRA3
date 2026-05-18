<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Admin\Estudiante;

use App\Support\EstudianteIdentificador;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEstudianteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        if ($this->filled('rude_est')) {
            $this->merge([
                'rude_est' => EstudianteIdentificador::normalizarRude($this->input('rude_est')),
            ]);
        }
    }

    public function rules(): array
    {
        $estudiante = $this->route('estudiante');

        return [
            'nombres_per'    => ['required', 'string', 'max:120'],
            'ap_paterno_per' => ['required', 'string', 'max:80'],
            'ap_materno_per' => ['nullable', 'string', 'max:80'],
            'ci_per'         => ['nullable', 'string', 'max:20'],
            'fecha_nac_per'  => ['nullable', 'date'],
            'genero_per'     => ['nullable', 'in:M,F'],
            'correo_per'     => ['nullable', 'email', 'max:120'],
            'telefono_per'   => ['nullable', 'string', 'max:20'],
            'rude_est'       => ['nullable', 'string', 'regex:'.EstudianteIdentificador::RUDE_REGEX, Rule::unique('estudiante', 'rude_est')->ignore($estudiante->id_est, 'id_est')],
            'codigo_est'     => ['nullable', 'string', 'max:40', Rule::unique('estudiante', 'codigo_est')->ignore($estudiante->id_est, 'id_est')],
            'id_ued_mat_est' => ['nullable', 'integer', 'exists:unidad_educativa,id_ued'],
        ];
    }

    public function messages(): array
    {
        return [
            'rude_est.regex' => 'El RUDE debe contener entre 8 y 12 dígitos numéricos.',
        ];
    }
}
