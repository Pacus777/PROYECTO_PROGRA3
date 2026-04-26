<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->bigIncrements('id_usu');
            $table->unsignedBigInteger('id_rol_usu');
            $table->unsignedBigInteger('id_per_usu');
            $table->unsignedBigInteger('id_ued_usu')->nullable();
            $table->string('correo_usu', 160)->unique();
            $table->string('password_usu');
            $table->boolean('activo_usu')->default(true);
            $table->string('remember_token_usu', 100)->nullable();
            $table->timestamps();

            $table->foreign('id_rol_usu')->references('id_rol')->on('rol')->restrictOnDelete();
            $table->foreign('id_per_usu')->references('id_per')->on('persona')->cascadeOnDelete();
            $table->foreign('id_ued_usu')->references('id_ued')->on('unidad_educativa')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
