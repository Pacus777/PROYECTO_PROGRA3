<?php

namespace App\Repositories;

use App\Models\UnidadEducativa;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UnidadEducativaRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return UnidadEducativa::query()
            ->orderBy('nombre_ued')
            ->paginate($perPage);
    }

    public function find(int $uedId): ?UnidadEducativa
    {
        return UnidadEducativa::query()->find($uedId);
    }

    public function create(array $attributes): UnidadEducativa
    {
        return UnidadEducativa::query()->create($attributes);
    }

    public function update(UnidadEducativa $unidad, array $attributes): bool
    {
        return $unidad->update($attributes);
    }

    public function delete(UnidadEducativa $unidad): ?bool
    {
        return $unidad->delete();
    }
}
