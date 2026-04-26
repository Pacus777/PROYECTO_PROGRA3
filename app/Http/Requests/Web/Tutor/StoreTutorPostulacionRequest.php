<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Tutor;

use App\Models\Tutor;
use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreTutorPostulacionRequest extends FormRequest
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
            'id_est_pos' => ['required', 'integer', 'exists:estudiante,id_est'],
            'id_oac_pos' => ['required', 'integer', 'exists:oferta_academica,id_oac'],
            'observaciones_pos' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $usuarioId = $this->session()->get('web_usuario_id');
            if ($usuarioId === null) {
                $v->errors()->add('id_est_pos', 'Sesión no válida.');

                return;
            }

            /** @var Usuario|null $usuario */
            $usuario = Usuario::query()->find($usuarioId);
            if ($usuario === null || $usuario->id_per_usu === null) {
                $v->errors()->add('id_est_pos', 'Usuario no encontrado.');

                return;
            }

            $tutor = Tutor::query()->where('id_per_tut', $usuario->id_per_usu)->first();
            if ($tutor === null) {
                $v->errors()->add('id_est_pos', 'No existe perfil de tutor asociado.');

                return;
            }

            $tutor->loadMissing('estudiantes');
            $allowed = $tutor->estudiantes->pluck('id_est')->map(static fn ($id): int => (int) $id)->all();
            if (! in_array((int) $this->input('id_est_pos'), $allowed, true)) {
                $v->errors()->add('id_est_pos', 'El estudiante seleccionado no está vinculado a tu perfil de tutor.');
            }
        });
    }
}
