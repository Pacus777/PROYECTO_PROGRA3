<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacion', function (Blueprint $table) {
            $table->bigIncrements('id_not');
            $table->unsignedBigInteger('id_usu_not');
            $table->string('titulo_not', 180);
            $table->text('mensaje_not');
            $table->boolean('leida_not')->default(false);
            $table->string('enlace_not', 500)->nullable();
            $table->timestamps();

            $table->foreign('id_usu_not')->references('id_usu')->on('usuario')->cascadeOnDelete();
        });

        Schema::create('historial', function (Blueprint $table) {
            $table->bigIncrements('id_his');
            $table->string('tabla_his', 80);
            $table->unsignedBigInteger('id_registro_his');
            $table->string('accion_his', 40);
            $table->unsignedBigInteger('id_usu_his')->nullable();
            $table->json('datos_his')->nullable();
            $table->timestamp('creado_his')->useCurrent();

            $table->foreign('id_usu_his')->references('id_usu')->on('usuario')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial');
        Schema::dropIfExists('notificacion');
    }
};
