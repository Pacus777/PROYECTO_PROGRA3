@props([
    'lat',
    'lng',
    'nombre' => 'Unidad educativa',
    'direccion' => null,
])

@php
    $mapId = 'colegio-map-'.substr(md5((string) $lat.(string) $lng), 0, 8);
@endphp

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm']) }}>
    <div id="{{ $mapId }}" class="colegio-mapa-canvas h-64 w-full bg-slate-100 sm:h-72" role="img" aria-label="Mapa de ubicación del colegio"></div>
    @if($direccion)
        <div class="border-t border-slate-100 px-4 py-3 text-sm text-slate-600">
            {{ $direccion }}
        </div>
    @endif
    <div class="flex items-center justify-between gap-2 border-t border-slate-100 px-4 py-2.5 text-xs">
        <span class="font-mono text-slate-500">{{ number_format((float) $lat, 6) }}, {{ number_format((float) $lng, 6) }}</span>
        <a href="https://www.google.com/maps?q={{ $lat }},{{ $lng }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 font-semibold text-blue-700 hover:bg-blue-100">
            Abrir en Google Maps
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        </a>
    </div>
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
        <style>
            .colegio-mapa-canvas .leaflet-container { height: 100% !important; width: 100% !important; font-family: inherit; }
        </style>
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
        <script>
            window.__colegioMapasPendientes = window.__colegioMapasPendientes || [];

            window.initColegioMapa = function (config) {
                const el = document.getElementById(config.id);
                if (!el || !window.L?.map) {
                    window.__colegioMapasPendientes.push(config);
                    return;
                }
                if (el._leaflet_id) {
                    return;
                }
                const map = L.map(el, { zoomControl: true, scrollWheelZoom: false }).setView([config.lat, config.lng], 16);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                }).addTo(map);
                L.marker([config.lat, config.lng]).addTo(map).bindPopup(config.nombre);
                setTimeout(() => map.invalidateSize(true), 100);
                setTimeout(() => map.invalidateSize(true), 400);
            };

            function bootColegioMapasPendientes() {
                if (!window.L?.map) {
                    return;
                }
                while (window.__colegioMapasPendientes.length) {
                    window.initColegioMapa(window.__colegioMapasPendientes.shift());
                }
            }

            document.addEventListener('DOMContentLoaded', bootColegioMapasPendientes);
            if (document.readyState !== 'loading') {
                bootColegioMapasPendientes();
            }
        </script>
    @endpush
@endonce

@push('scripts')
    <script>
        window.initColegioMapa(@js([
            'id' => $mapId,
            'lat' => (float) $lat,
            'lng' => (float) $lng,
            'nombre' => $nombre,
        ]));
    </script>
@endpush
