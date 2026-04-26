<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criterio', function (Blueprint $table) {
            $table->bigIncrements('id_cri');
            $table->unsignedBigInteger('id_tic_cri');
            $table->string('nombre_cri', 160);
            $table->decimal('peso_cri', 8, 4)->nullable();
            $table->timestamps();

            $table->foreign('id_tic_cri')->references('id_tic')->on('tipo_criterio')->cascadeOnDelete();
        });

        Schema::create('evaluacion', function (Blueprint $table) {
            $table->bigIncrements('id_eva');
            $table->unsignedBigInteger('id_pos_eva');
            $table->unsignedBigInteger('id_cri_eva');
            $table->decimal('puntaje_eva', 10, 4)->nullable();
            $table->text('observaciones_eva')->nullable();
            $table->timestamps();

            $table->foreign('id_pos_eva')->references('id_pos')->on('postulacion')->cascadeOnDelete();
            $table->foreign('id_cri_eva')->references('id_cri')->on('criterio')->cascadeOnDelete();
            $table->unique(['id_pos_eva', 'id_cri_eva']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluacion');
        Schema::dropIfExists('criterio');
    }
};
