<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estudiante', function (Blueprint $table): void {
            if (! Schema::hasColumn('estudiante', 'direccion_est')) {
                $table->string('direccion_est', 255)->nullable()->after('id_ued_mat_est');
            }
            if (! Schema::hasColumn('estudiante', 'lat_est')) {
                $table->decimal('lat_est', 10, 7)->nullable()->after('direccion_est');
            }
            if (! Schema::hasColumn('estudiante', 'lng_est')) {
                $table->decimal('lng_est', 10, 7)->nullable()->after('lat_est');
            }
        });
    }

    public function down(): void
    {
        Schema::table('estudiante', function (Blueprint $table): void {
            $columns = array_filter([
                Schema::hasColumn('estudiante', 'direccion_est') ? 'direccion_est' : null,
                Schema::hasColumn('estudiante', 'lat_est') ? 'lat_est' : null,
                Schema::hasColumn('estudiante', 'lng_est') ? 'lng_est' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
