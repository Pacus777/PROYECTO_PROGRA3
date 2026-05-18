<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unidad_educativa', function (Blueprint $table) {
            $table->unsignedBigInteger('id_mun_ued')->nullable()->after('direccion_ued');
            $table->unsignedBigInteger('id_dis_ued')->nullable()->after('id_mun_ued');

            $table->foreign('id_mun_ued')->references('id_mun')->on('municipio')->nullOnDelete();
            $table->foreign('id_dis_ued')->references('id_dis')->on('distrito_educativo')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('unidad_educativa', function (Blueprint $table) {
            $table->dropForeign(['id_mun_ued']);
            $table->dropForeign(['id_dis_ued']);
            $table->dropColumn(['id_mun_ued', 'id_dis_ued']);
        });
    }
};
