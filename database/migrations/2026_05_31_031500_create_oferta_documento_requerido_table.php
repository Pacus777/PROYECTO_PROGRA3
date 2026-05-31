<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('oferta_documento_requerido')) {
            return;
        }

        Schema::create('oferta_documento_requerido', function (Blueprint $table): void {
            $table->bigIncrements('id_odr');

            $table->unsignedBigInteger('id_oac_odr');
            $table->unsignedBigInteger('id_tdo_odr');
            $table->boolean('obligatorio_odr')->default(true);

            $table->timestamps();

            $table->foreign('id_oac_odr')
                ->references('id_oac')
                ->on('oferta_academica')
                ->cascadeOnDelete();

            $table->foreign('id_tdo_odr')
                ->references('id_tdo')
                ->on('tipo_documento')
                ->restrictOnDelete();

            $table->unique(
                ['id_oac_odr', 'id_tdo_odr'],
                'odr_oferta_tipo_unica'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oferta_documento_requerido');
    }
};
