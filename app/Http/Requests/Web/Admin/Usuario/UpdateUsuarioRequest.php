<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Admin\Usuario;

use App\Models\Usuario;
use App\Support\Roles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('id_ued_usu') === '' || $this->input('id_ued_usu') === null) {
            $this->merge(['id_ued_usu' => null]);
        }

        $password = $this->input('password_usu');
        if ($password !== null && trim((string) $password) === '') {
            $this->merge(['password_usu' => null]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Usuario|null $usuario */
        $usuario = $this->route('usuario');

        return [
            'id_rol_usu' => [
                'sometimes',
                'integer',
                Rule::exists('rol', 'id_rol')->where(fn ($query) => $query->whereIn('nombre_rol', Roles::assignable())),
            ],
            'id_ued_usu' => ['nullable', 'integer', 'exists:unidad_educativa,id_ued'],
            'correo_usu' => [
                'sometimes',
                'string',
                'email',
                'max:160',
                Rule::unique('usuario', 'correo_usu')->ignore($usuario?->id_usu, 'id_usu'),
            ],
            'password_usu' => ['sometimes', 'nullable', 'string', 'min:8'],
            'activo_usu' => ['required', 'boolean'],
            'nombres_per' => ['sometimes', 'string', 'max:120'],
            'ap_paterno_per' => ['sometimes', 'string', 'max:80'],
            'ap_materno_per' => ['nullable', 'string', 'max:80'],
            'ci_per' => [
                'nullable',
                'string',
                'max:32',
                Rule::unique('persona', 'ci_per')->ignore($usuario?->id_per_usu, 'id_per'),
            ],
            'fecha_nac_per' => ['nullable', 'date'],
            'genero_per' => ['nullable', 'string', 'size:1'],
            'correo_per' => ['nullable', 'string', 'email', 'max:160'],
            'telefono_per' => ['nullable', 'string', 'max:40'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var Usuario|null $usuario */
            $usuario = $this->route('usuario');
            $rolId = (int) ($this->input('id_rol_usu') ?? $usuario?->id_rol_usu);
            $uedId = $this->input('id_ued_usu', $usuario?->id_ued_usu);
            $rol = \App\Models\Rol::query()->find($rolId);

            if ($rol && $rol->nombre_rol === Roles::ADMIN_INSTITUCIONAL && empty($uedId)) {
                $validator->errors()->add('id_ued_usu', 'La cuenta de unidad educativa (director / secretaría) debe tener un colegio asignado.');
            }
        });
    }
}
