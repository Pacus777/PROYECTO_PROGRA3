<?php

declare(strict_types=1);

namespace App\Support\Geo;

/**
 * A* sobre una cuadrícula entre dos coordenadas geográficas.
 * La distancia en km se calibra con Haversine; A* modela el recorrido mínimo en la malla.
 */
final class AStarPathfinder
{
    /** @var list<array{0: int, 1: int, 2: float}> */
    private const NEIGHBORS = [
        [0, 1, 1.0],
        [1, 0, 1.0],
        [0, -1, 1.0],
        [-1, 0, 1.0],
        [1, 1, 1.41421356],
        [1, -1, 1.41421356],
        [-1, 1, 1.41421356],
        [-1, -1, 1.41421356],
    ];

    /**
     * @return array{
     *     found: bool,
     *     pasos: int,
     *     costo_grid: float,
     *     distancia_km: float,
     *     distancia_lineal_km: float
     * }
     */
    public function findBetween(float $latOrigen, float $lngOrigen, float $latDestino, float $lngDestino, int $gridSize = 48): array
    {
        $gridSize = max(16, min(128, $gridSize));
        $distanciaLinealKm = Haversine::distanceKm($latOrigen, $lngOrigen, $latDestino, $lngDestino);

        if ($distanciaLinealKm < 0.05) {
            return [
                'found' => true,
                'pasos' => 0,
                'costo_grid' => 0.0,
                'distancia_km' => $distanciaLinealKm,
                'distancia_lineal_km' => $distanciaLinealKm,
            ];
        }

        $padding = max(0.002, $distanciaLinealKm / 111.0 * 0.15);
        $minLat = min($latOrigen, $latDestino) - $padding;
        $maxLat = max($latOrigen, $latDestino) + $padding;
        $minLng = min($lngOrigen, $lngDestino) - $padding;
        $maxLng = max($lngOrigen, $lngDestino) + $padding;

        $start = $this->toCell($latOrigen, $lngOrigen, $minLat, $maxLat, $minLng, $maxLng, $gridSize);
        $goal = $this->toCell($latDestino, $lngDestino, $minLat, $maxLat, $minLng, $maxLng, $gridSize);

        $path = $this->aStar($start, $goal, $gridSize);

        if ($path === null) {
            return [
                'found' => false,
                'pasos' => 0,
                'costo_grid' => 0.0,
                'distancia_km' => $distanciaLinealKm,
                'distancia_lineal_km' => $distanciaLinealKm,
            ];
        }

        $costoGrid = $this->pathCost($path);
        $pasos = max(0, count($path) - 1);

        $linealGrid = $this->heuristic($start, $goal);
        $factor = $linealGrid > 0 ? ($costoGrid / $linealGrid) : 1.0;
        $distanciaKm = $distanciaLinealKm * min(1.35, max(1.0, $factor));

        return [
            'found' => true,
            'pasos' => $pasos,
            'costo_grid' => round($costoGrid, 2),
            'distancia_km' => round($distanciaKm, 3),
            'distancia_lineal_km' => round($distanciaLinealKm, 3),
        ];
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function toCell(float $lat, float $lng, float $minLat, float $maxLat, float $minLng, float $maxLng, int $size): array
    {
        $latSpan = max(0.000001, $maxLat - $minLat);
        $lngSpan = max(0.000001, $maxLng - $minLng);

        $row = (int) round((($lat - $minLat) / $latSpan) * ($size - 1));
        $col = (int) round((($lng - $minLng) / $lngSpan) * ($size - 1));

        return [
            max(0, min($size - 1, $row)),
            max(0, min($size - 1, $col)),
        ];
    }

    /**
     * @param array{0: int, 1: int} $start
     * @param array{0: int, 1: int} $goal
     * @return list<array{0: int, 1: int}>|null
     */
    private function aStar(array $start, array $goal, int $size): ?array
    {
        $startKey = $this->key($start);
        $goalKey = $this->key($goal);

        /** @var array<string, float> $gScore */
        $gScore = [$startKey => 0.0];
        /** @var array<string, float> $fScore */
        $fScore = [$startKey => $this->heuristic($start, $goal)];
        /** @var array<string, array{0: int, 1: int}> $cameFrom */
        $cameFrom = [];
        /** @var array<string, array{0: int, 1: int}> $open */
        $open = [$startKey => $start];

        while ($open !== []) {
            $currentKey = $this->lowestFScoreKey($open, $fScore);
            $current = $open[$currentKey];

            if ($currentKey === $goalKey) {
                return $this->reconstructPath($cameFrom, $current);
            }

            unset($open[$currentKey]);

            foreach (self::NEIGHBORS as [$dr, $dc, $moveCost]) {
                $neighbor = [$current[0] + $dr, $current[1] + $dc];

                if (! $this->inBounds($neighbor, $size)) {
                    continue;
                }

                $neighborKey = $this->key($neighbor);
                $tentative = ($gScore[$currentKey] ?? INF) + $moveCost;

                if ($tentative >= ($gScore[$neighborKey] ?? INF)) {
                    continue;
                }

                $cameFrom[$neighborKey] = $current;
                $gScore[$neighborKey] = $tentative;
                $fScore[$neighborKey] = $tentative + $this->heuristic($neighbor, $goal);
                $open[$neighborKey] = $neighbor;
            }
        }

        return null;
    }

    /**
     * @param array{0: int, 1: int} $a
     * @param array{0: int, 1: int} $b
     */
    private function heuristic(array $a, array $b): float
    {
        $dr = abs($a[0] - $b[0]);
        $dc = abs($a[1] - $b[1]);

        return max($dr, $dc) + (M_SQRT2 - 1) * min($dr, $dc);
    }

    /**
     * @param list<array{0: int, 1: int}> $path
     */
    private function pathCost(array $path): float
    {
        $cost = 0.0;

        for ($i = 1, $n = count($path); $i < $n; $i++) {
            $dr = abs($path[$i][0] - $path[$i - 1][0]);
            $dc = abs($path[$i][1] - $path[$i - 1][1]);
            $cost += ($dr === 1 && $dc === 1) ? M_SQRT2 : 1.0;
        }

        return $cost;
    }

    /**
     * @param array<string, array{0: int, 1: int}> $open
     * @param array<string, float> $fScore
     */
    private function lowestFScoreKey(array $open, array $fScore): string
    {
        $bestKey = array_key_first($open);
        $bestScore = $fScore[$bestKey] ?? INF;

        foreach (array_keys($open) as $key) {
            $score = $fScore[$key] ?? INF;
            if ($score < $bestScore) {
                $bestScore = $score;
                $bestKey = $key;
            }
        }

        return (string) $bestKey;
    }

    /**
     * @param array<string, array{0: int, 1: int}> $cameFrom
     * @param array{0: int, 1: int} $current
     * @return list<array{0: int, 1: int}>
     */
    private function reconstructPath(array $cameFrom, array $current): array
    {
        $path = [$current];
        $key = $this->key($current);

        while (isset($cameFrom[$key])) {
            $current = $cameFrom[$key];
            array_unshift($path, $current);
            $key = $this->key($current);
        }

        return $path;
    }

    /**
     * @param array{0: int, 1: int} $node
     */
    private function key(array $node): string
    {
        return $node[0].','.$node[1];
    }

    /**
     * @param array{0: int, 1: int} $node
     */
    private function inBounds(array $node, int $size): bool
    {
        return $node[0] >= 0 && $node[0] < $size && $node[1] >= 0 && $node[1] < $size;
    }
}
