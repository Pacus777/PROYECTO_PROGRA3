<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\AdminInstitucional;

use App\Models\OfertaAcademica;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOfertaAcademicaRequest extends FormRequest
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
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var OfertaAcademica|null $oferta */
            $oferta = $this->route('oferta_academica');

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
                    ->where('id_ued_oac', $oferta?->id_ued_oac)
                    ->where('id_niv_oac', $this->input('id_niv_oac'))
                    ->where('id_cur_oac', $this->input('id_cur_oac'))
                    ->where('id_par_oac', $this->input('id_par_oac'))
                    ->where('id_oac', '!=', $oferta?->id_oac)
                    ->exists()
            ) {
                $validator->errors()->add('id_par_oac', 'Ya existe esta oferta para la gestión y unidad.');
            }
        });
    }
}

