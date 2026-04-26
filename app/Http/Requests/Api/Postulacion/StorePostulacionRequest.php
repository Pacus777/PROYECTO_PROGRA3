<?php

namespace App\Http\Requests\Api\Postulacion;

use Illuminate\Foundation\Http\FormRequest;

class StorePostulacionRequest extends FormRequest
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
            'id_ept_pos' => ['nullable', 'integer', 'exists:estado_postulacion,id_ept'],
            'fecha_pos' => ['nullable', 'date'],
            'observaciones_pos' => ['nullable', 'string'],
        ];
    }
}
