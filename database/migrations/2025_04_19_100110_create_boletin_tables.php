<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_boletin', function (Blueprint $table) {
            $table->bigIncrements('id_dbo');
            $table->unsignedBigInteger('id_est_dbo');
            $table->unsignedBigInteger('id_ges_dbo')->nullable();
            $table->string('materia_dbo', 120)->nullable();
            $table->decimal('nota_dbo', 5, 2)->nullable();
            $table->timestamps();

            $table->foreign('id_est_dbo')->references('id_est')->on('estudiante')->cascadeOnDelete();
            $table->foreign('id_ges_dbo')->references('id_ges')->on('gestion')->nullOnDelete();
        });

        Schema::create('resumen_boletin', function (Blueprint $table) {
            $table->bigIncrements('id_rbo');
            $table->unsignedBigInteger('id_est_rbo');
            $table->unsignedBigInteger('id_ges_rbo')->nullable();
            $table->decimal('promedio_rbo', 5, 2)->nullable();
            $table->timestamps();

            $table->foreign('id_est_rbo')->references('id_est')->on('estudiante')->cascadeOnDelete();
            $table->foreign('id_ges_rbo')->references('id_ges')->on('gestion')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resumen_boletin');
        Schema::dropIfExists('detalle_boletin');
    }
};
