<?php

namespace Database\Seeders;

use App\Models\TipoDocumento;
use Illuminate\Database\Seeder;

class TipoDocumentoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre_tdo' => 'boletin'],
            ['nombre_tdo' => 'certificado_nacimiento'],
            ['nombre_tdo' => 'ci_estudiante'],
            ['nombre_tdo' => 'ci_tutor'],
            ['nombre_tdo' => 'libreta_escolar'],
            ['nombre_tdo' => 'fotografia_estudiante'],
            ['nombre_tdo' => 'formulario_postulacion'],
        ];

        foreach ($tipos as $tipo) {
            TipoDocumento::query()->updateOrCreate(
                ['nombre_tdo' => $tipo['nombre_tdo']],
                ['nombre_tdo' => $tipo['nombre_tdo']],
            );
        }
    }
}

