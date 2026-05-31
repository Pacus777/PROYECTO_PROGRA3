@props([
    'addressName' => 'direccion_ued',
    'latName' => 'lat_ued',
    'lngName' => 'lng_ued',
    'address' => '',
    'lat' => null,
    'lng' => null,
    'readonly' => false,
    'pickerId' => null,
    'searchLabel' => 'Ubicación de la unidad',
    'searchPlaceholder' => 'Buscar calle, zona, barrio o ciudad en Bolivia…',
])

@php
    $pickerId = $pickerId ?? 'addr-' . substr(md5($addressName . ($latName ?? '')), 0, 8);
    $addressVal = old($addressName, $address);
    $latVal = old($latName, $lat);
    $lngVal = old($lngName, $lng);
    $googleKey = config('maps.google_key') ?: config('services.google.maps_key');
    $preferGoogle = (config('maps.provider') ?: 'osm') === 'google';
    $useGoogle = $preferGoogle && filled($googleKey) && strlen(trim((string) $googleKey)) > 10;
    $hasCoords = $latVal !== null && $latVal !== '' && $lngVal !== null && $lngVal !== '';
    $defaultLat = (float) config('maps.default_lat', -16.5);
    $defaultLng = (float) config('maps.default_lng', -68.15);
    $config = [
        'pickerId' => $pickerId,
        'address' => $addressVal ?? '',
        'lat' => $hasCoords ? (float) $latVal : $defaultLat,
        'lng' => $hasCoords ? (float) $lngVal : $defaultLng,
        'readonly' => (bool) $readonly,
        'useGoogle' => $useGoogle,
        'geocodeSearchUrl' => route('geocode.search'),
        'geocodeReverseUrl' => route('geocode.reverse'),
    ];
@endphp

<div
    class="address-location-picker overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
    data-address-picker-root
    data-google-key="{{ $googleKey }}"
    x-data="addressLocationPicker(@js($config))"
    x-init="init()"
>
    @unless($readonly)
        @if($useGoogle)
            <div class="border-b border-emerald-100 bg-emerald-50/80 px-4 py-2 text-xs text-emerald-900">
                <span class="font-semibold">Google Maps</span> — motor de mapas activo.
            </div>
        @else
            <div class="border-b border-teal-100 bg-teal-50/80 px-4 py-2 text-xs text-teal-900">
                <span class="font-semibold">OpenStreetMap</span> — mapa gratuito y de código abierto. Busque la zona o calle, elija una opción y ajuste el marcador en el mapa si hace falta.
            </div>
        @endif
    @endunless
    @unless($readonly)
        <div class="border-b border-slate-100 bg-gradient-to-br from-slate-50 to-indigo-50/40 p-4 sm:p-5">
            <label for="{{ $pickerId }}-search" class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-800">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </span>
                {{ $searchLabel }}
            </label>
            <div class="relative flex gap-2">
                <input
                    type="text"
                    id="{{ $pickerId }}-search"
                    x-ref="search"
                    autocomplete="off"
                    placeholder="{{ $searchPlaceholder }}"
                    class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-white py-3 pl-4 pr-4 text-sm shadow-sm transition focus:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                >
                <button type="button" @click="runSearchFromInput()"
                        class="shrink-0 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    Buscar
                </button>
                <span x-show="searching" x-cloak class="pointer-events-none absolute inset-y-0 right-[5.5rem] flex items-center pr-1">
                    <svg class="h-5 w-5 animate-spin text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </span>
            </div>
            <div x-show="showResults && searchResults.length" x-cloak
                 class="relative z-20 mt-2 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                <template x-for="(item, idx) in searchResults" :key="idx">
                    <button type="button" @click="applySearchResult(item)"
                            class="block w-full border-b border-slate-100 px-4 py-3 text-left text-sm text-slate-700 transition last:border-b-0 hover:bg-indigo-50">
                        <span x-text="item.label"></span>
                        <span x-show="item.approximate" class="mt-0.5 block text-[11px] font-medium text-amber-700">Ubicación aproximada</span>
                    </button>
                </template>
            </div>
            <p x-show="searchMessage" x-cloak x-text="searchMessage"
               class="mt-2 rounded-lg border px-3 py-2 text-xs leading-relaxed"
               :class="searchResults.length ? 'border-amber-200 bg-amber-50 text-amber-900' : 'border-rose-200 bg-rose-50 text-rose-800'"></p>
            <p class="mt-2 text-xs leading-relaxed text-slate-500">
                Escriba la dirección y pulse <strong>Buscar</strong>, o haga clic en el mapa y arrastre el marcador azul.
            </p>
        </div>
    @endunless

    <div
        x-ref="map"
        id="{{ $pickerId }}-map"
        class="address-map-canvas w-full bg-slate-200"
        role="img"
        aria-label="Mapa de ubicación"
    ></div>

    <div class="space-y-3 border-t border-slate-100 p-4 sm:p-5">
        <div>
            <label for="{{ $pickerId }}-address" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">
                Dirección registrada
            </label>
            <textarea
                name="{{ $readonly ? '' : $addressName }}"
                id="{{ $pickerId }}-address"
                rows="2"
                @readonly($readonly)
                x-model="address"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300"
                placeholder="Se completa automáticamente al elegir un punto en el mapa"
            >{{ $readonly ? '' : $addressVal }}</textarea>
            @error($addressName)
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        @unless($readonly)
            <input type="hidden" name="{{ $latName }}" x-ref="latInput" :value="lat">
            <input type="hidden" name="{{ $lngName }}" x-ref="lngInput" :value="lng">
        @endunless

        <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
            <span x-show="hasLocation()" x-cloak class="text-slate-500">
                <span class="font-medium text-slate-600">Coordenadas:</span>
                <span class="font-mono text-slate-700" x-text="formatCoords()"></span>
            </span>
            <a
                x-show="hasLocation()"
                x-cloak
                :href="mapsUrl()"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-3 py-1.5 font-semibold text-indigo-700 transition hover:bg-indigo-100"
            >
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Ver en Google Maps
            </a>
        </div>
    </div>
</div>

@once
    @prepend('scripts')
        <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    @endprepend
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
        <link rel="preload" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js" as="script" crossorigin="">
        <style>
            .address-location-picker .address-map-canvas {
                height: 20rem;
                min-height: 280px;
                z-index: 0;
            }
            @media (min-width: 640px) {
                .address-location-picker .address-map-canvas {
                    height: 22rem;
                }
            }
            .address-location-picker .leaflet-container {
                height: 100% !important;
                width: 100% !important;
                font-family: inherit;
            }
            .address-location-picker .leaflet-control-zoom {
                border: none !important;
                box-shadow: 0 4px 14px rgba(15, 23, 42, 0.12) !important;
                border-radius: 0.75rem !important;
                overflow: hidden;
            }
            .address-location-picker .leaflet-control-zoom a {
                color: #4338ca !important;
            }
            .pac-container {
                z-index: 10050 !important;
                font-family: inherit;
                border-radius: 0.75rem;
                margin-top: 4px;
                box-shadow: 0 10px 40px rgba(15, 23, 42, 0.15);
            }
            .pac-item {
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
            }
        </style>
    @endpush
    @push('scripts')
        <script>
            window.__gmapsReady = window.__gmapsReady || null;

            function waitForLeaflet(maxMs = 5000) {
                return new Promise((resolve, reject) => {
                    if (window.L?.map) {
                        resolve();
                        return;
                    }
                    const started = Date.now();
                    const tick = setInterval(() => {
                        if (window.L?.map) {
                            clearInterval(tick);
                            resolve();
                        } else if (Date.now() - started > maxMs) {
                            clearInterval(tick);
                            reject(new Error('Leaflet no disponible'));
                        }
                    }, 40);
                });
            }

            function loadGoogleMaps(apiKey) {
                if (window.google?.maps?.Map) {
                    return Promise.resolve();
                }
                if (window.__gmapsReady) {
                    return window.__gmapsReady;
                }
                window.__gmapsReady = new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(apiKey) + '&libraries=places&language=es&region=BO&callback=__gmapsCallback';
                    window.__gmapsCallback = () => resolve();
                    script.onerror = reject;
                    document.head.appendChild(script);
                });
                return window.__gmapsReady;
            }

            function loadGoogleMapsWithTimeout(apiKey, maxMs = 10000) {
                return Promise.race([
                    loadGoogleMaps(apiKey),
                    new Promise((_, reject) => setTimeout(() => reject(new Error('Google Maps timeout')), maxMs)),
                ]);
            }

            function addressLocationPickerFactory(config) {
                return {
                    pickerId: config.pickerId,
                    address: config.address || '',
                    lat: config.lat ?? -16.5,
                    lng: config.lng ?? -68.15,
                    readonly: config.readonly ?? false,
                    useGoogle: config.useGoogle ?? false,
                    geocodeSearchUrl: config.geocodeSearchUrl || '/geocode/search',
                    geocodeReverseUrl: config.geocodeReverseUrl || '/geocode/reverse',
                    map: null,
                    marker: null,
                    autocomplete: null,
                    searchTimer: null,
                    searching: false,
                    searchResults: [],
                    searchMessage: '',
                    showResults: false,
                    _wizardHandler: null,
                    _bootPromise: null,
                    mapProvider: null,

                    init() {
                        const wizardStep = this.$root.closest('[data-wizard-step]');
                        if (wizardStep) {
                            this._wizardHandler = (e) => this.onWizardStepVisible(e);
                            window.addEventListener('wizard-step-visible', this._wizardHandler);
                            if (wizardStep.offsetParent !== null) {
                                this.scheduleMapBoot();
                            }
                        } else {
                            this.scheduleMapBoot();
                        }
                    },

                    onWizardStepVisible(e) {
                        if (!e.detail?.form?.contains(this.$root)) {
                            return;
                        }
                        const stepEl = this.$root.closest('[data-wizard-step]');
                        if (!stepEl) {
                            return;
                        }
                        const stepIndex = parseInt(stepEl.getAttribute('data-wizard-step'), 10);
                        if (e.detail.step !== stepIndex) {
                            return;
                        }
                        this.scheduleMapBoot();
                    },

                    scheduleMapBoot() {
                        setTimeout(() => this.ensureMap(), 80);
                    },

                    getMapEl() {
                        return this.$refs.map || document.getElementById(this.pickerId + '-map');
                    },

                    ensureMap() {
                        if (this.map) {
                            this.refreshMapSize();
                            return this._bootPromise;
                        }
                        if (!this._bootPromise) {
                            this._bootPromise = this.bootMap();
                        }
                        return this._bootPromise;
                    },

                    async bootMap() {
                        const el = this.getMapEl();
                        if (!el) {
                            return;
                        }
                        try {
                            if (this.useGoogle) {
                                try {
                                    await loadGoogleMapsWithTimeout(this.$root.dataset.googleKey);
                                    this.initGoogleMap(this.readonly);
                                    if (!this.readonly) {
                                        this.initGoogleAutocomplete();
                                        this.bindAddressSearch();
                                    }
                                    this.mapProvider = 'google';
                                    return;
                                } catch (e) {
                                    console.warn('Google Maps no disponible, usando OpenStreetMap', e);
                                }
                            }
                            await waitForLeaflet();
                            this.initLeafletMap(this.readonly);
                            if (!this.readonly) {
                                this.bindAddressSearch();
                            }
                            this.mapProvider = 'leaflet';
                        } catch (e) {
                            console.error('No se pudo iniciar el mapa', e);
                            this._bootPromise = null;
                        } finally {
                            this.refreshMapSize();
                        }
                    },

                    refreshMapSize() {
                        const run = () => {
                            if (this.mapProvider === 'leaflet' && this.map?.invalidateSize) {
                                this.map.invalidateSize(true);
                            }
                            if (this.mapProvider === 'google' && this.map && window.google?.maps?.event) {
                                google.maps.event.trigger(this.map, 'resize');
                                this.map.setCenter({ lat: this.lat, lng: this.lng });
                            }
                        };
                        run();
                        [80, 250, 500].forEach((ms) => setTimeout(run, ms));
                    },

                    hasLocation() {
                        return this.hasPreciseCoords();
                    },

                    formatCoords() {
                        return this.lat.toFixed(6) + ', ' + this.lng.toFixed(6);
                    },

                    mapsUrl() {
                        return 'https://www.google.com/maps?q=' + this.lat + ',' + this.lng;
                    },

                    setPosition(lat, lng, addressText) {
                        this.lat = Number(lat);
                        this.lng = Number(lng);
                        if (addressText !== undefined && addressText !== null) {
                            this.address = addressText;
                        }
                        this.syncMarker();
                        this.syncInputs();
                    },

                    syncInputs() {
                        if (this.$refs.latInput) {
                            this.$refs.latInput.value = this.lat;
                        }
                        if (this.$refs.lngInput) {
                            this.$refs.lngInput.value = this.lng;
                        }
                    },

                    syncMarker() {
                        if (!this.map) return;
                        if (this.mapProvider === 'google' && this.marker?.setPosition) {
                            const pos = { lat: this.lat, lng: this.lng };
                            this.marker.setPosition(pos);
                            this.map.panTo(pos);
                        } else if (this.marker?.setLatLng) {
                            this.marker.setLatLng([this.lat, this.lng]);
                            this.map.setView([this.lat, this.lng], Math.max(this.map.getZoom(), 14));
                        }
                    },

                    initGoogleMap(readonly) {
                        const el = this.$refs.map;
                        this.map = new google.maps.Map(el, {
                            center: { lat: this.lat, lng: this.lng },
                            zoom: this.hasPreciseCoords() ? 16 : 12,
                            mapTypeControl: false,
                            streetViewControl: !readonly,
                            fullscreenControl: true,
                            gestureHandling: 'greedy',
                        });
                        this.marker = new google.maps.Marker({
                            map: this.map,
                            position: { lat: this.lat, lng: this.lng },
                            draggable: !readonly,
                        });
                        if (!readonly) {
                            this.marker.addListener('dragend', () => {
                                const p = this.marker.getPosition();
                                this.setPosition(p.lat(), p.lng());
                                this.reverseGeocodeGoogle(p.lat(), p.lng());
                            });
                            this.map.addListener('click', (e) => {
                                this.setPosition(e.latLng.lat(), e.latLng.lng());
                                this.reverseGeocodeGoogle(e.latLng.lat(), e.latLng.lng());
                            });
                        }
                    },

                    initGoogleAutocomplete() {
                        const input = this.$refs.search;
                        if (!input || !google.maps.places) return;
                        const boliviaBounds = new google.maps.LatLngBounds(
                            { lat: -22.9, lng: -69.6 },
                            { lat: -9.7, lng: -57.5 },
                        );
                        this.autocomplete = new google.maps.places.Autocomplete(input, {
                            componentRestrictions: { country: 'bo' },
                            bounds: boliviaBounds,
                            fields: ['formatted_address', 'geometry', 'name', 'address_components', 'types'],
                        });
                        this.autocomplete.addListener('place_changed', () => {
                            const place = this.autocomplete.getPlace();
                            if (!place.geometry?.location) return;
                            const lat = place.geometry.location.lat();
                            const lng = place.geometry.location.lng();
                            const typed = (input.value || '').trim();
                            const addr = typed || place.formatted_address || place.name || '';
                            this.setPosition(lat, lng, addr);
                            this.map.setZoom(17);
                            this.showResults = false;
                            this.searchMessage = '';
                        });
                    },

                    geocodeWithGoogle(query) {
                        return new Promise((resolve) => {
                            if (!window.google?.maps?.Geocoder) {
                                resolve([]);
                                return;
                            }
                            const geocoder = new google.maps.Geocoder();
                            geocoder.geocode(
                                {
                                    address: query,
                                    componentRestrictions: { country: 'BO' },
                                    region: 'BO',
                                },
                                (results, status) => {
                                    if (status !== 'OK' || !Array.isArray(results) || results.length === 0) {
                                        resolve([]);
                                        return;
                                    }
                                    resolve(results.slice(0, 5).map((row) => {
                                        const loc = row.geometry.location;
                                        const types = row.types || [];
                                        const approximate = !types.includes('street_address')
                                            && !types.includes('premise')
                                            && !types.includes('subpremise')
                                            && !types.includes('route');
                                        return {
                                            lat: loc.lat(),
                                            lng: loc.lng(),
                                            label: row.formatted_address || query,
                                            approximate,
                                            source: 'google',
                                        };
                                    }));
                                },
                            );
                        });
                    },

                    async handleSearchResults(data, query) {
                        this.searchResults = Array.isArray(data.results) ? data.results : [];
                        if (this.searchResults.length === 0) {
                            this.searchMessage = data.message || 'No encontramos esa dirección. Marque el punto en el mapa.';
                            return;
                        }
                        if (this.searchResults.length === 1) {
                            this.applySearchResult(this.searchResults[0]);
                            if (data.message) {
                                this.searchMessage = data.message;
                            }
                            return;
                        }
                        this.showResults = true;
                        this.searchMessage = data.message || 'Elija la opción que más se acerque a la vivienda.';
                    },

                    bindAddressSearch() {
                        const input = this.$refs.search;
                        if (!input) return;
                        input.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                this.runSearchFromInput();
                            }
                        });
                        input.addEventListener('input', () => {
                            clearTimeout(this.searchTimer);
                            this.showResults = false;
                            const q = input.value.trim();
                            if (q.length < 4) {
                                this.searchMessage = '';
                                return;
                            }
                            this.searchTimer = setTimeout(() => this.runSearch(q), 700);
                        });
                    },

                    runSearchFromInput() {
                        const q = (this.$refs.search?.value || '').trim();
                        if (q.length < 3) {
                            this.searchMessage = 'Escriba al menos 3 caracteres para buscar.';
                            return;
                        }
                        this.runSearch(q);
                    },

                    async runSearch(query) {
                        this.searching = true;
                        this.searchMessage = '';
                        this.showResults = false;
                        try {
                            if (this.mapProvider === 'google') {
                                const googleHits = await this.geocodeWithGoogle(query);
                                if (googleHits.length > 0) {
                                    const precise = googleHits.filter((h) => !h.approximate);
                                    await this.handleSearchResults({
                                        results: precise.length > 0 ? precise : googleHits,
                                        message: precise.length === 0
                                            ? 'Ubicación aproximada. Arrastre el marcador hasta la casa exacta.'
                                            : null,
                                    }, query);
                                    return;
                                }
                            }

                            const url = this.geocodeSearchUrl + '?q=' + encodeURIComponent(query);
                            const res = await fetch(url, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                credentials: 'same-origin',
                            });
                            if (!res.ok) {
                                throw new Error('HTTP ' + res.status);
                            }
                            const data = await res.json();
                            await this.handleSearchResults(data, query);
                        } catch (e) {
                            console.warn('Búsqueda de dirección no disponible', e);
                            this.searchMessage = 'No se pudo buscar la dirección. Marque el punto manualmente en el mapa.';
                        } finally {
                            this.searching = false;
                        }
                    },

                    applySearchResult(item) {
                        if (!item) return;
                        const typed = (this.$refs.search?.value || '').trim();
                        this.setPosition(item.lat, item.lng, typed || item.label);
                        this.showResults = false;
                        if (item.approximate) {
                            this.searchMessage = 'Ubicación aproximada. Arrastre el marcador azul hasta la casa exacta.';
                        } else {
                            this.searchMessage = '';
                        }
                        if (this.mapProvider === 'google' && this.map?.setZoom) {
                            this.map.setZoom(17);
                        } else if (this.map?.setView) {
                            this.map.setView([item.lat, item.lng], 17);
                        }
                        this.refreshMapSize();
                    },

                    async reverseGeocodeServer(lat, lng) {
                        try {
                            const url = this.geocodeReverseUrl + '?lat=' + encodeURIComponent(lat) + '&lng=' + encodeURIComponent(lng);
                            const res = await fetch(url, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                credentials: 'same-origin',
                            });
                            if (!res.ok) return;
                            const data = await res.json();
                            if (data.label) {
                                this.address = data.label;
                            }
                        } catch (e) {
                            /* ignore */
                        }
                    },

                    reverseGeocodeGoogle(lat, lng) {
                        if (!google.maps.Geocoder) return;
                        const geocoder = new google.maps.Geocoder();
                        geocoder.geocode({ location: { lat, lng } }, (results, status) => {
                            if (status === 'OK' && results[0]) {
                                this.address = results[0].formatted_address;
                            }
                        });
                    },

                    initLeafletMap(readonly) {
                        const el = this.getMapEl();
                        if (!el) {
                            return;
                        }
                        if (el._leaflet_id) {
                            this.refreshMapSize();
                            return;
                        }
                        const zoom = this.hasPreciseCoords() ? 15 : 6;
                        this.map = L.map(el, { zoomControl: true }).setView([this.lat, this.lng], zoom);
                        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                        }).addTo(this.map);
                        this.marker = L.marker([this.lat, this.lng], { draggable: !readonly }).addTo(this.map);
                        if (!readonly) {
                            this.marker.on('dragend', () => {
                                const p = this.marker.getLatLng();
                                this.setPosition(p.lat, p.lng);
                                this.reverseGeocodeServer(p.lat, p.lng);
                            });
                            this.map.on('click', (e) => {
                                this.setPosition(e.latlng.lat, e.latlng.lng);
                                this.reverseGeocodeServer(e.latlng.lat, e.latlng.lng);
                            });
                        }
                        this.refreshMapSize();
                    },

                    hasPreciseCoords() {
                        return Math.abs(this.lat + 16.5) > 0.01 || Math.abs(this.lng + 68.15) > 0.01;
                    },
                };
            }

            function registerAddressLocationPicker() {
                Alpine.data('addressLocationPicker', addressLocationPickerFactory);
            }

            document.addEventListener('alpine:init', registerAddressLocationPicker);
            if (window.Alpine) {
                registerAddressLocationPicker();
            }
        </script>
    @endpush
@endonce
