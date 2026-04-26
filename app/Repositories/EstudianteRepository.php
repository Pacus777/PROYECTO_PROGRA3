<?php

namespace App\Repositories;

use App\Models\Estudiante;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EstudianteRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Estudiante::query()
            ->with('persona')
            ->orderByDesc('id_est')
            ->paginate($perPage);
    }

    public function find(int $estId): ?Estudiante
    {
        return Estudiante::query()->with('persona')->find($estId);
    }

    public function create(array $attributes): Estudiante
    {
        return Estudiante::query()->create($attributes);
    }

    public function update(Estudiante $estudiante, array $attributes): bool
    {
        return $estudiante->update($attributes);
    }

    public function delete(Estudiante $estudiante): ?bool
    {
        return $estudiante->delete();
    }
}
