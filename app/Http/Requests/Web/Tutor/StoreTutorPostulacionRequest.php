<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Tutor;

use App\Models\Estudiante;
use App\Models\OfertaAcademica;
use App\Models\Postulacion;
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
            'prioridad_pos' => ['required', 'integer', 'min:1', 'max:20'],
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

            $tutor = Tutor::query()
                ->where('id_per_tut', $usuario->id_per_usu)
                ->first();

            if ($tutor === null) {
                $v->errors()->add('id_est_pos', 'No existe perfil de tutor asociado.');
                return;
            }

            $tutor->loadMissing('estudiantes');

            $allowed = $tutor->estudiantes
                ->pluck('id_est')
                ->map(static fn ($id): int => (int) $id)
                ->all();

            $estudianteId = (int) $this->input('id_est_pos');
            $ofertaId = (int) $this->input('id_oac_pos');
            $prioridad = (int) $this->input('prioridad_pos');

            if (! in_array($estudianteId, $allowed, true)) {
                $v->errors()->add('id_est_pos', 'El estudiante seleccionado no está vinculado a tu perfil de tutor.');
                return;
            }

            /** @var OfertaAcademica|null $oferta */
            $oferta = OfertaAcademica::query()->with('unidadEducativa')->find($ofertaId);

            if ($oferta === null) {
                $v->errors()->add('id_oac_pos', 'La oferta académica seleccionada no existe.');
                return;
            }

            if (! $oferta->estaAbiertaParaPostulacion()) {
                $v->errors()->add('id_oac_pos', 'La oferta académica seleccionada no se encuentra abierta para postulación.');
                return;
            }

            $yaExistePostulacion = Postulacion::query()
                ->where('id_est_pos', $estudianteId)
                ->where('id_oac_pos', $ofertaId)
                ->exists();

            if ($yaExistePostulacion) {
                $v->errors()->add('id_oac_pos', 'Este estudiante ya tiene una postulación registrada para la oferta seleccionada.');
                return;
            }

            $prioridadRepetida = Postulacion::query()
                ->where('id_est_pos', $estudianteId)
                ->where('prioridad_pos', $prioridad)
                ->whereHas('ofertaAcademica', function ($query) use ($oferta): void {
                    $query->where('id_ges_oac', $oferta->id_ges_oac);
                })
                ->exists();

            if ($prioridadRepetida) {
                $v->errors()->add('prioridad_pos', 'Este estudiante ya tiene una postulación con esa prioridad en la misma gestión.');
            }

            $estudiante = Estudiante::query()->find($estudianteId);
            if ($estudiante !== null && ! $estudiante->tieneDomicilioRegistrado()) {
                $v->errors()->add(
                    'id_est_pos',
                    'Debe registrar el domicilio del estudiante en el mapa antes de postular (se usa para evaluar la cercanía al colegio).',
                );
            }

            if ($oferta->unidadEducativa !== null
                && ($oferta->unidadEducativa->lat_ued === null || $oferta->unidadEducativa->lng_ued === null)) {
                $v->errors()->add(
                    'id_oac_pos',
                    'El colegio de esta oferta aún no tiene ubicación en el mapa. Contacte a la unidad educativa.',
                );
            }
        });
    }
}