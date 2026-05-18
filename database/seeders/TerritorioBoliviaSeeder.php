<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Departamento;
use App\Models\DistritoEducativo;
use App\Models\Municipio;
use App\Models\Provincia;
use Illuminate\Database\Seeder;

class TerritorioBoliviaSeeder extends Seeder
{
    public function run(): void
    {
        $estructura = [
            'LP' => [
                'nombre' => 'La Paz',
                'provincias' => [
                    'Murillo' => ['La Paz', 'El Alto', 'Palca'],
                    'Los Andes' => ['Pucarani', 'Laja'],
                    'Omasuyos' => ['Achacachi', 'Ancoraimes'],
                ],
                'distritos' => [
                    ['codigo' => 'LP-01', 'nombre' => 'Distrito La Paz Centro'],
                    ['codigo' => 'LP-02', 'nombre' => 'Distrito El Alto'],
                ],
            ],
            'SC' => [
                'nombre' => 'Santa Cruz',
                'provincias' => [
                    'Andrés Ibáñez' => ['Santa Cruz de la Sierra', 'La Guardia'],
                    'Warnes' => ['Warnes', 'Okinawa Uno'],
                ],
                'distritos' => [
                    ['codigo' => 'SC-01', 'nombre' => 'Distrito Santa Cruz Centro'],
                ],
            ],
            'CB' => [
                'nombre' => 'Cochabamba',
                'provincias' => [
                    'Cercado' => ['Cochabamba', 'Sacaba'],
                    'Quillacollo' => ['Quillacollo', 'Colcapirhua'],
                ],
                'distritos' => [
                    ['codigo' => 'CB-01', 'nombre' => 'Distrito Cochabamba Centro'],
                ],
            ],
            'OR' => [
                'nombre' => 'Oruro',
                'provincias' => [
                    'Cercado' => ['Oruro'],
                    'Saucarí' => ['Toledo'],
                ],
                'distritos' => [
                    ['codigo' => 'OR-01', 'nombre' => 'Distrito Oruro'],
                ],
            ],
            'PT' => [
                'nombre' => 'Potosí',
                'provincias' => [
                    'Tomás Frías' => ['Potosí'],
                    'Antonio Quijarro' => ['Uyuni'],
                ],
                'distritos' => [
                    ['codigo' => 'PT-01', 'nombre' => 'Distrito Potosí'],
                ],
            ],
            'TJ' => [
                'nombre' => 'Tarija',
                'provincias' => [
                    'Cercado' => ['Tarija'],
                    'Arce' => ['Bermejo'],
                ],
                'distritos' => [
                    ['codigo' => 'TJ-01', 'nombre' => 'Distrito Tarija'],
                ],
            ],
            'CH' => [
                'nombre' => 'Chuquisaca',
                'provincias' => [
                    'Oropeza' => ['Sucre'],
                    'Zudáñez' => ['Presto'],
                ],
                'distritos' => [
                    ['codigo' => 'CH-01', 'nombre' => 'Distrito Sucre'],
                ],
            ],
            'BN' => [
                'nombre' => 'Beni',
                'provincias' => [
                    'Cercado' => ['Trinidad'],
                    'Vaca Diez' => ['Riberalta'],
                ],
                'distritos' => [
                    ['codigo' => 'BN-01', 'nombre' => 'Distrito Trinidad'],
                ],
            ],
            'PD' => [
                'nombre' => 'Pando',
                'provincias' => [
                    'Abuná' => ['Cobija'],
                    'Manuripi' => ['Puerto Rico'],
                ],
                'distritos' => [
                    ['codigo' => 'PD-01', 'nombre' => 'Distrito Cobija'],
                ],
            ],
        ];

        foreach ($estructura as $codigo => $dep) {
            $departamento = Departamento::query()->updateOrCreate(
                ['codigo_dep' => $codigo],
                ['nombre_dep' => $dep['nombre']],
            );

            foreach ($dep['provincias'] as $nombreProv => $municipios) {
                $provincia = Provincia::query()->updateOrCreate(
                    [
                        'id_dep_prov' => $departamento->id_dep,
                        'nombre_prov' => $nombreProv,
                    ],
                    ['id_dep_prov' => $departamento->id_dep, 'nombre_prov' => $nombreProv],
                );

                foreach ($municipios as $nombreMun) {
                    Municipio::query()->updateOrCreate(
                        [
                            'id_prov_mun' => $provincia->id_prov,
                            'nombre_mun' => $nombreMun,
                        ],
                        ['id_prov_mun' => $provincia->id_prov, 'nombre_mun' => $nombreMun],
                    );
                }
            }

            foreach ($dep['distritos'] as $distrito) {
                DistritoEducativo::query()->updateOrCreate(
                    [
                        'id_dep_dis' => $departamento->id_dep,
                        'nombre_dis' => $distrito['nombre'],
                    ],
                    [
                        'id_dep_dis' => $departamento->id_dep,
                        'codigo_dis' => $distrito['codigo'],
                        'nombre_dis' => $distrito['nombre'],
                    ],
                );
            }
        }
    }
}
