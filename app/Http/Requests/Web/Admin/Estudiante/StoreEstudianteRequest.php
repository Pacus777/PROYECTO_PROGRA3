<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Admin\Estudiante;

use App\Support\EstudianteIdentificador;
use Illuminate\Foundation\Http\FormRequest;

class StoreEstudianteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        if ($this->filled('rude_est')) {
            $this->merge([
                'rude_est' => EstudianteIdentificador::normalizarRude($this->input('rude_est')),
            ]);
        }

        $ci = $this->input('ci_per');
        if (is_string($ci) && trim($ci) !== '') {
            $this->merge(['ci_per' => preg_replace('/\D/', '', $ci)]);
        }
    }

    public function rules(): array
    {
        return [
            'nombres_per'    => ['required', 'string', 'max:120'],
            'ap_paterno_per' => ['required', 'string', 'max:80'],
            'ap_materno_per' => ['nullable', 'string', 'max:80'],
            'ci_per'         => ['nullable', 'string', 'regex:/^\d{8,20}$/'],
            'fecha_nac_per'  => ['nullable', 'date'],
            'genero_per'     => ['nullable', 'in:M,F'],
            'rude_est'       => ['nullable', 'string', 'regex:'.EstudianteIdentificador::RUDE_REGEX, 'unique:estudiante,rude_est'],
            'codigo_est'     => ['nullable', 'string', 'max:40', 'unique:estudiante,codigo_est'],
            'id_ued_mat_est' => ['nullable', 'integer', 'exists:unidad_educativa,id_ued'],
            'direccion_est' => ['nullable', 'string', 'max:255'],
            'lat_est' => ['nullable', 'numeric', 'between:-90,90'],
            'lng_est' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'rude_est.regex' => 'El RUDE debe contener entre 8 y 12 dígitos numéricos.',
            'ci_per.regex' => 'El CI debe contener solo números, con un mínimo de 8 dígitos.',
        ];
    }
}
