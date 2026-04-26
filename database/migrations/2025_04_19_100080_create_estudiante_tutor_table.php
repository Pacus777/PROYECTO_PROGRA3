<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiante_tutor', function (Blueprint $table) {
            $table->bigIncrements('id_ett');
            $table->unsignedBigInteger('id_est_ett');
            $table->unsignedBigInteger('id_tut_ett');
            $table->timestamps();

            $table->foreign('id_est_ett')->references('id_est')->on('estudiante')->cascadeOnDelete();
            $table->foreign('id_tut_ett')->references('id_tut')->on('tutor')->cascadeOnDelete();
            $table->unique(['id_est_ett', 'id_tut_ett']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiante_tutor');
    }
};
