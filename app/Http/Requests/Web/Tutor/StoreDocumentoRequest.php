<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Tutor;

use App\Models\Documento;
use App\Models\Postulacion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_tdo_doc' => ['required', 'integer', 'exists:tipo_documento,id_tdo'],
            'archivo' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            /** @var Postulacion|null $postulacion */
            $postulacion = $this->route('postulacion');

            if (! $postulacion instanceof Postulacion) {
                $v->errors()->add('id_tdo_doc', 'No se pudo identificar la postulación.');
                return;
            }

            $postulacion->loadMissing('ofertaAcademica.tiposDocumentoRequeridos');

            $tipoDocumentoId = (int) $this->input('id_tdo_doc');

            $esDocumentoRequerido = $postulacion->ofertaAcademica
                ? $postulacion->ofertaAcademica->tiposDocumentoRequeridos->contains('id_tdo', $tipoDocumentoId)
                : false;

            if (! $esDocumentoRequerido) {
                $v->errors()->add('id_tdo_doc', 'El documento seleccionado no está requerido para esta oferta académica.');
                return;
            }

            if (! $postulacion->ofertaAcademica->estaAbiertaParaPostulacion()) {
                $v->errors()->add('id_pos_doc', 'No se pueden subir documentos fuera del periodo de postulación.');
                return;
            }

            $yaExisteDocumentoActivo = Documento::query()
                ->where('id_pos_doc', $postulacion->id_pos)
                ->where('id_tdo_doc', $tipoDocumentoId)
                ->whereIn('estado_doc', ['pendiente', 'verificado'])
                ->exists();

            if ($yaExisteDocumentoActivo) {
                $v->errors()->add('id_tdo_doc', 'Este documento ya fue cargado y se encuentra pendiente o verificado.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'archivo.max' => 'El archivo no puede superar los 5 MB.',
            'archivo.mimes' => 'Solo se permiten archivos PDF, JPG o PNG.',
        ];
    }
}