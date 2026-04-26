<?php

namespace Database\Seeders;

use App\Models\Persona;
use App\Models\Rol;
use App\Models\UnidadEducativa;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminInstitucionalSeeder extends Seeder
{
    public function run(): void
    {
        $rolId = Rol::query()
            ->where('nombre_rol', 'admin_institucional')
            ->value('id_rol');

        if ($rolId === null) {
            return;
        }

        $unidadId = UnidadEducativa::query()
            ->orderBy('id_ued')
            ->value('id_ued');

        if ($unidadId === null) {
            return;
        }

        $correo = 'admin.institucional@gmail.com';

        $persona = Persona::query()->updateOrCreate(
            ['correo_per' => $correo],
            [
                'nombres_per' => 'Admin',
                'ap_paterno_per' => 'Institucional',
                'ap_materno_per' => null,
                'correo_per' => $correo,
            ],
        );

        Usuario::query()->updateOrCreate(
            ['correo_usu' => $correo],
            [
                'id_rol_usu' => $rolId,
                'id_per_usu' => $persona->id_per,
                'id_ued_usu' => $unidadId,
                'correo_usu' => $correo,
                'password_usu' => Hash::make('admininst123'),
                'activo_usu' => true,
            ],
        );
    }
}

