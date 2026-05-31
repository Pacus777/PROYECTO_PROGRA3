<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Criterio;
use App\Models\Evaluacion;
use App\Models\Postulacion;
use App\Support\Geo\AStarPathfinder;
use App\Support\Geo\Haversine;

final class ProximidadEvaluacionService
{
    public function __construct(
        private readonly AStarPathfinder $pathfinder,
    ) {}

    /**
     * Calcula proximidad con A* y registra el criterio «Distancia domicilio».
     *
     * @return array{
     *     ok: bool,
     *     motivo?: string,
     *     distancia_km?: float,
     *     distancia_lineal_km?: float,
     *     pasos_astar?: int,
     *     puntaje?: float,
     *     evaluacion_id?: int
     * }|null
     */
    public function calcularParaPostulacion(Postulacion $postulacion): ?array
    {
        $postulacion->loadMissing([
            'estudiante',
            'ofertaAcademica.unidadEducativa',
        ]);

        $estudiante = $postulacion->estudiante;
        $unidad = $postulacion->ofertaAcademica?->unidadEducativa;

        if ($estudiante === null || $unidad === null) {
            return ['ok' => false, 'motivo' => 'Datos de postulación incompletos.'];
        }

        if ($estudiante->lat_est === null || $estudiante->lng_est === null) {
            return ['ok' => false, 'motivo' => 'El estudiante no tiene domicilio georreferenciado (lat/lng).'];
        }

        if ($unidad->lat_ued === null || $unidad->lng_ued === null) {
            return ['ok' => false, 'motivo' => 'La unidad educativa no tiene coordenadas en el mapa.'];
        }

        $criterio = $this->criterioDistancia();
        if ($criterio === null) {
            return ['ok' => false, 'motivo' => 'No existe el criterio geográfico «Distancia domicilio». Ejecute los seeders.'];
        }

        $latEst = (float) $estudiante->lat_est;
        $lngEst = (float) $estudiante->lng_est;
        $latUe = (float) $unidad->lat_ued;
        $lngUe = (float) $unidad->lng_ued;

        $ruta = $this->pathfinder->findBetween(
            $latEst,
            $lngEst,
            $latUe,
            $lngUe,
            (int) config('proximidad.grid_size', 48),
        );

        $distanciaKm = $ruta['distancia_km'];
        $puntaje = $this->distanciaAPuntaje($distanciaKm);

        $observacion = sprintf(
            'A*: %s · %.2f km estimados (lineal %.2f km) · %d pasos en cuadrícula · domicilio → %s',
            $ruta['found'] ? 'ruta encontrada' : 'sin ruta (usa lineal)',
            $distanciaKm,
            $ruta['distancia_lineal_km'],
            $ruta['pasos'],
            $unidad->nombre_ued ?? 'UE',
        );

        $evaluacion = Evaluacion::query()->updateOrCreate(
            [
                'id_pos_eva' => $postulacion->id_pos,
                'id_cri_eva' => $criterio->id_cri,
            ],
            [
                'puntaje_eva' => $puntaje,
                'observaciones_eva' => $observacion,
            ],
        );

        return [
            'ok' => true,
            'distancia_km' => $distanciaKm,
            'distancia_lineal_km' => $ruta['distancia_lineal_km'],
            'pasos_astar' => $ruta['pasos'],
            'puntaje' => $puntaje,
            'evaluacion_id' => $evaluacion->id_eva,
        ];
    }

    public function distanciaAPuntaje(float $distanciaKm): float
    {
        $max = max(1.0, (float) config('proximidad.distancia_max_km', 12));
        $ratio = min(1.0, max(0.0, $distanciaKm / $max));

        return round(100 * (1 - $ratio), 2);
    }

    public function preview(float $latEst, float $lngEst, float $latUe, float $lngUe): array
    {
        $ruta = $this->pathfinder->findBetween(
            $latEst,
            $lngEst,
            $latUe,
            $lngUe,
            (int) config('proximidad.grid_size', 48),
        );

        return [
            ...$ruta,
            'puntaje' => $this->distanciaAPuntaje($ruta['distancia_km']),
            'lineal_km' => Haversine::distanceKm($latEst, $lngEst, $latUe, $lngUe),
        ];
    }

    private function criterioDistancia(): ?Criterio
    {
        return Criterio::query()
            ->whereHas('tipoCriterio', fn ($q) => $q->where('nombre_tic', 'geografico'))
            ->where('nombre_cri', 'like', '%Distancia%')
            ->first();
    }
}
