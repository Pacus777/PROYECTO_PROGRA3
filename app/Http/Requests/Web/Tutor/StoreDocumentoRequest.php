<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Tutor;

use Illuminate\Foundation\Http\FormRequest;

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
            'archivo'    => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'archivo.max'   => 'El archivo no puede superar los 5 MB.',
            'archivo.mimes' => 'Solo se permiten archivos PDF, JPG o PNG.',
        ];
    }
}
