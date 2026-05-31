<?php

declare(strict_types=1);

return [

    /** Distancia máxima de referencia (km) para puntaje 0 en el criterio geográfico. */
    'distancia_max_km' => (float) env('PROXIMIDAD_DISTANCIA_MAX_KM', 12),

    /** Tamaño de la cuadrícula para A* (mayor = más preciso, más lento). */
    'grid_size' => (int) env('PROXIMIDAD_GRID_SIZE', 48),

];
