<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Estudiante;
use App\Models\Tutor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TutorVinculoService
{
    public function listTutores(int $perPage = 15): LengthAwarePaginator
    {
        return Tutor::query()
            ->with('persona')
            ->orderByDesc('id_tut')
            ->paginate($perPage);
    }

    public function estudiantesVinculados(Tutor $tutor): Collection
    {
        return $tutor->estudiantes()
            ->with(['persona', 'unidadMatriculaActual'])
            ->orderBy('id_est')
            ->get();
    }

    public function estudiantesNoVinculados(Tutor $tutor): Collection
    {
        $vinculados = $tutor->estudiantes()->pluck('estudiante.id_est');

        return Estudiante::query()
            ->with(['persona', 'unidadMatriculaActual'])
            ->whereNotIn('id_est', $vinculados)
            ->orderBy('id_est')
            ->get();
    }

    public function attach(Tutor $tutor, int $idEst): void
    {
        Estudiante::query()->findOrFail($idEst);

        $yaVinculado = $tutor->estudiantes()
            ->where('estudiante.id_est', $idEst)
            ->exists();

        if ($yaVinculado) {
            throw new \RuntimeException('El estudiante ya está vinculado a este tutor.');
        }

        $tutor->estudiantes()->attach($idEst);
    }

    public function detach(Tutor $tutor, int $idEst): void
    {
        $tutor->estudiantes()->detach($idEst);
    }

    public function tutoresVinculados(Estudiante $estudiante): Collection
    {
        return $estudiante->tutores()->with('persona')->orderBy('id_tut')->get();
    }

    public function tutoresDisponibles(Estudiante $estudiante): Collection
    {
        $vinculados = $estudiante->tutores()->pluck('tutor.id_tut');

        return Tutor::query()
            ->with('persona')
            ->whereNotIn('id_tut', $vinculados)
            ->orderBy('id_tut')
            ->get();
    }
}
