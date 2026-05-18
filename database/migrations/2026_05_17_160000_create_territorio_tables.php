<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departamento', function (Blueprint $table) {
            $table->bigIncrements('id_dep');
            $table->string('codigo_dep', 4)->unique();
            $table->string('nombre_dep', 80);
            $table->timestamps();
        });

        Schema::create('provincia', function (Blueprint $table) {
            $table->bigIncrements('id_prov');
            $table->unsignedBigInteger('id_dep_prov');
            $table->string('nombre_prov', 120);
            $table->timestamps();

            $table->foreign('id_dep_prov')->references('id_dep')->on('departamento')->cascadeOnDelete();
            $table->unique(['id_dep_prov', 'nombre_prov']);
        });

        Schema::create('municipio', function (Blueprint $table) {
            $table->bigIncrements('id_mun');
            $table->unsignedBigInteger('id_prov_mun');
            $table->string('nombre_mun', 120);
            $table->timestamps();

            $table->foreign('id_prov_mun')->references('id_prov')->on('provincia')->cascadeOnDelete();
            $table->unique(['id_prov_mun', 'nombre_mun']);
        });

        Schema::create('distrito_educativo', function (Blueprint $table) {
            $table->bigIncrements('id_dis');
            $table->unsignedBigInteger('id_dep_dis');
            $table->string('codigo_dis', 16)->nullable();
            $table->string('nombre_dis', 160);
            $table->timestamps();

            $table->foreign('id_dep_dis')->references('id_dep')->on('departamento')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distrito_educativo');
        Schema::dropIfExists('municipio');
        Schema::dropIfExists('provincia');
        Schema::dropIfExists('departamento');
    }
};
