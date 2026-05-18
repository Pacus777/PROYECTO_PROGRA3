<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resultado', function (Blueprint $table) {
            $table->bigIncrements('id_res');
            $table->unsignedBigInteger('id_pos_res');
            $table->decimal('puntaje_total_res', 12, 4)->nullable();
            $table->unsignedInteger('clasificacion_res')->nullable();
            $table->timestamps();

            $table->foreign('id_pos_res')->references('id_pos')->on('postulacion')->cascadeOnDelete();
            $table->unique('id_pos_res');
        });

        Schema::create('asignacion', function (Blueprint $table) {
            $table->bigIncrements('id_asi');
            $table->unsignedBigInteger('id_pos_asi');
            $table->unsignedBigInteger('id_cup_asi')->nullable();
            $table->string('estado_asi', 40)->default('pendiente');
            $table->timestamp('fecha_asi')->nullable();
            $table->timestamp('fecha_limite_respuesta_asi')->nullable();
            $table->timestamps();

            $table->foreign('id_pos_asi')->references('id_pos')->on('postulacion')->cascadeOnDelete();
            $table->foreign('id_cup_asi')->references('id_cup')->on('cupo')->nullOnDelete();
        });

        Schema::create('lista_espera', function (Blueprint $table) {
            $table->bigIncrements('id_les');
            $table->unsignedBigInteger('id_pos_les');
            $table->unsignedBigInteger('id_oac_les');
            $table->unsignedInteger('orden_les')->default(0);
            $table->timestamps();

            $table->foreign('id_pos_les')->references('id_pos')->on('postulacion')->cascadeOnDelete();
            $table->foreign('id_oac_les')->references('id_oac')->on('oferta_academica')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lista_espera');
        Schema::dropIfExists('asignacion');
        Schema::dropIfExists('resultado');
    }
};
