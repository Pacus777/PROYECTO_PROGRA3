<?php

namespace App\Repositories;

use App\Models\Gestion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GestionRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Gestion::query()
            ->orderByDesc('activa_ges')
            ->orderByDesc('id_ges')
            ->paginate($perPage);
    }

    public function find(int $gestionId): ?Gestion
    {
        return Gestion::query()->find($gestionId);
    }

    public function create(array $attributes): Gestion
    {
        return Gestion::query()->create($attributes);
    }

    public function update(Gestion $gestion, array $attributes): bool
    {
        return $gestion->update($attributes);
    }

    public function delete(Gestion $gestion): ?bool
    {
        return $gestion->delete();
    }
}
