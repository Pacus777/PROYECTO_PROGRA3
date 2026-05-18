<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $rolId = DB::table('rol')->where('nombre_rol', 'estudiante')->value('id_rol');

        if ($rolId === null) {
            return;
        }

        DB::table('usuario')->where('id_rol_usu', $rolId)->delete();
        DB::table('rol')->where('id_rol', $rolId)->delete();
    }

    public function down(): void
    {
        DB::table('rol')->updateOrInsert(
            ['nombre_rol' => 'estudiante'],
            ['descripcion_rol' => 'Usuario estudiante del proceso de admisión.'],
        );
    }
};
