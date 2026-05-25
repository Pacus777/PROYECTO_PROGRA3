<?php

namespace App\Services;

use App\Models\Gestion;
use App\Repositories\GestionRepository;
use Illuminate\Support\Facades\DB;

class GestionService
{
    public function __construct(
        private readonly GestionRepository $repository,
    ) {}

    public function listPaginated(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    public function find(int $id): ?Gestion
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Gestion
    {
        return DB::transaction(function () use ($data) {
            $attributes = collect($data)->only([
                'nombre_ges',
                'fecha_ini_ges',
                'fecha_fin_ges',
                'fecha_inicio_postulacion_ges',
                'fecha_fin_postulacion_ges',
                'activa_ges',
            ])->all();

            $makeActive = (bool) ($attributes['activa_ges'] ?? false);

            if ($makeActive) {
                Gestion::query()
                    ->where('activa_ges', true)
                    ->update(['activa_ges' => false]);
            }

            return $this->repository->create($attributes);
        });
    }

    public function update(Gestion $gestion, array $data): Gestion
    {
        return DB::transaction(function () use ($gestion, $data) {
            $attributes = collect($data)->only([
                'nombre_ges',
                'fecha_ini_ges',
                'fecha_fin_ges',
                'fecha_inicio_postulacion_ges',
                'fecha_fin_postulacion_ges',
                'activa_ges',
            ])->all();

            $makeActive = (bool) ($attributes['activa_ges'] ?? false);

            if ($makeActive) {
                Gestion::query()
                    ->where('id_ges', '!=', $gestion->id_ges)
                    ->where('activa_ges', true)
                    ->update(['activa_ges' => false]);
            }

            $gestion->update($attributes);

            return $gestion->fresh();
        });
    }

    public function delete(Gestion $gestion): void
    {
        $this->repository->delete($gestion);
    }
}
