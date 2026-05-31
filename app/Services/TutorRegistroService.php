<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Persona;
use App\Models\Rol;
use App\Models\Tutor;
use App\Models\Usuario;
use App\Support\EstudianteIdentificador;
use App\Support\Roles;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class TutorRegistroService
{
    public function __construct(
        private readonly TutorVinculoService $vinculos,
    ) {}

    /**
     * @param array<string, mixed> $data
     * @return array{usuario: Usuario, vinculados: int, omitidos: int, nombres_vinculados: list<string>, estudiantes_vinculados: list<\App\Models\Estudiante>}
     */
    public function registrar(array $data): array
    {
        $rol = Rol::query()->where('nombre_rol', Roles::TUTOR)->firstOrFail();

        /** @var list<string> $rudes */
        $rudes = collect($data['rudes'] ?? [])
            ->map(fn ($r) => EstudianteIdentificador::normalizarRude((string) $r))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return DB::transaction(function () use ($data, $rol, $rudes): array {
            $persona = $this->resolverPersona($data);

            $usuario = Usuario::query()->create([
                'id_rol_usu' => $rol->id_rol,
                'id_per_usu' => $persona->id_per,
                'id_ued_usu' => null,
                'correo_usu' => $data['correo_usu'],
                'password_usu' => $data['password_usu'],
                'activo_usu' => true,
            ]);

            $tutor = Tutor::query()->firstOrCreate([
                'id_per_tut' => $persona->id_per,
            ]);

            $vinculados = 0;
            $omitidos = 0;
            $nombres = [];
            $estudiantesVinculados = [];

            foreach ($rudes as $rude) {
                $estudiante = EstudianteIdentificador::buscarPorCodigoOVinculo($rude);
                if ($estudiante === null) {
                    continue;
                }

                try {
                    $this->vinculos->attach($tutor, (int) $estudiante->id_est);
                    $vinculados++;
                    $estudiante->refresh()->loadMissing('persona');
                    $estudiantesVinculados[] = $estudiante;
                    $nombres[] = trim(
                        ($estudiante->persona->nombres_per ?? '').' '
                        .($estudiante->persona->ap_paterno_per ?? '')
                    );
                } catch (\RuntimeException) {
                    $omitidos++;
                }
            }

            if ($vinculados === 0 && $rudes !== []) {
                throw ValidationException::withMessages([
                    'rudes' => ['Ningún RUDE pudo vincularse. Verifique que el estudiante esté registrado en el sistema.'],
                ]);
            }

            return [
                'usuario' => $usuario->load(['persona', 'rol']),
                'vinculados' => $vinculados,
                'omitidos' => $omitidos,
                'nombres_vinculados' => $nombres,
                'estudiantes_vinculados' => $estudiantesVinculados,
            ];
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    private function resolverPersona(array $data): Persona
    {
        $ci = isset($data['ci_per']) && is_string($data['ci_per']) && $data['ci_per'] !== ''
            ? $data['ci_per']
            : null;

        $atributos = [
            'nombres_per' => $data['nombres_per'],
            'ap_paterno_per' => $data['ap_paterno_per'],
            'ap_materno_per' => $data['ap_materno_per'] ?? null,
            'telefono_per' => $data['telefono_per'] ?? null,
            'correo_per' => $data['correo_usu'],
        ];

        if ($ci !== null) {
            $existente = Persona::query()
                ->where('ci_per', $ci)
                ->with(['usuario', 'estudiante'])
                ->first();

            if ($existente !== null) {
                if ($existente->usuario !== null) {
                    throw ValidationException::withMessages([
                        'ci_per' => ['Este CI ya tiene una cuenta en el sistema. Inicie sesión con su correo registrado.'],
                    ]);
                }

                if ($existente->estudiante !== null) {
                    throw ValidationException::withMessages([
                        'ci_per' => ['Este CI pertenece a un estudiante. Use el CI del tutor o apoderado, no el del postulante.'],
                    ]);
                }

                $existente->update($atributos);

                return $existente->fresh();
            }
        }

        return Persona::query()->create([
            'ci_per' => $ci,
            ...$atributos,
        ]);
    }
}
