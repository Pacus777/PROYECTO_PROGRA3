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

        $fotosEscuela = [
            'https://images.unsplash.com/photo-1580582932707-520aed925b7f?w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=1200&auto=format&fit=crop',
        ];

        $unidades = [
            [
                'nombre_ued' => 'Unidad Educativa Mariscal Santa Cruz',
                'codigo_ued' => 'UEMS001',
                'direccion_ued' => 'Zona Munaypata, Calle Sahapaqui, La Paz',
                'descripcion_ued' => 'Institución pública con tradición en formación integral. Ofrece niveles Inicial, Primaria y Secundaria con énfasis en valores, tecnología y participación de las familias en el proceso educativo.',
                'telefono_ued' => '2-1234567',
                'correo_ued' => 'contacto@mariscalsantacruz.edu.bo',
                'turno_ued' => 'Mañana y Tarde',
                'niveles_ued' => 'Inicial, Primaria, Secundaria',
                'imagen_portada_ued' => $fotosEscuela[0],
                'galeria_ued' => $fotosEscuela,
                'lat_ued' => -16.489689,
                'lng_ued' => -68.119293,
            ],
            [
                'nombre_ued' => 'Unidad Educativa Ayacucho',
                'codigo_ued' => 'UEA002',
                'direccion_ued' => 'Zona San Pedro, La Paz',
                'descripcion_ued' => 'Colegio fiscal con enfoque académico y actividades deportivas. Atiende a estudiantes de Primaria y Secundaria en turno mañana.',
                'telefono_ued' => '2-2345678',
                'correo_ued' => 'info@ueayacucho.edu.bo',
                'turno_ued' => 'Mañana',
                'niveles_ued' => 'Primaria, Secundaria',
                'imagen_portada_ued' => $fotosEscuela[1],
                'galeria_ued' => array_slice($fotosEscuela, 1, 3),
                'lat_ued' => -16.514521,
                'lng_ued' => -68.124812,
            ],
            [
                'nombre_ued' => 'Unidad Educativa República de México',
                'codigo_ued' => 'UERM003',
                'direccion_ued' => 'Zona Miraflores, La Paz',
                'descripcion_ued' => 'Unidad educativa con proyectos de ciencia y arte. Cuenta con infraestructura para laboratorios y talleres creativos.',
                'telefono_ued' => '2-3456789',
                'turno_ued' => 'Tarde',
                'niveles_ued' => 'Primaria, Secundaria',
                'imagen_portada_ued' => $fotosEscuela[2],
                'galeria_ued' => [$fotosEscuela[2], $fotosEscuela[3]],
                'lat_ued' => -16.503112,
                'lng_ued' => -68.118445,
            ],
            [
                'nombre_ued' => 'Unidad Educativa Venezuela',
                'codigo_ued' => 'UEV004',
                'direccion_ued' => 'Zona Villa Fátima, La Paz',
                'descripcion_ued' => 'Institución orientada al acompañamiento personalizado del estudiante y vinculación con la comunidad educativa.',
                'turno_ued' => 'Mañana',
                'niveles_ued' => 'Inicial, Primaria',
                'imagen_portada_ued' => $fotosEscuela[3],
                'galeria_ued' => [$fotosEscuela[3]],
                'lat_ued' => -16.532401,
                'lng_ued' => -68.135221,
            ],
            [
                'nombre_ued' => 'Unidad Educativa Bolivia Japón',
                'codigo_ued' => 'UEBJ005',
                'direccion_ued' => 'Zona Alto Obrajes, La Paz',
                'descripcion_ued' => 'Colegio con convenios de intercambio cultural y programas de idiomas. Formación bilingüe en niveles Primaria y Secundaria.',
                'telefono_ued' => '2-4567890',
                'correo_ued' => 'admisiones@boliviajapon.edu.bo',
                'turno_ued' => 'Mañana y Tarde',
                'niveles_ued' => 'Primaria, Secundaria',
                'imagen_portada_ued' => $fotosEscuela[0],
                'galeria_ued' => $fotosEscuela,
                'lat_ued' => -16.541902,
                'lng_ued' => -68.089112,
            ],
        ];

        foreach ($unidades as $unidad) {
            UnidadEducativa::query()->updateOrCreate(
                ['codigo_ued' => $unidad['codigo_ued']],
                [
                    ...$unidad,
                    'id_mun_ued' => $municipioLaPaz?->id_mun,
                    'id_dis_ued' => $distrito?->id_dis,
                ],
            );
        }
    }
}
