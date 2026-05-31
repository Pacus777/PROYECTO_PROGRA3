{{-- Domicilio / vivienda del postulante --}}
<div class="mt-8 border-t border-slate-100 pt-8">
    <h3 class="mb-1 text-sm font-bold uppercase tracking-wide text-slate-500">Domicilio del postulante</h3>
    <p class="mb-4 text-xs text-slate-500">
        Ubicación de la vivienda familiar. Se usa para evaluar la cercanía al colegio donde postule.
    </p>

    <div class="mb-4 rounded-xl border border-blue-100 bg-blue-50/70 p-4 text-xs text-blue-950">
        <p class="font-semibold">Cómo escribir la dirección para ubicarla en el mapa</p>
        <p class="mt-2 leading-relaxed">
            Escriba la dirección lo más completa posible en la búsqueda o en el campo «Dirección registrada».
            Use este orden:
        </p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            <li><strong>Zona o barrio:</strong> ej. Bajo Llojeta</li>
            <li><strong>Calle o avenida:</strong> ej. Acacias</li>
            <li><strong>N° de puerta:</strong> ej. 2019</li>
            <li><strong>Ciudad:</strong> ej. La Paz</li>
        </ul>
        <p class="mt-3 rounded-lg bg-white/80 px-3 py-2 font-mono text-[11px] text-slate-700 sm:text-xs">
            Zona Bajo Llojeta, Calle Acacias N° 2019, La Paz
        </p>
        <p class="mt-2 text-blue-900/80">
            Escriba la dirección y pulse <strong>Buscar</strong>. Si no aparece la casa exacta, elija la opción más cercana y arrastre el marcador azul hasta el punto correcto.
        </p>
    </div>

    <x-admin.address-location-picker
        :address="old('direccion_est', $estudiante->direccion_est ?? '')"
        :lat="old('lat_est', $estudiante->lat_est ?? null)"
        :lng="old('lng_est', $estudiante->lng_est ?? null)"
        address-name="direccion_est"
        lat-name="lat_est"
        lng-name="lng_est"
        picker-id="est-domicilio"
        search-label="Ubicación del domicilio"
        search-placeholder="Ej: Zona Bajo Llojeta, Calle Acacias N° 2019, La Paz"
    />

    @error('direccion_est')<p class="mt-2 text-xs text-rose-600">{{ $message }}</p>@enderror
    @error('lat_est')<p class="mt-2 text-xs text-rose-600">{{ $message }}</p>@enderror
    @error('lng_est')<p class="mt-2 text-xs text-rose-600">{{ $message }}</p>@enderror
</div>
