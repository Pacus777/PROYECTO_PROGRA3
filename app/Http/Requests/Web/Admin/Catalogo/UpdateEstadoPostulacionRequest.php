<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Admin\Catalogo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEstadoPostulacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $estado = $this->route('estadoPostulacion');

        return [
            'nombre_ept' => ['required', 'string', 'max:80', Rule::unique('estado_postulacion', 'nombre_ept')->ignore($estado->id_ept, 'id_ept')],
            'descripcion_ept' => ['nullable', 'string', 'max:255'],
        ];
    }
}
