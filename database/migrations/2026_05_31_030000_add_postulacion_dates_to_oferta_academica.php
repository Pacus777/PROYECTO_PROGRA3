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
        if (Schema::hasColumn('oferta_academica', 'fecha_inicio_postulacion_oac')) {
            return;
        }

        Schema::table('oferta_academica', function (Blueprint $table): void {
            $table->dateTime('fecha_inicio_postulacion_oac')->nullable();
            $table->dateTime('fecha_fin_postulacion_oac')->nullable();
        });

        $inicio = now()->subMonths(1)->startOfDay();
        $fin = now()->addMonths(6)->endOfDay();

        DB::table('oferta_academica')->update([
            'fecha_inicio_postulacion_oac' => $inicio,
            'fecha_fin_postulacion_oac' => $fin,
        ]);

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE oferta_academica ALTER COLUMN fecha_inicio_postulacion_oac SET NOT NULL');
            DB::statement('ALTER TABLE oferta_academica ALTER COLUMN fecha_fin_postulacion_oac SET NOT NULL');
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('oferta_academica', 'fecha_inicio_postulacion_oac')) {
            return;
        }

        Schema::table('oferta_academica', function (Blueprint $table): void {
            $table->dropColumn(['fecha_inicio_postulacion_oac', 'fecha_fin_postulacion_oac']);
        });
    }
};
