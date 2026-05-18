@props([
    'items' => [],
    'exclude' => ['page', 'format'],
    'compact' => false,
])

@php
    $params = collect(request()->query())
        ->except($exclude)
        ->filter(fn ($v) => $v !== null && $v !== '')
        ->all();
    $btnExcel = 'inline-flex items-center gap-1 rounded-lg border border-emerald-300 bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700';
    $btnPdf = 'inline-flex items-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-800 hover:bg-rose-100';
    $single = count($items) === 1 && empty($items[0]['label'] ?? null);
    $useCompact = $compact || $single;
@endphp

<div {{ $attributes->merge(['class' => $useCompact ? 'inline-flex flex-wrap items-center gap-2' : 'rounded-xl border border-slate-200 bg-slate-50/80 p-4 space-y-3']) }}>
    @unless($useCompact)
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Exportar con los filtros actuales</p>
    @endunless

    @foreach($items as $item)
        @php
            $route = $item['route'] ?? null;
            $label = $item['label'] ?? null;
            $hint = $item['hint'] ?? null;
        @endphp
        @if($route)
            <div class="{{ $useCompact ? 'inline-flex flex-wrap items-center gap-2' : 'flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-t border-slate-100 first:border-0 first:pt-0 pt-3' }}">
                @if($label && ! $useCompact)
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800">{{ $label }}</p>
                        @if($hint)
                            <p class="text-xs text-slate-500">{{ $hint }}</p>
                        @endif
                    </div>
                @elseif($label && $useCompact)
                    <span class="text-xs font-medium text-slate-600">{{ $label }}:</span>
                @endif
                <div class="flex flex-wrap items-center gap-2 {{ $useCompact ? '' : 'sm:flex-shrink-0' }}">
                    <a href="{{ route($route, array_merge($params, ['format' => 'xlsx'])) }}" class="{{ $btnExcel }}" title="Hoja de cálculo Excel (.xlsx)">
                        Excel
                    </a>
                    <a href="{{ route($route, array_merge($params, ['format' => 'pdf'])) }}" class="{{ $btnPdf }}" title="Documento PDF">
                        PDF
                    </a>
                </div>
            </div>
        @endif
    @endforeach
</div>
