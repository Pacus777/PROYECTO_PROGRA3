<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\Nivel;
use App\Support\CatalogoEducativoBolivia;
use Illuminate\Database\Seeder;

class NivelCursoParaleloSeeder extends Seeder
{
    public function run(): void
    {
        $this->normalizarNombresLegacy();

        foreach (CatalogoEducativoBolivia::nivelesConCursos() as $nombreNivel => $cursos) {
            $nivel = Nivel::query()->updateOrCreate(
                ['nombre_niv' => $nombreNivel],
                ['nombre_niv' => $nombreNivel],
            );

            foreach ($cursos as $nombreCurso) {
                Curso::query()->updateOrCreate(
                    [
                        'id_niv_cur' => $nivel->id_niv,
                        'nombre_cur' => $nombreCurso,
                    ],
                    [
                        'id_niv_cur' => $nivel->id_niv,
                        'nombre_cur' => $nombreCurso,
                    ],
                );
            }
        }
    }

    private function normalizarNombresLegacy(): void
    {
        $nivelSecundaria = Nivel::query()->where('nombre_niv', 'Secundaria')->first();

        if ($nivelSecundaria === null) {
            return;
        }

        Curso::query()
            ->where('id_niv_cur', $nivelSecundaria->id_niv)
            ->where('nombre_cur', '1 de secundaria')
            ->update(['nombre_cur' => '1ro de Secundaria']);
    }
}
