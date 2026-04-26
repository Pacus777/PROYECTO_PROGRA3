<?php

namespace Database\Seeders;

use App\Models\EstadoPostulacion;
use Illuminate\Database\Seeder;

class EstadoPostulacionSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['nombre_ept' => 'borrador', 'descripcion_ept' => 'Postulación en edición.'],
            ['nombre_ept' => 'enviada', 'descripcion_ept' => 'Enviada para revisión.'],
            ['nombre_ept' => 'en_evaluacion', 'descripcion_ept' => 'En proceso de evaluación.'],
            ['nombre_ept' => 'aprobada', 'descripcion_ept' => 'Aprobada.'],
            ['nombre_ept' => 'rechazada', 'descripcion_ept' => 'Rechazada.'],
        ];

        foreach ($estados as $e) {
            EstadoPostulacion::query()->updateOrCreate(
                ['nombre_ept' => $e['nombre_ept']],
                ['descripcion_ept' => $e['descripcion_ept']],
            );
        }
    }
}
