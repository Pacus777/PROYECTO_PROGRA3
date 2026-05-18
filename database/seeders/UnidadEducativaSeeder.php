<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DistritoEducativo;
use App\Models\Municipio;
use App\Models\UnidadEducativa;
use Illuminate\Database\Seeder;

class UnidadEducativaSeeder extends Seeder
{
    public function run(): void
    {
        $municipioLaPaz = Municipio::query()
            ->where('nombre_mun', 'La Paz')
            ->whereHas('provincia', fn ($q) => $q->where('nombre_prov', 'Murillo'))
            ->first();

        $distrito = DistritoEducativo::query()
            ->where('codigo_dis', 'LP-01')
            ->first();

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
                    'id_mun_ued' => $municipioLaPaz?->id_mun,
                    'id_dis_ued' => $distrito?->id_dis,
                ],
            );
        }
    }
}
