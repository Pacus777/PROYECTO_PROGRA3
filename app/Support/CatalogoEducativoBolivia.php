<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Catálogo base del sistema educativo plurinacional de Bolivia (educación regular).
 * Los paralelos los define cada unidad educativa (A, B, C…).
 */
final class CatalogoEducativoBolivia
{
    /**
     * @return array<string, list<string>>
     */
    public static function nivelesConCursos(): array
    {
        return [
            'Inicial' => [
                'Maternal',
                'Prekínder',
                'Kínder',
            ],
            'Primaria' => [
                '1ro de Primaria',
                '2do de Primaria',
                '3ro de Primaria',
                '4to de Primaria',
                '5to de Primaria',
                '6to de Primaria',
            ],
            'Secundaria' => [
                '1ro de Secundaria',
                '2do de Secundaria',
                '3ro de Secundaria',
                '4to de Secundaria',
                '5to de Secundaria',
                '6to de Secundaria',
            ],
        ];
    }
}
