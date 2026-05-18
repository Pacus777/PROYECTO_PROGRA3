<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oferta_documento_requerido', function (Blueprint $table) {
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

        Schema::create('postulacion', function (Blueprint $table) {
            $table->bigIncrements('id_pos');

            $table->unsignedBigInteger('id_est_pos');
            $table->unsignedBigInteger('id_oac_pos');
            $table->unsignedBigInteger('id_ept_pos');

            $table->unsignedSmallInteger('prioridad_pos')->default(1);

            $table->timestamp('fecha_pos')->nullable();
            $table->text('observaciones_pos')->nullable();
            
            $table->boolean('aceptacion_cupo')->nullable();
            $table->timestamp('fecha_aceptacion_cupo')->nullable();

            $table->timestamps();

            $table->foreign('id_est_pos')
                ->references('id_est')
                ->on('estudiante')
                ->cascadeOnDelete();

            $table->foreign('id_oac_pos')
                ->references('id_oac')
                ->on('oferta_academica')
                ->cascadeOnDelete();

            $table->foreign('id_ept_pos')
                ->references('id_ept')
                ->on('estado_postulacion')
                ->restrictOnDelete();

            $table->unique(
                ['id_est_pos', 'id_oac_pos'],
                'pos_estudiante_oferta_unica'
            );
        });

        Schema::create('documento', function (Blueprint $table) {
            $table->bigIncrements('id_doc');

            $table->unsignedBigInteger('id_pos_doc');
            $table->unsignedBigInteger('id_tdo_doc');

            $table->string('ruta_archivo_doc', 500);
            $table->string('estado_doc', 40)->default('pendiente');

            $table->text('observacion_doc')->nullable();
            $table->timestamp('fecha_revision_doc')->nullable();

            $table->timestamps();

            $table->foreign('id_pos_doc')
                ->references('id_pos')
                ->on('postulacion')
                ->cascadeOnDelete();

            $table->foreign('id_tdo_doc')
                ->references('id_tdo')
                ->on('tipo_documento')
                ->restrictOnDelete();
        });

        Schema::create('procesamiento_ocr', function (Blueprint $table) {
            $table->bigIncrements('id_poc');

            $table->unsignedBigInteger('id_doc_poc');

            $table->longText('texto_extraido_poc')->nullable();
            $table->decimal('confianza_poc', 5, 2)->nullable();
            $table->string('estado_poc', 40)->default('pendiente');

            $table->timestamps();

            $table->foreign('id_doc_poc')
                ->references('id_doc')
                ->on('documento')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procesamiento_ocr');
        Schema::dropIfExists('documento');
        Schema::dropIfExists('postulacion');
        Schema::dropIfExists('oferta_documento_requerido');
    }
};