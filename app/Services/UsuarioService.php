<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\Rol;
use App\Models\Tutor;
use App\Models\Usuario;
use App\Repositories\UsuarioRepository;
use App\Support\Roles;
use Illuminate\Support\Facades\DB;

class UsuarioService
{
    public function __construct(
        private readonly UsuarioRepository $repository,
    ) {}

    public function listPaginated(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    public function find(int $id): ?Usuario
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Usuario
    {
        return DB::transaction(function () use ($data) {
            $persona = Persona::query()->create([
                'ci_per' => $data['ci_per'] ?? null,
                'nombres_per' => $data['nombres_per'],
                'ap_paterno_per' => $data['ap_paterno_per'],
                'ap_materno_per' => $data['ap_materno_per'] ?? null,
                'fecha_nac_per' => $data['fecha_nac_per'] ?? null,
                'genero_per' => $data['genero_per'] ?? null,
                'correo_per' => $data['correo_per'] ?? null,
                'telefono_per' => $data['telefono_per'] ?? null,
            ]);

            $usuario = $this->repository->create([
                'id_rol_usu' => $data['id_rol_usu'],
                'id_per_usu' => $persona->id_per,
                'id_ued_usu' => $data['id_ued_usu'] ?? null,
                'correo_usu' => $data['correo_usu'],
                'password_usu' => $data['password_usu'],
                'activo_usu' => $data['activo_usu'] ?? true,
            ]);

            $this->syncTutorProfileForUsuario($usuario, (int) $data['id_rol_usu']);

            return $usuario;
        });
    }

    public function update(Usuario $usuario, array $data): Usuario
    {
        DB::transaction(function () use ($usuario, $data) {
            $personaAttrs = collect($data)->only([
                'ci_per',
                'nombres_per',
                'ap_paterno_per',
                'ap_materno_per',
                'fecha_nac_per',
                'genero_per',
                'correo_per',
                'telefono_per',
            ])->filter(fn ($v) => $v !== null)->all();

            if ($personaAttrs !== []) {
                $usuario->persona?->update($personaAttrs);
            }

            $usuarioData = collect($data)
                ->only(['id_rol_usu', 'id_ued_usu', 'correo_usu', 'password_usu', 'activo_usu'])
                ->filter(fn ($v) => $v !== null)
                ->all();

            if ($usuarioData !== []) {
                $usuario->update($usuarioData);
            }

            $usuario->refresh();
            $this->syncTutorProfileForUsuario($usuario);
        });

        return $usuario->fresh(['persona', 'rol', 'unidadEducativa']);
    }

    public function delete(Usuario $usuario): void
    {
        DB::transaction(function () use ($usuario) {
            $usuario->delete();
        });
    }

    private function syncTutorProfileForUsuario(Usuario $usuario, ?int $rolId = null): void
    {
        if ($usuario->id_per_usu === null) {
            return;
        }

        $effectiveRolId = $rolId ?? (int) $usuario->id_rol_usu;
        $isTutorRole = Rol::query()
            ->where('id_rol', $effectiveRolId)
            ->where('nombre_rol', Roles::TUTOR)
            ->exists();

        if (! $isTutorRole) {
            return;
        }

        Tutor::query()->firstOrCreate([
            'id_per_tut' => $usuario->id_per_usu,
        ]);
    }
}
