<?php

namespace Database\Seeders;

use App\Models\Persona;
use App\Models\Rol;
use App\Models\Usuario;
use App\Support\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $dataset = [
            [
                'correo_usu' => 'admin@gmail.com',
                'password_plano' => 'admin123',
                'rol' => Roles::ADMIN_GENERAL,
                'persona' => [
                    'nombres_per' => 'Admin',
                    'ap_paterno_per' => 'Sistema',
                    'ap_materno_per' => 'Escolar',
                ],
            ],
            [
                'correo_usu' => 'tutor@gmail.com',
                'password_plano' => 'tutor123',
                'rol' => Roles::TUTOR,
                'persona' => [
                    'nombres_per' => 'Tutor',
                    'ap_paterno_per' => 'Demo',
                    'ap_materno_per' => 'Uno',
                ],
            ],
        ];

        foreach ($dataset as $item) {
            $rolId = Rol::query()
                ->where('nombre_rol', $item['rol'])
                ->value('id_rol');

            if ($rolId === null) {
                continue;
            }

            $usuario = Usuario::query()
                ->where('correo_usu', $item['correo_usu'])
                ->first();

            if ($usuario !== null) {
                $usuario->update([
                    'id_rol_usu' => $rolId,
                    'password_usu' => Hash::make($item['password_plano']),
                    'activo_usu' => true,
                ]);
                continue;
            }

            $persona = Persona::query()->create([
                ...$item['persona'],
                'correo_per' => $item['correo_usu'],
            ]);

            Usuario::query()->create([
                'id_rol_usu' => $rolId,
                'id_per_usu' => $persona->id_per,
                'id_ued_usu' => null,
                'correo_usu' => $item['correo_usu'],
                'password_usu' => Hash::make($item['password_plano']),
                'activo_usu' => true,
            ]);
        }
    }
}
