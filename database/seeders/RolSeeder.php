<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\Usuario;
use App\Support\Roles;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre_rol' => Roles::ADMIN_GENERAL, 'descripcion_rol' => Roles::description(Roles::ADMIN_GENERAL)],
            ['nombre_rol' => Roles::ADMIN_INSTITUCIONAL, 'descripcion_rol' => Roles::description(Roles::ADMIN_INSTITUCIONAL)],
            ['nombre_rol' => Roles::TUTOR, 'descripcion_rol' => Roles::description(Roles::TUTOR)],
        ];

        foreach ($roles as $rol) {
            Rol::query()->updateOrCreate(
                ['nombre_rol' => $rol['nombre_rol']],
                ['descripcion_rol' => $rol['descripcion_rol']],
            );
        }

        $legacyEstudianteRol = Rol::query()->where('nombre_rol', 'estudiante')->first();
        if ($legacyEstudianteRol !== null) {
            Usuario::query()->where('id_rol_usu', $legacyEstudianteRol->id_rol)->delete();
            $legacyEstudianteRol->delete();
        }
    }
}
