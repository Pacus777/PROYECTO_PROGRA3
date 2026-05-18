@props([
    'module' => 'default',
    'title',
    'subtitle' => null,
    'eyebrow' => null,
])

@php
    $theme = \App\Support\InstitucionalModuleTheme::for($module);
@endphp

@push('styles')
    @include('components.institucional.partials.styles')
@endpush

<div
    data-module="{{ $theme['key'] }}"
    @class(['inst-module -mx-3 min-h-full px-3 py-4 sm:-mx-4 sm:px-4 lg:-mx-6 lg:px-6', $theme['page_bg'], $theme['pattern']])
>
    @include('components.institucional.partials.header', [
        'theme' => $theme,
        'title' => $title,
        'subtitle' => $subtitle,
        'eyebrow' => $eyebrow ?? 'Unidad educativa',
        'actions' => $actions ?? null,
    ])

    <x-institucional.flash />

    @if(isset($kpis))
        {{ $kpis }}
    @endif

    {{ $slot }}
</div>
