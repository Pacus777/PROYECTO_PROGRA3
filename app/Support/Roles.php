<?php

declare(strict_types=1);

namespace App\Support;

final class Roles
{
    public const ADMIN_GENERAL = 'admin_general';

    public const ADMIN_INSTITUCIONAL = 'admin_institucional';

    public const TUTOR = 'tutor';

    /** @return list<string> */
    public static function assignable(): array
    {
        return [
            self::ADMIN_GENERAL,
            self::ADMIN_INSTITUCIONAL,
            self::TUTOR,
        ];
    }

    /** @return list<string> */
    public static function all(): array
    {
        return self::assignable();
    }

    public static function isAssignable(?string $nombreRol): bool
    {
        return $nombreRol !== null && in_array($nombreRol, self::assignable(), true);
    }

    /** Nombre corto para tablas, sidebar y selects. */
    public static function label(?string $nombreRol): string
    {
        return match ($nombreRol) {
            self::ADMIN_GENERAL => 'Ministerio de Educación',
            self::ADMIN_INSTITUCIONAL => 'Unidad educativa (director / secretaría)',
            self::TUTOR => 'Tutor o apoderado',
            'estudiante' => 'Estudiante (sin acceso — descontinuado)',
            default => $nombreRol !== null && $nombreRol !== ''
                ? ucfirst(str_replace('_', ' ', $nombreRol))
                : '—',
        };
    }

    /** Texto de ayuda al crear usuarios o explicar permisos. */
    public static function description(?string $nombreRol): string
    {
        return match ($nombreRol) {
            self::ADMIN_GENERAL => 'Nivel nacional: ve todas las unidades educativas, postulaciones del país, reportes territoriales y gestión de cuentas.',
            self::ADMIN_INSTITUCIONAL => 'Nivel colegio: gestiona solo su unidad educativa (ofertas, cupos, postulaciones recibidas, documentos y resultados).',
            self::TUTOR => 'Familia o apoderado: registra postulantes, sube documentos y postula a colegios. No administra el establecimiento.',
            'estudiante' => 'Los postulantes no tienen cuenta; se identifican con RUDE.',
            default => '',
        };
    }

    public static function panelTitle(?string $nombreRol): string
    {
        return match ($nombreRol) {
            self::ADMIN_GENERAL => 'Panel del Ministerio de Educación',
            self::ADMIN_INSTITUCIONAL => 'Panel de la unidad educativa',
            self::TUTOR => 'Panel del tutor o apoderado',
            default => 'Panel de admisión escolar',
        };
    }

    public static function panelSubtitle(?string $nombreRol): string
    {
        return match ($nombreRol) {
            self::ADMIN_GENERAL => 'Visión nacional de postulaciones, unidades educativas, tutores y reportes por departamento o municipio.',
            self::ADMIN_INSTITUCIONAL => 'Gestiona la admisión de su colegio: ofertas, revisión de postulaciones y documentos.',
            self::TUTOR => 'Acompaña a sus hijos o tutelados en el proceso de postulación a unidades educativas.',
            default => 'Gestión del proceso de admisión escolar.',
        };
    }
}
