@props(['unidad', 'compact' => false])

@php
    $abiertas = (int) ($unidad->ofertas_abiertas_count ?? 0);
    $ubicacion = $unidad->municipio
        ? collect([
            $unidad->municipio->nombre_mun ?? null,
            $unidad->municipio->provincia->departamento->nombre_dep ?? null,
        ])->filter()->implode(', ')
        : null;
@endphp

<article @class([
    'group relative flex flex-col overflow-hidden rounded-3xl border bg-white transition-all duration-300',
    'border-emerald-200 shadow-md shadow-emerald-100/40 hover:shadow-xl hover:-translate-y-1' => $abiertas > 0,
    'border-slate-200 shadow-sm hover:shadow-lg hover:-translate-y-0.5' => $abiertas === 0,
    'p-5' => $compact,
    'p-6' => ! $compact,
])>
    @if($abiertas > 0)
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-400 to-teal-500"></div>
    @endif

    <div class="flex items-start gap-4">
        <div @class([
            'flex shrink-0 items-center justify-center rounded-2xl font-bold text-white shadow-inner',
            'h-12 w-12 text-lg' => $compact,
            'h-14 w-14 text-xl' => ! $compact,
            'bg-gradient-to-br from-emerald-500 to-teal-600' => $abiertas > 0,
            'bg-gradient-to-br from-slate-400 to-slate-500' => $abiertas === 0,
        ])>
            {{ mb_strtoupper(mb_substr($unidad->nombre_ued, 0, 1)) }}
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-start justify-between gap-2">
                <h3 @class(['font-bold text-slate-900 leading-snug group-hover:text-blue-700 transition-colors', 'text-base' => $compact, 'text-xl' => ! $compact])>
                    {{ $unidad->nombre_ued }}
                </h3>
                @if($abiertas > 0)
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-emerald-700 border border-emerald-100">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        {{ $abiertas === 1 ? '1 cupo abierto' : $abiertas.' cupos abiertos' }}
                    </span>
                @else
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-slate-500">Sin convocatoria</span>
                @endif
            </div>

            @if($unidad->codigo_ued)
                <p class="mt-1.5 inline-flex items-center gap-1 text-xs font-mono text-slate-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                    {{ $unidad->codigo_ued }}
                </p>
            @endif
        </div>
    </div>

    <dl class="mt-4 space-y-2 text-sm flex-1">
        @if($ubicacion)
            <div class="flex items-start gap-2 text-slate-600">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <dd>{{ $ubicacion }}</dd>
            </div>
        @endif
        @if($unidad->direccion_ued)
            <div class="flex items-start gap-2 text-slate-500">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <dd class="line-clamp-2">{{ $unidad->direccion_ued }}</dd>
            </div>
        @endif
    </dl>

    <div class="mt-5 pt-4 border-t border-slate-100">
        <a href="{{ route('colegios.show', $unidad) }}"
           @class([
               'inline-flex w-full items-center justify-center gap-2 rounded-xl px-5 py-3 text-sm font-semibold transition',
               'bg-gradient-to-r from-blue-600 to-cyan-500 text-white shadow-md hover:opacity-95' => $abiertas > 0,
               'border border-slate-200 bg-slate-50 text-slate-700 hover:bg-white hover:border-blue-200 hover:text-blue-700' => $abiertas === 0,
           ])>
            @if($abiertas > 0)
                Ver colegio y postular
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            @else
                Ver información del colegio
            @endif
        </a>
    </div>
</article>
