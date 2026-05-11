<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Admin\Estudiante;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEstudianteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

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
            'codigo_est'     => ['nullable', 'string', 'max:40', Rule::unique('estudiante', 'codigo_est')->ignore($estudiante->id_est, 'id_est')],
        ];
    }
}
