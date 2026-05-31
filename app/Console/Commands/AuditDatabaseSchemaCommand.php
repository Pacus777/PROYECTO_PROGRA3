<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class AuditDatabaseSchemaCommand extends Command
{
    protected $signature = 'db:schema-audit';

    protected $description = 'Compara tablas/columnas esperadas con la base de datos actual';

    /** @var array<string, list<string>> */
    private array $expected = [
        'oferta_academica' => [
            'id_oac', 'id_ges_oac', 'id_ued_oac', 'id_niv_oac', 'id_cur_oac', 'id_par_oac',
            'descripcion_oac', 'fecha_inicio_postulacion_oac', 'fecha_fin_postulacion_oac',
            'created_at', 'updated_at',
        ],
        'oferta_documento_requerido' => [
            'id_odr', 'id_oac_odr', 'id_tdo_odr', 'obligatorio_odr', 'created_at', 'updated_at',
        ],
        'postulacion' => [
            'id_pos', 'id_est_pos', 'id_oac_pos', 'id_ept_pos', 'prioridad_pos',
            'fecha_pos', 'observaciones_pos', 'aceptacion_cupo', 'fecha_aceptacion_cupo',
            'created_at', 'updated_at',
        ],
        'documento' => [
            'id_doc', 'id_pos_doc', 'id_tdo_doc', 'ruta_archivo_doc', 'estado_doc',
            'observacion_doc', 'fecha_revision_doc', 'created_at', 'updated_at',
        ],
        'procesamiento_ocr' => [
            'id_poc', 'id_doc_poc', 'texto_extraido_poc', 'confianza_poc', 'estado_poc',
            'created_at', 'updated_at',
        ],
    ];

    public function handle(): int
    {
        $issues = 0;

        foreach ($this->expected as $table => $columns) {
            if (! Schema::hasTable($table)) {
                $this->error("Tabla faltante: {$table}");
                $issues++;

                continue;
            }

            $existing = Schema::getColumnListing($table);

            foreach ($columns as $column) {
                if (! in_array($column, $existing, true)) {
                    $this->warn("Columna faltante: {$table}.{$column}");
                    $issues++;
                }
            }
        }

        if ($issues === 0) {
            $this->info('Esquema OK en tablas auditadas.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->line('Ejecute: php artisan migrate');

        return self::FAILURE;
    }
}
