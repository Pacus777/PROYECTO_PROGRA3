<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Proveedor de mapas
    |--------------------------------------------------------------------------
    | osm    = OpenStreetMap / Leaflet (gratis, open source, sin tarjeta)
    | google = Google Maps (requiere API key y cuenta de facturación)
    */
    'provider' => env('MAPS_PROVIDER', 'osm'),

    /*
    |--------------------------------------------------------------------------
    | Google Maps Platform
    |--------------------------------------------------------------------------
    | Cree una clave en Google Cloud y active:
    | Maps JavaScript API, Places API, Geocoding API
    */
    'google_key' => env('GOOGLE_MAPS_API_KEY'),

    'default_lat' => -16.5,
    'default_lng' => -68.15,

];
