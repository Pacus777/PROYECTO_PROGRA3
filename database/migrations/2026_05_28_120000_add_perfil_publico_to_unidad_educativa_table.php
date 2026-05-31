<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unidad_educativa', function (Blueprint $table) {
            $table->text('descripcion_ued')->nullable()->after('direccion_ued');
            $table->string('telefono_ued', 40)->nullable()->after('descripcion_ued');
            $table->string('correo_ued', 120)->nullable()->after('telefono_ued');
            $table->string('turno_ued', 80)->nullable()->after('correo_ued');
            $table->string('niveles_ued', 160)->nullable()->after('turno_ued');
            $table->string('imagen_portada_ued', 500)->nullable()->after('niveles_ued');
            $table->json('galeria_ued')->nullable()->after('imagen_portada_ued');
        });
    }

    public function down(): void
    {
        Schema::table('unidad_educativa', function (Blueprint $table) {
            $table->dropColumn([
                'descripcion_ued',
                'telefono_ued',
                'correo_ued',
                'turno_ued',
                'niveles_ued',
                'imagen_portada_ued',
                'galeria_ued',
            ]);
        });
    }
};
