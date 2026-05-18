<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Admin\Catalogo;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_tdo' => ['required', 'string', 'max:120', 'unique:tipo_documento,nombre_tdo'],
        ];
    }
}
