<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estudiante', function (Blueprint $table) {
            $table->string('rude_est', 20)->nullable()->unique()->after('codigo_est');
        });
    }

    public function down(): void
    {
        Schema::table('estudiante', function (Blueprint $table) {
            $table->dropUnique(['rude_est']);
            $table->dropColumn('rude_est');
        });
    }
};
