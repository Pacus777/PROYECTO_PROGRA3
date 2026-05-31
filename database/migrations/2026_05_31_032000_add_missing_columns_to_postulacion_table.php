<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postulacion', function (Blueprint $table): void {
            if (! Schema::hasColumn('postulacion', 'prioridad_pos')) {
                $table->unsignedSmallInteger('prioridad_pos')->default(1);
            }

            if (! Schema::hasColumn('postulacion', 'aceptacion_cupo')) {
                $table->boolean('aceptacion_cupo')->nullable();
            }

            if (! Schema::hasColumn('postulacion', 'fecha_aceptacion_cupo')) {
                $table->timestamp('fecha_aceptacion_cupo')->nullable();
            }
        });

        if (Schema::hasColumn('postulacion', 'prioridad_pos')) {
            DB::table('postulacion')->whereNull('prioridad_pos')->update(['prioridad_pos' => 1]);
        }
    }

    public function down(): void
    {
        Schema::table('postulacion', function (Blueprint $table): void {
            $columns = array_filter([
                Schema::hasColumn('postulacion', 'prioridad_pos') ? 'prioridad_pos' : null,
                Schema::hasColumn('postulacion', 'aceptacion_cupo') ? 'aceptacion_cupo' : null,
                Schema::hasColumn('postulacion', 'fecha_aceptacion_cupo') ? 'fecha_aceptacion_cupo' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
