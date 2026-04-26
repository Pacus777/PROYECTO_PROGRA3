<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gestion', function (Blueprint $table) {
            $table->bigIncrements('id_ges');
            $table->string('nombre_ges', 32);
            $table->date('fecha_ini_ges')->nullable();
            $table->date('fecha_fin_ges')->nullable();
            $table->boolean('activa_ges')->default(false);
            $table->timestamps();
        });

        Schema::create('nivel', function (Blueprint $table) {
            $table->bigIncrements('id_niv');
            $table->string('nombre_niv', 80);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nivel');
        Schema::dropIfExists('gestion');
    }
};
