<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutor', function (Blueprint $table) {
            $table->bigIncrements('id_tut');
            $table->unsignedBigInteger('id_per_tut');
            $table->timestamps();

            $table->foreign('id_per_tut')->references('id_per')->on('persona')->cascadeOnDelete();
            $table->unique('id_per_tut');
        });

        Schema::create('estudiante', function (Blueprint $table) {
            $table->bigIncrements('id_est');
            $table->unsignedBigInteger('id_per_est');
            $table->string('codigo_est', 40)->nullable()->unique();
            $table->timestamps();

            $table->foreign('id_per_est')->references('id_per')->on('persona')->cascadeOnDelete();
            $table->unique('id_per_est');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiante');
        Schema::dropIfExists('tutor');
    }
};
