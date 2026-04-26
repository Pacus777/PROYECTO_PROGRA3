<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\AdminInstitucional;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNivelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_niv' => ['required', 'string', 'max:80'],
        ];
    }
}

