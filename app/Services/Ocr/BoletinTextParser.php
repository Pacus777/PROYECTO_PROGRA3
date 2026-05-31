<?php

declare(strict_types=1);

namespace App\Services\Ocr;

use App\Models\DetalleBoletin;
use App\Models\Documento;
use App\Models\ResumenBoletin;
use Illuminate\Support\Facades\DB;

/**
 * Intenta extraer materias y notas del texto OCR de un boletín.
 * Heurístico: no reemplaza la revisión humana del administrador.
 */
final class BoletinTextParser
{
    public function __construct(
        private readonly BoletinLayoutParser $layout,
    ) {}

    /**
     * @return array{materias: int, promedio: float|null}
     */
    public function parseAndPersist(Documento $documento, string $text): array
    {
        $documento->loadMissing(['postulacion.ofertaAcademica', 'tipoDocumento']);

        $tipo = strtolower((string) ($documento->tipoDocumento->nombre_tdo ?? ''));
        if (! str_contains($tipo, 'boletin') && ! str_contains($tipo, 'libreta')) {
            return ['materias' => 0, 'promedio' => null];
        }

        $estudianteId = (int) ($documento->postulacion?->id_est_pos ?? 0);
        if ($estudianteId <= 0) {
            return ['materias' => 0, 'promedio' => null];
        }

        $gestionId = $documento->postulacion?->ofertaAcademica?->id_ges_oac;

        $estructura = $this->layout->parse($text);
        $materias = [];

        foreach ($estructura['materias'] as $fila) {
            $nota = $this->layout->notaFinalDeFila($fila['notas']);
            if ($nota !== null) {
                $materias[] = ['materia' => $fila['nombre'], 'nota' => $nota];
            }
        }

        $promedio = $estructura['promedio'] ?? $this->extractPromedio($text);

        if ($materias === [] && $promedio === null) {
            return ['materias' => 0, 'promedio' => null];
        }

        DB::transaction(function () use ($estudianteId, $gestionId, $materias, $promedio): void {
            DetalleBoletin::query()
                ->where('id_est_dbo', $estudianteId)
                ->when($gestionId, fn ($q) => $q->where('id_ges_dbo', $gestionId))
                ->delete();

            foreach ($materias as $row) {
                DetalleBoletin::query()->create([
                    'id_est_dbo' => $estudianteId,
                    'id_ges_dbo' => $gestionId,
                    'materia_dbo' => $row['materia'],
                    'nota_dbo' => $row['nota'],
                ]);
            }

            if ($promedio !== null) {
                ResumenBoletin::query()->updateOrCreate(
                    [
                        'id_est_rbo' => $estudianteId,
                        'id_ges_rbo' => $gestionId,
                    ],
                    ['promedio_rbo' => $promedio],
                );
            }
        });

        return ['materias' => count($materias), 'promedio' => $promedio];
    }

    private function extractPromedio(string $text): ?float
    {
        if (preg_match('/promedio\s*(?:general|final)?\s*[:\-]?\s*(\d{1,2}(?:[.,]\d{1,2})?)/iu', $text, $m)) {
            return (float) str_replace(',', '.', $m[1]);
        }

        return null;
    }
}
