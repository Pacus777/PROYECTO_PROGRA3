<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documento', function (Blueprint $table): void {
            if (! Schema::hasColumn('documento', 'observacion_doc')) {
                $table->text('observacion_doc')->nullable();
            }

            if (! Schema::hasColumn('documento', 'fecha_revision_doc')) {
                $table->timestamp('fecha_revision_doc')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('documento', function (Blueprint $table): void {
            $columns = array_filter([
                Schema::hasColumn('documento', 'observacion_doc') ? 'observacion_doc' : null,
                Schema::hasColumn('documento', 'fecha_revision_doc') ? 'fecha_revision_doc' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
