<?php

namespace Database\Seeders;

use App\Models\UnidadEducativa;
use Illuminate\Database\Seeder;

class UnidadEducativaSeeder extends Seeder
{
    public function run(): void
    {
        $unidades = [
            [
                'nombre_ued' => 'Unidad Educativa Mariscal Santa Cruz',
                'codigo_ued' => 'UEMS001',
                'direccion_ued' => 'Zona Central, La Paz',
            ],
            [
                'nombre_ued' => 'Unidad Educativa Ayacucho',
                'codigo_ued' => 'UEA002',
                'direccion_ued' => 'Zona San Pedro, La Paz',
            ],
            [
                'nombre_ued' => 'Unidad Educativa República de México',
                'codigo_ued' => 'UERM003',
                'direccion_ued' => 'Zona Miraflores, La Paz',
            ],
            [
                'nombre_ued' => 'Unidad Educativa Venezuela',
                'codigo_ued' => 'UEV004',
                'direccion_ued' => 'Zona Villa Fátima, La Paz',
            ],
            [
                'nombre_ued' => 'Unidad Educativa Bolivia Japón',
                'codigo_ued' => 'UEBJ005',
                'direccion_ued' => 'Zona Alto Obrajes, La Paz',
            ],
        ];

        foreach ($unidades as $unidad) {
            UnidadEducativa::query()->updateOrCreate(
                ['codigo_ued' => $unidad['codigo_ued']],
                [
                    'nombre_ued' => $unidad['nombre_ued'],
                    'codigo_ued' => $unidad['codigo_ued'],
                    'direccion_ued' => $unidad['direccion_ued'],
                ],
            );
        }
    }
}

