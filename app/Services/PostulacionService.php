<?php

namespace App\Services;

use App\Models\EstadoPostulacion;
use App\Models\Postulacion;
use App\Repositories\PostulacionRepository;
use Illuminate\Support\Facades\DB;

class PostulacionService
{
    public function __construct(
        private readonly PostulacionRepository $repository,
    ) {}

    public function listPaginated(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    public function find(int $id): ?Postulacion
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Postulacion
    {
        return DB::transaction(function () use ($data) {
            $eptId = $data['id_ept_pos'] ?? EstadoPostulacion::query()
                ->where('nombre_ept', 'borrador')
                ->value('id_ept');

            if ($eptId === null) {
                $eptId = EstadoPostulacion::query()
                    ->orderBy('id_ept')
                    ->value('id_ept');
            }

            return $this->repository->create([
                'id_est_pos' => $data['id_est_pos'],
                'id_oac_pos' => $data['id_oac_pos'],
                'id_ept_pos' => $eptId,
                'prioridad_pos' => $data['prioridad_pos'],
                'fecha_pos' => $data['fecha_pos'] ?? now(),
                'observaciones_pos' => $data['observaciones_pos'] ?? null,
            ]);
        });
    }

    public function update(Postulacion $postulacion, array $data): Postulacion
    {
        $postulacion->update(collect($data)->only([
            'id_est_pos',
            'id_oac_pos',
            'id_ept_pos',
            'prioridad_pos',
            'fecha_pos',
            'observaciones_pos',
        ])->all());

        return $postulacion->fresh([
            'estudiante.persona',
            'ofertaAcademica',
            'estadoPostulacion',
        ]);
    }

    public function delete(Postulacion $postulacion): void
    {
        $postulacion->delete();
    }
}