<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Estudiante;
use Illuminate\Database\Eloquent\Builder;

final class EstudianteIdentificador
{
    /** Registro Único de Estudiantes (Bolivia): solo dígitos, longitud habitual 8–12. */
    public const RUDE_REGEX = '/^\d{8,12}$/';

    public static function normalizarRude(?string $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $digits = preg_replace('/\D/', '', trim($valor));

        return $digits !== '' ? $digits : null;
    }

    public static function buscarPorCodigoOVinculo(string $identificador): ?Estudiante
    {
        $valor = trim($identificador);
        if ($valor === '') {
            return null;
        }

        $rude = self::normalizarRude($valor);

        return Estudiante::query()
            ->where(function (Builder $query) use ($valor, $rude): void {
                $query->where('codigo_est', $valor);
                if ($rude !== null) {
                    $query->orWhere('rude_est', $rude);
                }
            })
            ->first();
    }
}
