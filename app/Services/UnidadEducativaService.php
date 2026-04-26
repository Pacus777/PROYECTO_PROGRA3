<?php

namespace App\Services;

use App\Models\UnidadEducativa;
use App\Repositories\UnidadEducativaRepository;

class UnidadEducativaService
{
    public function __construct(
        private readonly UnidadEducativaRepository $repository,
    ) {}

    public function listPaginated(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    public function find(int $id): ?UnidadEducativa
    {
        return $this->repository->find($id);
    }

    public function create(array $data): UnidadEducativa
    {
        return $this->repository->create($data);
    }

    public function update(UnidadEducativa $unidad, array $data): UnidadEducativa
    {
        $unidad->update(collect($data)->only(['nombre_ued', 'codigo_ued', 'direccion_ued'])->all());

        return $unidad->fresh();
    }

    public function delete(UnidadEducativa $unidad): void
    {
        $unidad->delete();
    }
}
