@php
    $estado = $estado ?? 'abierta';
    $label = trim(implode(' · ', array_filter([
        $oferta->gestion->nombre_ges ?? null,
        $oferta->nivel->nombre_niv ?? null,
        $oferta->curso->nombre_cur ?? null,
        $oferta->paralelo->nombre_par ?? null,
    ])));
@endphp

<article @class([
    'rounded-2xl border p-5 transition hover:shadow-md',
    'border-emerald-200 bg-gradient-to-br from-emerald-50/80 to-white' => $estado === 'abierta',
    'border-amber-200 bg-gradient-to-br from-amber-50/60 to-white' => $estado === 'proxima',
    'border-slate-200 bg-slate-50/80' => $estado === 'cerrada',
])>
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                @if($estado === 'abierta')
                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold uppercase text-emerald-800">Abierta</span>
                @elseif($estado === 'proxima')
                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase text-amber-800">Próxima</span>
                @endif
                <h3 class="font-bold text-slate-900">{{ $label ?: 'Oferta #'.$oferta->id_oac }}</h3>
            </div>
            @if($oferta->descripcion_oac)
                <p class="mt-2 text-sm text-slate-600">{{ $oferta->descripcion_oac }}</p>
            @endif
            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                <span class="inline-flex items-center gap-1">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ $oferta->fecha_inicio_postulacion_oac?->format('d/m/Y') ?? '—' }} — {{ $oferta->fecha_fin_postulacion_oac?->format('d/m/Y') ?? '—' }}
                </span>
            </div>
            @if($oferta->tiposDocumentoRequeridos->isNotEmpty())
                <p class="mt-2 text-xs text-slate-500">
                    <span class="font-semibold text-slate-600">Documentos:</span>
                    {{ $oferta->tiposDocumentoRequeridos->pluck('nombre_tdo')->implode(', ') }}
                </p>
            @endif
        </div>

        @if($estado === 'abierta')
            @if($esTutor)
                <a href="{{ route('tutor.postulaciones.create', ['colegio' => $unidad->codigo_ued, 'oac' => $oferta->id_oac]) }}"
                   class="shrink-0 inline-flex items-center justify-center gap-1 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition">
                    Postular aquí
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            @else
                <button type="button" onclick="window.abrirRegistroTutor()"
                        class="shrink-0 inline-flex items-center justify-center rounded-xl border border-blue-200 bg-white px-4 py-2.5 text-sm font-semibold text-blue-700 hover:bg-blue-50 transition">
                    Registrarme para postular
                </button>
            @endif
        @elseif($estado === 'proxima')
            <span class="shrink-0 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Próximamente</span>
        @else
            <span class="shrink-0 rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-600">Cerrada</span>
        @endif
    </div>
</article>
