<?php

namespace App\Http\Requests\Api\Usuario;

use App\Support\Roles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUsuarioRequest extends FormRequest
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
            'id_rol_usu' => ['required', 'integer', 'exists:rol,id_rol'],
            'id_ued_usu' => ['nullable', 'integer', 'exists:unidad_educativa,id_ued'],
            'correo_usu' => ['required', 'string', 'email', 'max:160', 'unique:usuario,correo_usu'],
            'password_usu' => ['required', 'string', 'min:8'],
            'activo_usu' => ['sometimes', 'boolean'],
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $rolId = (int) $this->input('id_rol_usu');
            $uedId = $this->input('id_ued_usu');
            $rol = \App\Models\Rol::query()->find($rolId);

            if ($rol && $rol->nombre_rol === Roles::ADMIN_INSTITUCIONAL && empty($uedId)) {
                $validator->errors()->add('id_ued_usu', 'El administrador institucional debe tener unidad educativa asignada.');
            }
        });
    }
}
