<?php

namespace App\Services;

use App\Models\Estudiante;
use App\Models\Persona;
use App\Repositories\EstudianteRepository;
use App\Support\EstudianteIdentificador;
use Illuminate\Support\Facades\DB;

class EstudianteService
{
    public function __construct(
        private readonly EstudianteRepository $repository,
    ) {}

    public function listPaginated(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    public function find(int $id): ?Estudiante
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Estudiante
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

            $rude = EstudianteIdentificador::normalizarRude($data['rude_est'] ?? null);
            $codigo = isset($data['codigo_est']) && trim((string) $data['codigo_est']) !== ''
                ? trim((string) $data['codigo_est'])
                : $rude;

            return $this->repository->create([
                'id_per_est' => $persona->id_per,
                'codigo_est' => $codigo,
                'rude_est' => $rude,
                'id_ued_mat_est' => $data['id_ued_mat_est'] ?? null,
                'direccion_est' => $data['direccion_est'] ?? null,
                'lat_est' => $data['lat_est'] ?? null,
                'lng_est' => $data['lng_est'] ?? null,
            ]);
        });
    }

    public function update(Estudiante $estudiante, array $data): Estudiante
    {
        DB::transaction(function () use ($estudiante, $data) {
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
                $estudiante->persona?->update($personaAttrs);
            }

            $attrs = [];
            if (array_key_exists('rude_est', $data)) {
                $attrs['rude_est'] = EstudianteIdentificador::normalizarRude($data['rude_est']);
            }
            if (array_key_exists('codigo_est', $data)) {
                $attrs['codigo_est'] = trim((string) $data['codigo_est']) !== ''
                    ? trim((string) $data['codigo_est'])
                    : null;
            }
            if (array_key_exists('id_ued_mat_est', $data)) {
                $attrs['id_ued_mat_est'] = $data['id_ued_mat_est'] !== '' && $data['id_ued_mat_est'] !== null
                    ? (int) $data['id_ued_mat_est']
                    : null;
            }
            foreach (['direccion_est', 'lat_est', 'lng_est'] as $field) {
                if (array_key_exists($field, $data)) {
                    $attrs[$field] = $data[$field] !== '' && $data[$field] !== null
                        ? $data[$field]
                        : null;
                }
            }
            if ($attrs !== []) {
                $estudiante->update($attrs);
            }
        });

        return $estudiante->fresh(['persona', 'unidadMatriculaActual']);
    }

    public function delete(Estudiante $estudiante): void
    {
        DB::transaction(function () use ($estudiante) {
            $estudiante->delete();
        });
    }
}
