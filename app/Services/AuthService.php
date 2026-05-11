<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\Rol;
use App\Models\Tutor;
use App\Models\Usuario;
use App\Repositories\UsuarioRepository;
use App\Support\Roles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private readonly UsuarioRepository $usuarios,
    ) {}

    public function register(array $data): array
    {
        if (($data['rol_nombre'] ?? Roles::TUTOR) !== Roles::TUTOR) {
            throw ValidationException::withMessages([
                'rol_nombre' => ['Solo se permite registro público con el rol tutor.'],
            ]);
        }

        $rol = Rol::query()->where('nombre_rol', Roles::TUTOR)->firstOrFail();

        return DB::transaction(function () use ($data, $rol) {
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

            $usuario = Usuario::query()->create([
                'id_rol_usu' => $rol->id_rol,
                'id_per_usu' => $persona->id_per,
                'id_ued_usu' => null,
                'correo_usu' => $data['correo_usu'],
                'password_usu' => $data['password_usu'],
                'activo_usu' => true,
            ]);

            Tutor::query()->firstOrCreate([
                'id_per_tut' => $persona->id_per,
            ]);

            $token = $usuario->createToken('api')->plainTextToken;

            return [
                'usuario' => $usuario->load(['persona', 'rol']),
                'token' => $token,
            ];
        });
    }

    public function login(string $correo, string $password): array
    {
        $usuario = $this->usuarios->findByCorreo($correo);

        if ($usuario === null || ! $usuario->activo_usu || ! Hash::check($password, $usuario->password_usu)) {
            throw ValidationException::withMessages([
                'correo_usu' => ['Credenciales incorrectas.'],
            ]);
        }

        $usuario->tokens()->delete();
        $token = $usuario->createToken('api')->plainTextToken;

        return [
            'usuario' => $usuario->load(['persona', 'rol', 'unidadEducativa']),
            'token' => $token,
        ];
    }

    public function logout(Usuario $usuario): void
    {
        $usuario->currentAccessToken()?->delete();
    }
}
