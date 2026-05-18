<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unidad_educativa', function (Blueprint $table) {
            $table->decimal('lat_ued', 10, 7)->nullable()->after('direccion_ued');
            $table->decimal('lng_ued', 10, 7)->nullable()->after('lat_ued');
        });
    }

    public function down(): void
    {
        Schema::table('unidad_educativa', function (Blueprint $table) {
            $table->dropColumn(['lat_ued', 'lng_ued']);
        });
    }
};
