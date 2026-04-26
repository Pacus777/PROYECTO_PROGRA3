<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\AdminInstitucional;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOfertaAcademicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_ges_oac' => ['required', 'integer', 'exists:gestion,id_ges'],
            'id_niv_oac' => ['required', 'integer', 'exists:nivel,id_niv'],
            'id_cur_oac' => ['required', 'integer', 'exists:curso,id_cur'],
            'id_par_oac' => ['required', 'integer', 'exists:paralelo,id_par'],
            'descripcion_oac' => ['nullable', 'string', 'max:255'],
            'total_cup' => ['nullable', 'integer', 'min:0'],
            'disponibles_cup' => ['nullable', 'integer', 'min:0'],
            'id_ued_oac' => ['sometimes'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $curso = \App\Models\Curso::query()->find($this->input('id_cur_oac'));
            if ($curso && (int) $curso->id_niv_cur !== (int) $this->input('id_niv_oac')) {
                $validator->errors()->add('id_cur_oac', 'El curso no pertenece al nivel seleccionado.');
            }

            $paralelo = \App\Models\Paralelo::query()->find($this->input('id_par_oac'));
            if ($paralelo && (int) $paralelo->id_cur_par !== (int) $this->input('id_cur_oac')) {
                $validator->errors()->add('id_par_oac', 'El paralelo no pertenece al curso seleccionado.');
            }

            if (
                \App\Models\OfertaAcademica::query()
                    ->where('id_ges_oac', $this->input('id_ges_oac'))
                    ->where('id_ued_oac', $this->input('id_ued_oac'))
                    ->where('id_niv_oac', $this->input('id_niv_oac'))
                    ->where('id_cur_oac', $this->input('id_cur_oac'))
                    ->where('id_par_oac', $this->input('id_par_oac'))
                    ->exists()
            ) {
                $validator->errors()->add('id_par_oac', 'Ya existe esta oferta para la gestión y unidad.');
            }

            $total = $this->input('total_cup');
            $disp = $this->input('disponibles_cup');
            if ($total !== null && $disp !== null && (int) $disp > (int) $total) {
                $validator->errors()->add('disponibles_cup', 'Disponibles no puede ser mayor que el total.');
            }
        });
    }
}

