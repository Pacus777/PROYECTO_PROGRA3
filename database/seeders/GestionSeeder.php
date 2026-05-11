<?php

namespace Database\Seeders;

use App\Models\Gestion;
use Illuminate\Database\Seeder;

class GestionSeeder extends Seeder
{
    public function run(): void
    {
        if (Gestion::query()->exists()) {
            return;
        }

        $year = (int) now()->format('Y');

        Gestion::query()->create([
            'nombre_ges' => 'Gestión '.$year,
            'fecha_ini_ges' => $year.'-01-01',
            'fecha_fin_ges' => $year.'-12-31',
            'activa_ges' => true,
        ]);
    }
}
