<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Rol;
use App\Models\Tutor;
use App\Models\Usuario;
use App\Support\Roles;
use Illuminate\Console\Command;

class BackfillTutorProfilesCommand extends Command
{
    protected $signature = 'app:backfill-tutor-profiles {--dry-run : Solo muestra cu\u00e1ntos faltan sin insertar}';

    protected $description = 'Crea registros faltantes en la tabla tutor para usuarios con rol tutor.';

    public function handle(): int
    {
        $rolTutor = Rol::query()
            ->where('nombre_rol', Roles::TUTOR)
            ->first();

        if ($rolTutor === null) {
            $this->error('No se encontr\u00f3 el rol tutor en la tabla rol.');

            return self::FAILURE;
        }

        $usuariosSinPerfil = Usuario::query()
            ->where('id_rol_usu', $rolTutor->id_rol)
            ->whereNotNull('id_per_usu')
            ->whereNotIn('id_per_usu', Tutor::query()->select('id_per_tut'))
            ->get(['id_usu', 'id_per_usu', 'correo_usu']);

        $faltantes = $usuariosSinPerfil->count();

        if ($faltantes === 0) {
            $this->info('No hay usuarios tutor pendientes de vincular.');

            return self::SUCCESS;
        }

        $this->line("Se detectaron {$faltantes} usuario(s) tutor sin perfil en tabla tutor.");

        if ((bool) $this->option('dry-run')) {
            $this->line('Modo dry-run: no se realizaron inserciones.');

            return self::SUCCESS;
        }

        $creados = 0;

        foreach ($usuariosSinPerfil as $usuario) {
            Tutor::query()->firstOrCreate([
                'id_per_tut' => (int) $usuario->id_per_usu,
            ]);

            $creados++;
        }

        $this->info("Perfiles tutor creados: {$creados}.");

        return self::SUCCESS;
    }
}
