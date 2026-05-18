<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            EstadoPostulacionSeeder::class,
            TipoDocumentoSeeder::class,
            TipoCriterioSeeder::class,
            CriterioSeeder::class,
            GestionSeeder::class,
            NivelCursoParaleloSeeder::class,
            TerritorioBoliviaSeeder::class,
            UnidadEducativaSeeder::class,
            AdminInstitucionalSeeder::class,
            AdminUsuarioSeeder::class,
            UsuariosPruebaSeeder::class,
            TutorDemoSeeder::class,
        ]);
    }
}
