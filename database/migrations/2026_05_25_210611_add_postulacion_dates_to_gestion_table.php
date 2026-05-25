<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gestion', function (Blueprint $table) {
            $table->dateTime('fecha_inicio_postulacion_ges')->nullable()->after('fecha_fin_ges');
            $table->dateTime('fecha_fin_postulacion_ges')->nullable()->after('fecha_inicio_postulacion_ges');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gestion', function (Blueprint $table) {
            $table->dropColumn(['fecha_inicio_postulacion_ges', 'fecha_fin_postulacion_ges']);
        });
    }
};
