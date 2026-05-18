<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oferta_academica', function (Blueprint $table) {
            $table->bigIncrements('id_oac');

            $table->unsignedBigInteger('id_ges_oac');
            $table->unsignedBigInteger('id_ued_oac');
            $table->unsignedBigInteger('id_niv_oac');
            $table->unsignedBigInteger('id_cur_oac');
            $table->unsignedBigInteger('id_par_oac');

            $table->string('descripcion_oac', 255)->nullable();

            // Fechas oficiales del periodo de postulación de esta oferta
            $table->dateTime('fecha_inicio_postulacion_oac');
            $table->dateTime('fecha_fin_postulacion_oac');

            $table->timestamps();

            $table->foreign('id_ges_oac')
                ->references('id_ges')
                ->on('gestion')
                ->cascadeOnDelete();

            $table->foreign('id_ued_oac')
                ->references('id_ued')
                ->on('unidad_educativa')
                ->cascadeOnDelete();

            $table->foreign('id_niv_oac')
                ->references('id_niv')
                ->on('nivel')
                ->cascadeOnDelete();

            $table->foreign('id_cur_oac')
                ->references('id_cur')
                ->on('curso')
                ->cascadeOnDelete();

            $table->foreign('id_par_oac')
                ->references('id_par')
                ->on('paralelo')
                ->cascadeOnDelete();

            $table->unique(
                ['id_ges_oac', 'id_ued_oac', 'id_niv_oac', 'id_cur_oac', 'id_par_oac'],
                'oac_oferta_unica'
            );
        });

        Schema::create('cupo', function (Blueprint $table) {
            $table->bigIncrements('id_cup');

            $table->unsignedBigInteger('id_oac_cup');
            $table->unsignedInteger('total_cup')->default(0);
            $table->unsignedInteger('disponibles_cup')->default(0);

            $table->timestamps();

            $table->foreign('id_oac_cup')
                ->references('id_oac')
                ->on('oferta_academica')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cupo');
        Schema::dropIfExists('oferta_academica');
    }
};