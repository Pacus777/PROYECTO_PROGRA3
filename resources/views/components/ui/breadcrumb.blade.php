@props([
    'items' => [],
])

@php
    $items = is_array($items) ? $items : [];
@endphp

<nav aria-label="Ruta de navegación" class="flex min-w-0 flex-wrap items-center gap-2 text-sm text-slate-400">
    @foreach($items as $index => $item)
        @if($index > 0)
            <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        @endif

        @if(!empty($item['url']) && $index < count($items) - 1)
            <a href="{{ $item['url'] }}" class="truncate font-medium text-slate-500 transition hover:text-indigo-600">
                {{ $item['label'] }}
            </a>
        @else
            <span class="truncate {{ $loop->last ? 'font-medium text-slate-600' : 'text-slate-500' }}">
                {{ $item['label'] }}
            </span>
        @endif
    @endforeach
</nav>
