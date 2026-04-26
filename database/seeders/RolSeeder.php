<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Support\Roles;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre_rol' => Roles::ADMIN_GENERAL, 'descripcion_rol' => 'Administración global del sistema.'],
            ['nombre_rol' => Roles::ADMIN_INSTITUCIONAL, 'descripcion_rol' => 'Administración por unidad educativa.'],
            ['nombre_rol' => Roles::TUTOR, 'descripcion_rol' => 'Tutor o responsable de estudiantes.'],
            ['nombre_rol' => Roles::ESTUDIANTE, 'descripcion_rol' => 'Usuario estudiante del proceso de admisión.'],
        ];

        foreach ($roles as $rol) {
            Rol::query()->updateOrCreate(
                ['nombre_rol' => $rol['nombre_rol']],
                ['descripcion_rol' => $rol['descripcion_rol']],
            );
        }
    }
}
