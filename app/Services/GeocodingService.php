<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class GeocodingService
{
    /** @var list<string> */
    private const CITIES = [
        'La Paz', 'El Alto', 'Cochabamba', 'Santa Cruz', 'Oruro', 'Potosí', 'Potosi',
        'Sucre', 'Tarija', 'Trinidad', 'Cobija', 'Beni', 'Pando',
    ];

    /**
     * @return list<array{lat: float, lng: float, label: string, approximate: bool, source: string}>
     */
    public function search(string $query): array
    {
        $queries = $this->buildQueryVariants($query);
        $seen = [];
        $all = [];

        foreach ($queries as $q) {
            foreach ($this->searchAllProviders($q) as $hit) {
                $key = round($hit['lat'], 5).','.round($hit['lng'], 5);
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;
                $all[] = $hit;
            }

            $precise = array_values(array_filter($all, static fn (array $h): bool => ! $h['approximate']));
            if ($precise !== []) {
                return array_slice($precise, 0, 5);
            }

            if (count($all) >= 5) {
                break;
            }
        }

        usort($all, static fn (array $a, array $b): int => ($a['approximate'] <=> $b['approximate'])
            ?: strcmp($a['label'], $b['label']));

        return array_slice($all, 0, 5);
    }

    /**
     * @return array{label: string|null}
     */
    public function reverse(float $lat, float $lng): array
    {
        $googleKey = config('services.google.maps_key');
        if ($this->useGoogleMaps() && is_string($googleKey) && strlen(trim($googleKey)) > 10) {
            try {
                $response = Http::timeout(8)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'latlng' => $lat.','.$lng,
                    'key' => trim($googleKey),
                    'language' => 'es',
                    'region' => 'bo',
                ]);
                $data = $response->json();
                $label = $data['results'][0]['formatted_address'] ?? null;
                if (is_string($label) && $label !== '') {
                    return ['label' => $label];
                }
            } catch (\Throwable $e) {
                Log::debug('Reverse geocode Google falló', ['message' => $e->getMessage()]);
            }
        }

        try {
            $response = Http::withHeaders($this->nominatimHeaders())
                ->timeout(8)
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'json',
                    'lat' => $lat,
                    'lon' => $lng,
                    'zoom' => 18,
                    'addressdetails' => 1,
                ]);
            $data = $response->json();
            $label = is_string($data['display_name'] ?? null) ? $data['display_name'] : null;

            return ['label' => $label];
        } catch (\Throwable $e) {
            Log::debug('Reverse geocode Nominatim falló', ['message' => $e->getMessage()]);

            return ['label' => null];
        }
    }

    /**
     * @return list<string>
     */
    private function buildQueryVariants(string $query): array
    {
        $base = trim(preg_replace('/\s+/u', ' ', $query) ?? $query);
        if ($base === '') {
            return [];
        }

        $variants = [];
        $push = function (string $value) use (&$variants): void {
            $value = trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
            if ($value === '') {
                return;
            }
            $variants[] = $value;
            if (stripos($value, 'bolivia') === false) {
                $variants[] = $value.', Bolivia';
            }
        };

        $push($base);

        $rawParts = array_values(array_filter(array_map(
            'trim',
            preg_split('/[,\\/]+/u', $base) ?: [],
        )));
        $cleanParts = array_values(array_filter(array_map(
            fn (string $p): string => $this->cleanAddressSegment($p),
            $rawParts,
        )));

        $city = $this->detectCity($cleanParts) ?? $this->detectCity($rawParts);

        $localParts = array_values(array_filter(
            $cleanParts,
            fn (string $p): bool => ! $this->isCityName($p) && ! $this->isDoorNumber($p),
        ));

        if ($city !== null) {
            if (isset($localParts[0])) {
                $push($localParts[0].', '.$city);
            }
            if (isset($localParts[1], $localParts[0])) {
                $push($localParts[1].', '.$localParts[0].', '.$city);
                $push('Calle '.$localParts[1].', '.$localParts[0].', '.$city);
            }
            if (count($localParts) >= 2) {
                $push(implode(', ', array_slice($localParts, 0, 2)).', '.$city);
            }
        }

        $simple = preg_replace(
            '/\b(zona|barrio|calle|c\.|avenida|av\.?|pasaje|psje\.?|n°|nº|no\.?|num\.?|numero|#)\b/ui',
            ' ',
            $base,
        );
        $simple = trim(preg_replace('/\s+/u', ' ', $simple ?? '') ?? '');
        if ($simple !== '' && mb_strtolower($simple) !== mb_strtolower($base)) {
            $push($simple);
        }

        return array_values(array_unique($variants));
    }

    private function cleanAddressSegment(string $part): string
    {
        $part = trim($part);
        $part = preg_replace('/\b(zona|barrio|calle|c\.|avenida|av\.?|pasaje|psje\.?)\b/ui', '', $part) ?? $part;
        $part = preg_replace('/\b(n°|nº|no\.?|num\.?|numero|#)\s*\d+\b/ui', '', $part) ?? $part;
        $part = preg_replace('/\b\d+\b/u', '', $part) ?? $part;

        return trim(preg_replace('/\s+/u', ' ', $part) ?? $part);
    }

    /**
     * @param list<string> $parts
     */
    private function detectCity(array $parts): ?string
    {
        foreach (array_reverse($parts) as $part) {
            if ($this->isCityName($part)) {
                return $part;
            }
        }

        return null;
    }

    private function isCityName(string $part): bool
    {
        $normalized = mb_strtolower(trim($part));
        foreach (self::CITIES as $city) {
            if ($normalized === mb_strtolower($city)) {
                return true;
            }
        }

        return (bool) preg_match('/^(la paz|el alto|santa cruz de la sierra|santa cruz)$/iu', $normalized);
    }

    private function isDoorNumber(string $part): bool
    {
        return (bool) preg_match('/^\d+$/u', trim($part));
    }

    /**
     * @return list<array{lat: float, lng: float, label: string, approximate: bool, source: string}>
     */
    private function searchAllProviders(string $query): array
    {
        if ($this->useGoogleMaps()) {
            $googleKey = config('maps.google_key') ?: config('services.google.maps_key');
            if (is_string($googleKey) && strlen(trim($googleKey)) > 10) {
                $google = $this->searchGoogle($query, trim($googleKey));
                if ($google !== []) {
                    return $google;
                }
            }
        }

        $nominatim = $this->searchNominatim($query);
        if ($nominatim !== []) {
            return $nominatim;
        }

        return $this->searchPhoton($query);
    }

    private function useGoogleMaps(): bool
    {
        return (config('maps.provider') ?: 'osm') === 'google';
    }

    /**
     * @return list<array{lat: float, lng: float, label: string, approximate: bool, source: string}>
     */
    private function searchGoogle(string $query, string $apiKey): array
    {
        try {
            $response = Http::timeout(8)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $query,
                'key' => $apiKey,
                'region' => 'bo',
                'language' => 'es',
                'components' => 'country:BO',
            ]);
            $data = $response->json();
            if (($data['status'] ?? '') !== 'OK' || ! is_array($data['results'] ?? null)) {
                return [];
            }

            $hits = [];
            foreach (array_slice($data['results'], 0, 5) as $row) {
                $loc = $row['geometry']['location'] ?? null;
                if (! is_array($loc)) {
                    continue;
                }
                $types = $row['types'] ?? [];
                $approximate = ! in_array('street_address', $types, true)
                    && ! in_array('premise', $types, true)
                    && ! in_array('subpremise', $types, true);
                $hits[] = [
                    'lat' => (float) $loc['lat'],
                    'lng' => (float) $loc['lng'],
                    'label' => (string) ($row['formatted_address'] ?? $query),
                    'approximate' => $approximate,
                    'source' => 'google',
                ];
            }

            return $hits;
        } catch (\Throwable $e) {
            Log::debug('Geocode Google falló', ['query' => $query, 'message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * @return list<array{lat: float, lng: float, label: string, approximate: bool, source: string}>
     */
    private function searchNominatim(string $query): array
    {
        try {
            $response = Http::withHeaders($this->nominatimHeaders())
                ->timeout(8)
                ->get('https://nominatim.openstreetmap.org/search', [
                    'format' => 'json',
                    'limit' => 5,
                    'countrycodes' => 'bo',
                    'q' => $query,
                    'addressdetails' => 1,
                ]);

            if (! $response->successful()) {
                return [];
            }

            $data = $response->json();
            if (! is_array($data)) {
                return [];
            }

            return $this->mapNominatimHits($data, $query);
        } catch (\Throwable $e) {
            Log::debug('Geocode Nominatim falló', ['query' => $query, 'message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * @return list<array{lat: float, lng: float, label: string, approximate: bool, source: string}>
     */
    private function searchPhoton(string $query): array
    {
        try {
            $response = Http::timeout(8)
                ->get('https://photon.komoot.io/api/', [
                    'q' => $query,
                    'limit' => 5,
                    'lang' => 'es',
                    'bbox' => '-69.6,-22.9,-57.5,-9.7',
                ]);

            if (! $response->successful()) {
                return [];
            }

            $data = $response->json();
            $features = is_array($data['features'] ?? null) ? $data['features'] : [];
            $hits = [];

            foreach ($features as $feature) {
                $coords = $feature['geometry']['coordinates'] ?? null;
                if (! is_array($coords) || count($coords) < 2) {
                    continue;
                }
                $props = is_array($feature['properties'] ?? null) ? $feature['properties'] : [];
                $label = $this->formatPhotonLabel($props, $query);
                $type = (string) ($props['type'] ?? '');
                $approximate = ! in_array($type, ['house', 'building', 'street'], true);
                $hits[] = [
                    'lat' => (float) $coords[1],
                    'lng' => (float) $coords[0],
                    'label' => $label,
                    'approximate' => $approximate,
                    'source' => 'photon',
                ];
            }

            return $hits;
        } catch (\Throwable $e) {
            Log::debug('Geocode Photon falló', ['query' => $query, 'message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array{lat: float, lng: float, label: string, approximate: bool, source: string}>
     */
    private function mapNominatimHits(array $rows, string $query): array
    {
        $hits = [];
        foreach ($rows as $row) {
            if (! isset($row['lat'], $row['lon'])) {
                continue;
            }
            $type = (string) ($row['type'] ?? '');
            $class = (string) ($row['class'] ?? '');
            $approximate = ! in_array($type, ['house', 'building', 'address', 'residential'], true)
                && ! ($class === 'highway' && in_array($type, ['residential', 'living_street', 'tertiary'], true));
            $hits[] = [
                'lat' => (float) $row['lat'],
                'lng' => (float) $row['lon'],
                'label' => (string) ($row['display_name'] ?? $query),
                'approximate' => $approximate,
                'source' => 'nominatim',
            ];
        }

        return $hits;
    }

    /**
     * @param array<string, mixed> $props
     */
    private function formatPhotonLabel(array $props, string $fallback): string
    {
        $parts = array_filter([
            $props['street'] ?? null,
            $props['housenumber'] ?? null,
            $props['district'] ?? null,
            $props['city'] ?? null,
            $props['state'] ?? null,
            $props['country'] ?? null,
        ], static fn ($v): bool => is_string($v) && trim($v) !== '');

        if ($parts === []) {
            return $fallback;
        }

        return implode(', ', $parts);
    }

    /**
     * @return array<string, string>
     */
    private function nominatimHeaders(): array
    {
        $app = (string) config('app.name', 'AdmisionEscolar');

        return [
            'User-Agent' => $app.' Geocoder/1.0 (Laravel; admision escolar)',
            'Accept-Language' => 'es',
        ];
    }
}
