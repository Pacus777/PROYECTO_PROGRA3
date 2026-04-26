<?php

namespace Database\Seeders;

use App\Models\Criterio;
use App\Models\TipoCriterio;
use Illuminate\Database\Seeder;

class CriterioSeeder extends Seeder
{
    public function run(): void
    {
        $criterios = [
            ['tipo' => 'academico', 'nombre_cri' => 'Promedio académico', 'peso_cri' => 0.40],
            ['tipo' => 'geografico', 'nombre_cri' => 'Distancia domicilio', 'peso_cri' => 0.20],
            ['tipo' => 'familiar', 'nombre_cri' => 'Hermano en la unidad educativa', 'peso_cri' => 0.15],
            ['tipo' => 'social', 'nombre_cri' => 'Condición socioeconómica', 'peso_cri' => 0.15],
            ['tipo' => 'prioridad', 'nombre_cri' => 'Prioridad institucional', 'peso_cri' => 0.10],
        ];

        foreach ($criterios as $criterio) {
            $idTipoCriterio = TipoCriterio::query()
                ->where('nombre_tic', $criterio['tipo'])
                ->value('id_tic');

            if ($idTipoCriterio === null) {
                throw new \Exception(
                    "TipoCriterio no encontrado: " . $criterio['tipo']
                );
            }

            Criterio::query()->updateOrCreate(
                ['nombre_cri' => $criterio['nombre_cri']],
                [
                    'id_tic_cri' => $idTipoCriterio,
                    'nombre_cri' => $criterio['nombre_cri'],
                    'peso_cri' => $criterio['peso_cri'],
                ],
            );
        }
    }
}

