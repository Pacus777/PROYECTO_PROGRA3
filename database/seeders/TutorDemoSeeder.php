<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Estudiante;
use App\Models\OfertaAcademica;
use App\Models\Postulacion;
use App\Models\Persona;
use App\Models\EstadoPostulacion;
use App\Models\Rol;
use App\Models\Tutor;
use App\Models\UnidadEducativa;
use App\Models\Usuario;
use App\Support\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TutorDemoSeeder extends Seeder
{
    public function run(): void
    {
        $rolTutor = Rol::query()->where('nombre_rol', Roles::TUTOR)->first();
        if ($rolTutor === null) {
            return;
        }

        $unidad = UnidadEducativa::query()->orderBy('id_ued')->first();
        if ($unidad === null) {
            return;
        }

        $tutorCorreo = 'tutor@gmail.com';
        $tutorPersona = Persona::query()->updateOrCreate(
            ['correo_per' => $tutorCorreo],
            [
                'nombres_per' => 'Tutor',
                'ap_paterno_per' => 'Demo',
                'ap_materno_per' => 'Uno',
                'correo_per' => $tutorCorreo,
                'telefono_per' => null,
                'ci_per' => null,
            ],
        );

        Usuario::query()->updateOrCreate(
            ['correo_usu' => $tutorCorreo],
            [
                'id_rol_usu' => $rolTutor->id_rol,
                'id_per_usu' => $tutorPersona->id_per,
                'id_ued_usu' => null,
                'correo_usu' => $tutorCorreo,
                'password_usu' => Hash::make('tutor123'),
                'activo_usu' => true,
            ],
        );

        $tutor = Tutor::query()->updateOrCreate(
            ['id_per_tut' => $tutorPersona->id_per],
            ['id_per_tut' => $tutorPersona->id_per],
        );

        $estudianteCorreo = 'estudiante.demo@example.com';
        $estudiantePersona = Persona::query()->updateOrCreate(
            ['correo_per' => $estudianteCorreo],
            [
                'nombres_per' => 'Estudiante',
                'ap_paterno_per' => 'Demo',
                'ap_materno_per' => 'Vinculado',
                'correo_per' => $estudianteCorreo,
                'telefono_per' => null,
                'ci_per' => null,
            ],
        );

        $estudiante = Estudiante::query()->updateOrCreate(
            ['id_per_est' => $estudiantePersona->id_per],
            [
                'id_per_est' => $estudiantePersona->id_per,
                'codigo_est' => 'EST-DEMO-001',
                'rude_est' => 'RUDE-DEMO-001',
                'id_ued_mat_est' => $unidad->id_ued,
            ],
        );

        $tutor->estudiantes()->syncWithoutDetaching([$estudiante->id_est]);

        $oferta = OfertaAcademica::query()
            ->with('gestion')
            ->orderByDesc('id_oac')
            ->first();

        if ($oferta === null) {
            return;
        }

        $estadoBorradorId = EstadoPostulacion::query()
            ->where('nombre_ept', 'borrador')
            ->value('id_ept')
            ?? EstadoPostulacion::query()->orderBy('id_ept')->value('id_ept');

        if ($estadoBorradorId === null) {
            return;
        }

        Postulacion::query()->updateOrCreate(
            [
                'id_est_pos' => $estudiante->id_est,
                'id_oac_pos' => $oferta->id_oac,
            ],
            [
                'id_est_pos' => $estudiante->id_est,
                'id_oac_pos' => $oferta->id_oac,
                'id_ept_pos' => $estadoBorradorId,
                'fecha_pos' => now(),
                'observaciones_pos' => 'Postulación demo creada automáticamente para el tutor de prueba.',
            ],
        );
    }
}