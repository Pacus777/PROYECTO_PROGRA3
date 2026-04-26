<?php

namespace Database\Seeders;

use App\Models\TipoCriterio;
use Illuminate\Database\Seeder;

class TipoCriterioSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre_tic' => 'academico'],
            ['nombre_tic' => 'social'],
            ['nombre_tic' => 'geografico'],
            ['nombre_tic' => 'prioridad'],
            ['nombre_tic' => 'familiar'],
        ];

        foreach ($tipos as $tipo) {
            TipoCriterio::query()->updateOrCreate(
                ['nombre_tic' => $tipo['nombre_tic']],
                ['nombre_tic' => $tipo['nombre_tic']],
            );
        }
    }
}

