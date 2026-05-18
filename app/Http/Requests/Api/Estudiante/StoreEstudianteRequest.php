<?php

namespace App\Http\Requests\Api\Estudiante;

use App\Support\EstudianteIdentificador;
use Illuminate\Foundation\Http\FormRequest;

class StoreEstudianteRequest extends FormRequest
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
            'rude_est' => ['nullable', 'string', 'regex:'.EstudianteIdentificador::RUDE_REGEX, 'unique:estudiante,rude_est'],
            'codigo_est' => ['nullable', 'string', 'max:40', 'unique:estudiante,codigo_est'],
            'nombres_per' => ['required', 'string', 'max:120'],
            'ap_paterno_per' => ['required', 'string', 'max:80'],
            'ap_materno_per' => ['nullable', 'string', 'max:80'],
            'ci_per' => ['nullable', 'string', 'max:32', 'unique:persona,ci_per'],
            'fecha_nac_per' => ['nullable', 'date'],
            'genero_per' => ['nullable', 'string', 'size:1'],
            'correo_per' => ['nullable', 'string', 'email', 'max:160'],
            'telefono_per' => ['nullable', 'string', 'max:40'],
        ];
    }
}
