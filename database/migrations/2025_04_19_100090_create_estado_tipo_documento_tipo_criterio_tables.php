<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estado_postulacion', function (Blueprint $table) {
            $table->bigIncrements('id_ept');
            $table->string('nombre_ept', 80)->unique();
            $table->string('descripcion_ept', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('tipo_documento', function (Blueprint $table) {
            $table->bigIncrements('id_tdo');
            $table->string('nombre_tdo', 120);
            $table->timestamps();
        });

        Schema::create('tipo_criterio', function (Blueprint $table) {
            $table->bigIncrements('id_tic');
            $table->string('nombre_tic', 120);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_criterio');
        Schema::dropIfExists('tipo_documento');
        Schema::dropIfExists('estado_postulacion');
    }
};
