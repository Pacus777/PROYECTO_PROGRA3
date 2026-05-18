<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Admin\Catalogo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTipoDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tipo = $this->route('tipoDocumento');

        return [
            'nombre_tdo' => ['required', 'string', 'max:120', Rule::unique('tipo_documento', 'nombre_tdo')->ignore($tipo->id_tdo, 'id_tdo')],
        ];
    }
}
