<?php

namespace App\Repositories;

use App\Models\Postulacion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostulacionRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Postulacion::query()
            ->with(['estudiante.persona', 'ofertaAcademica', 'estadoPostulacion'])
            ->orderByDesc('id_pos')
            ->paginate($perPage);
    }

    public function find(int $posId): ?Postulacion
    {
        return Postulacion::query()
            ->with(['estudiante.persona', 'ofertaAcademica', 'estadoPostulacion'])
            ->find($posId);
    }

    public function create(array $attributes): Postulacion
    {
        return Postulacion::query()->create($attributes);
    }

    public function update(Postulacion $postulacion, array $attributes): bool
    {
        return $postulacion->update($attributes);
    }

    public function delete(Postulacion $postulacion): ?bool
    {
        return $postulacion->delete();
    }
}
