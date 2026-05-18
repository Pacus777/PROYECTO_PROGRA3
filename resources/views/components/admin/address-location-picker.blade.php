@props([
    'addressName' => 'direccion_ued',
    'latName' => 'lat_ued',
    'lngName' => 'lng_ued',
    'address' => '',
    'lat' => null,
    'lng' => null,
    'readonly' => false,
    'pickerId' => null,
])

@php
    $pickerId = $pickerId ?? 'addr-' . substr(md5($addressName . ($latName ?? '')), 0, 8);
    $addressVal = old($addressName, $address);
    $latVal = old($latName, $lat);
    $lngVal = old($lngName, $lng);
    $googleKey = config('services.google.maps_key');
    $hasCoords = $latVal !== null && $latVal !== '' && $lngVal !== null && $lngVal !== '';
    $config = [
        'pickerId' => $pickerId,
        'address' => $addressVal ?? '',
        'lat' => $hasCoords ? (float) $latVal : -16.5,
        'lng' => $hasCoords ? (float) $lngVal : -68.15,
        'readonly' => (bool) $readonly,
        'useGoogle' => filled($googleKey) && strlen(trim((string) $googleKey)) > 10,
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
        <div class="border-b border-slate-100 bg-gradient-to-br from-slate-50 to-indigo-50/40 p-4 sm:p-5">
            <label for="{{ $pickerId }}-search" class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-800">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </span>
                Ubicación de la unidad
            </label>
            <div class="relative">
                <input
                    type="text"
                    id="{{ $pickerId }}-search"
                    x-ref="search"
                    autocomplete="off"
                    placeholder="Buscar calle, zona, barrio o ciudad en Bolivia…"
                    class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-4 pr-10 text-sm shadow-sm transition focus:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                >
                <span x-show="searching" x-cloak class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                    <svg class="h-5 w-5 animate-spin text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-xs leading-relaxed text-slate-500">
                Escribe para buscar, haz clic en el mapa o arrastra el marcador azul para fijar el punto.
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

            function loadGoogleMapsWithTimeout(apiKey, maxMs = 3000) {
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
                    map: null,
                    marker: null,
                    autocomplete: null,
                    searchTimer: null,
                    searching: false,
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
                            await waitForLeaflet();
                            if (this.useGoogle) {
                                try {
                                    await loadGoogleMapsWithTimeout(this.$root.dataset.googleKey);
                                    this.initGoogleMap(this.readonly);
                                    if (!this.readonly) {
                                        this.initGoogleAutocomplete();
                                    }
                                    this.mapProvider = 'google';
                                    return;
                                } catch (e) {
                                    /* OpenStreetMap */
                                }
                            }
                            this.initLeafletMap(this.readonly);
                            if (!this.readonly) {
                                this.bindNominatimSearch();
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
                        this.autocomplete = new google.maps.places.Autocomplete(input, {
                            componentRestrictions: { country: 'bo' },
                            fields: ['formatted_address', 'geometry', 'name'],
                        });
                        this.autocomplete.addListener('place_changed', () => {
                            const place = this.autocomplete.getPlace();
                            if (!place.geometry?.location) return;
                            const lat = place.geometry.location.lat();
                            const lng = place.geometry.location.lng();
                            const addr = place.formatted_address || place.name || input.value;
                            this.setPosition(lat, lng, addr);
                            this.map.setZoom(16);
                        });
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
                                this.reverseGeocodeNominatim(p.lat, p.lng);
                            });
                            this.map.on('click', (e) => {
                                this.setPosition(e.latlng.lat, e.latlng.lng);
                                this.reverseGeocodeNominatim(e.latlng.lat, e.latlng.lng);
                            });
                        }
                        this.refreshMapSize();
                    },

                    bindNominatimSearch() {
                        const input = this.$refs.search;
                        if (!input) return;
                        input.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                const q = input.value.trim();
                                if (q.length >= 3) {
                                    this.searchNominatim(q);
                                }
                            }
                        });
                        input.addEventListener('input', () => {
                            clearTimeout(this.searchTimer);
                            const q = input.value.trim();
                            if (q.length < 3) return;
                            this.searchTimer = setTimeout(() => this.searchNominatim(q), 500);
                        });
                    },

                    async searchNominatim(query) {
                        this.searching = true;
                        try {
                            const url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=bo&q=' + encodeURIComponent(query);
                            const res = await fetch(url, { headers: { 'Accept-Language': 'es' } });
                            const data = await res.json();
                            if (data[0]) {
                                const lat = parseFloat(data[0].lat);
                                const lng = parseFloat(data[0].lon);
                                this.setPosition(lat, lng, data[0].display_name);
                                if (this.map?.setView) {
                                    this.map.setView([lat, lng], 16);
                                }
                                this.refreshMapSize();
                            }
                        } catch (e) {
                            console.warn('Búsqueda de dirección no disponible', e);
                        } finally {
                            this.searching = false;
                        }
                    },

                    async reverseGeocodeNominatim(lat, lng) {
                        try {
                            const url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng;
                            const res = await fetch(url, { headers: { 'Accept-Language': 'es' } });
                            const data = await res.json();
                            if (data.display_name) {
                                this.address = data.display_name;
                            }
                        } catch (e) {
                            /* ignore */
                        }
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
