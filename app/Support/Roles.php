<?php

namespace App\Support;

final class Roles
{
    public const ADMIN_GENERAL = 'admin_general';

    public const ADMIN_INSTITUCIONAL = 'admin_institucional';

    public const TUTOR = 'tutor';

    public const ESTUDIANTE = 'estudiante';

    /** @return list<string> */
    public static function all(): array
    {
        return [
            self::ADMIN_GENERAL,
            self::ADMIN_INSTITUCIONAL,
            self::TUTOR,
            self::ESTUDIANTE,
        ];
    }
}
