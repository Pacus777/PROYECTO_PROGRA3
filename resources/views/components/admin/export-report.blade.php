@props([
    'route',
    'exclude' => ['page', 'format'],
])

@php
    $params = collect(request()->query())
        ->except($exclude)
        ->filter(fn ($v) => $v !== null && $v !== '')
        ->all();
    $btnBase = 'inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-semibold transition sm:text-sm';
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex flex-wrap items-center gap-2']) }}>
    <span class="sr-only">Descargar reporte con los filtros actuales</span>
    <a href="{{ route($route, array_merge($params, ['format' => 'xlsx'])) }}"
       title="Archivo Excel (.xlsx) para abrir en Microsoft Excel o Google Sheets"
       class="{{ $btnBase }} border border-emerald-300 bg-emerald-600 text-white hover:bg-emerald-700">
        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a1 1 0 001 1h14a1 1 0 001-1v-2M7 10l5 5m0 0l5-5m-5 5V4"/>
        </svg>
        Descargar Excel
    </a>
    <a href="{{ route($route, array_merge($params, ['format' => 'pdf'])) }}"
       title="Documento PDF listo para imprimir o compartir"
       class="{{ $btnBase }} border border-rose-200 bg-rose-50 text-rose-800 hover:bg-rose-100">
        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1zm8 0v5h5M9 12h6m-6 4h4"/>
        </svg>
        Descargar PDF
    </a>
</div>
