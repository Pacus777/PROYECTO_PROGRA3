<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unidad_educativa', function (Blueprint $table) {
            $table->bigIncrements('id_ued');
            $table->string('nombre_ued', 200);
            $table->string('codigo_ued', 32)->nullable()->unique();
            $table->string('direccion_ued', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unidad_educativa');
    }
};
