<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Tutor;

use Illuminate\Foundation\Http\FormRequest;

class AttachEstudianteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_est' => ['required', 'integer', 'exists:estudiante,id_est'],
        ];
    }
}
