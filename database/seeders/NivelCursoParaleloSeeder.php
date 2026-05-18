<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\Nivel;
use App\Models\Paralelo;
use Illuminate\Database\Seeder;

class NivelCursoParaleloSeeder extends Seeder
{
    public function run(): void
    {
        $nivel = Nivel::query()->updateOrCreate(
            ['nombre_niv' => 'Secundaria'],
            ['nombre_niv' => 'Secundaria'],
        );

        $curso = Curso::query()->updateOrCreate(
            [
                'id_niv_cur' => $nivel->id_niv,
                'nombre_cur' => '1 de secundaria',
            ],
            [
                'id_niv_cur' => $nivel->id_niv,
                'nombre_cur' => '1 de secundaria',
            ],
        );

        Paralelo::query()->updateOrCreate(
            [
                'id_cur_par' => $curso->id_cur,
                'nombre_par' => 'A',
            ],
            [
                'id_cur_par' => $curso->id_cur,
                'nombre_par' => 'A',
            ],
        );
    }
}