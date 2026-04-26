<?php

namespace App\Repositories;

use App\Models\Usuario;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UsuarioRepository
{
    public function allWithRelations(): Collection
    {
        return Usuario::query()
            ->with(['persona', 'rol', 'unidadEducativa'])
            ->orderBy('id_usu')
            ->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Usuario::query()
            ->with(['persona', 'rol', 'unidadEducativa'])
            ->orderByDesc('id_usu')
            ->paginate($perPage);
    }

    public function find(int $usuId): ?Usuario
    {
        return Usuario::query()
            ->with(['persona', 'rol', 'unidadEducativa'])
            ->find($usuId);
    }

    public function create(array $attributes): Usuario
    {
        return Usuario::query()->create($attributes);
    }

    public function update(Usuario $usuario, array $attributes): bool
    {
        return $usuario->update($attributes);
    }

    public function delete(Usuario $usuario): ?bool
    {
        return $usuario->delete();
    }

    public function findByCorreo(string $correo): ?Usuario
    {
        return Usuario::query()
            ->with(['persona', 'rol', 'unidadEducativa'])
            ->where('correo_usu', $correo)
            ->first();
    }
}
