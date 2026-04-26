<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('persona', function (Blueprint $table) {
            $table->bigIncrements('id_per');
            $table->string('ci_per', 32)->nullable()->unique();
            $table->string('nombres_per', 120);
            $table->string('ap_paterno_per', 80);
            $table->string('ap_materno_per', 80)->nullable();
            $table->date('fecha_nac_per')->nullable();
            $table->char('genero_per', 1)->nullable();
            $table->string('correo_per', 160)->nullable();
            $table->string('telefono_per', 40)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persona');
    }
};
