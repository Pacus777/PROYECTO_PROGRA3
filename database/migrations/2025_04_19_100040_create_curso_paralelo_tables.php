<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curso', function (Blueprint $table) {
            $table->bigIncrements('id_cur');
            $table->unsignedBigInteger('id_niv_cur');
            $table->string('nombre_cur', 80);
            $table->timestamps();

            $table->foreign('id_niv_cur')->references('id_niv')->on('nivel')->cascadeOnDelete();
        });

        Schema::create('paralelo', function (Blueprint $table) {
            $table->bigIncrements('id_par');
            $table->unsignedBigInteger('id_cur_par');
            $table->string('nombre_par', 16);
            $table->timestamps();

            $table->foreign('id_cur_par')->references('id_cur')->on('curso')->cascadeOnDelete();
            $table->unique(['id_cur_par', 'nombre_par']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paralelo');
        Schema::dropIfExists('curso');
    }
};
