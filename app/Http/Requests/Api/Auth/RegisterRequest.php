<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'rol_nombre' => ['nullable', 'string', Rule::in(['tutor'])],
            'correo_usu' => ['required', 'string', 'email', 'max:160', 'unique:usuario,correo_usu'],
            'password_usu' => ['required', 'string', 'min:8', 'confirmed'],
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
