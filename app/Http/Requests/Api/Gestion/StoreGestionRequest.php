<?php

namespace App\Http\Requests\Api\Gestion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class StoreGestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach (['fecha_ini_ges', 'fecha_fin_ges'] as $field) {
            if ($this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }

        $this->merge([
            'activa_ges' => $this->boolean('activa_ges'),
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre_ges' => ['required', 'string', 'max:32'],
            'fecha_ini_ges' => ['nullable', 'date'],
            'fecha_fin_ges' => ['nullable', 'date'],
            'activa_ges' => ['boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $ini = $this->input('fecha_ini_ges');
            $fin = $this->input('fecha_fin_ges');

            if (! $ini || ! $fin) {
                return;
            }

            if (Carbon::parse($fin)->lt(Carbon::parse($ini))) {
                $validator->errors()->add('fecha_fin_ges', 'La fecha fin no puede ser menor que la fecha inicio.');
            }
        });
    }
}
