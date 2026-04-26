<?php

namespace Database\Seeders;

use App\Models\Persona;
use App\Models\Rol;
use App\Models\Usuario;
use App\Support\Roles;
use Illuminate\Database\Seeder;

class AdminUsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $correo = env('DEFAULT_ADMIN_EMAIL', 'admin@sistema.test');
        $password = env('DEFAULT_ADMIN_PASSWORD', 'Admin123!');

        $rolId = Rol::query()->where('nombre_rol', Roles::ADMIN_GENERAL)->value('id_rol');
        if ($rolId === null) {
            return;
        }

        if (Usuario::query()->where('correo_usu', $correo)->exists()) {
            return;
        }

        $persona = Persona::query()->create([
            'ci_per' => null,
            'nombres_per' => 'Administrador',
            'ap_paterno_per' => 'General',
            'ap_materno_per' => null,
            'correo_per' => $correo,
        ]);

        Usuario::query()->create([
            'id_rol_usu' => $rolId,
            'id_per_usu' => $persona->id_per,
            'id_ued_usu' => null,
            'correo_usu' => $correo,
            'password_usu' => $password,
            'activo_usu' => true,
        ]);
    }
}
