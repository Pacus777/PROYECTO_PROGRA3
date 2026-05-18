<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Admin\Catalogo;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstadoPostulacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_ept' => ['required', 'string', 'max:80', 'unique:estado_postulacion,nombre_ept'],
            'descripcion_ept' => ['nullable', 'string', 'max:255'],
        ];
    }
}
