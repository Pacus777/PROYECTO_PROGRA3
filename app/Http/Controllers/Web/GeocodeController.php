<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\GeocodingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeocodeController extends Controller
{
    public function __construct(
        private readonly GeocodingService $geocoding,
    ) {}

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        $results = $this->geocoding->search($validated['q']);

        return response()->json([
            'results' => $results,
            'message' => $results === []
                ? 'No encontramos esa dirección. Pruebe con zona y ciudad (ej. Bajo Llojeta, La Paz) o marque el punto en el mapa.'
                : (count(array_filter($results, static fn (array $r): bool => ! $r['approximate'])) === 0
                    ? 'No hay coincidencia exacta con el número de puerta. Elija la calle o zona más cercana y arrastre el marcador hasta su casa.'
                    : null),
        ]);
    }

    public function reverse(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $result = $this->geocoding->reverse((float) $validated['lat'], (float) $validated['lng']);

        return response()->json($result);
    }
}
