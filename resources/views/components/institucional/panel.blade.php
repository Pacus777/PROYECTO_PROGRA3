@props([
    'module' => 'default',
    'title' => null,
    'class' => '',
])

@php
    $theme = \App\Support\InstitucionalModuleTheme::for($module);
    $panel = $theme['panel'];
@endphp

<section @class([
    'mb-6',
    match($panel) {
        'sharp' => 'overflow-hidden rounded-lg border-2 border-slate-200 bg-white shadow-sm',
        'inset' => 'overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-inner ring-4 ring-blue-50/50',
        'dark-cap' => 'overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-emerald-100',
        'ledger' => 'overflow-hidden rounded-xl border border-amber-200/60 bg-amber-50/20 shadow-sm',
        'action' => 'overflow-hidden rounded-2xl border border-orange-200 bg-gradient-to-b from-orange-50/50 to-white shadow-md',
        'queue' => 'overflow-hidden rounded-2xl border-l-4 border-l-amber-500 bg-white shadow-sm',
        'folder' => 'overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-rose-100',
        'timeline' => 'relative pl-6 before:absolute before:left-[11px] before:top-2 before:h-[calc(100%-1rem)] before:w-0.5 before:bg-zinc-300',
        'hub' => 'rounded-2xl border border-fuchsia-100 bg-white/80 p-1 shadow-sm backdrop-blur-sm',
        'elevated' => 'overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-violet-100',
        default => 'overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100/80',
    },
    $class,
])>
    @if($title)
        <header @class([
            'px-5 py-4',
            match($panel) {
                'dark-cap' => 'bg-gradient-to-r from-emerald-800 to-teal-800 text-white',
                'ledger' => 'border-b border-amber-200/50 bg-amber-100/40',
                'queue' => 'border-b border-slate-100 bg-slate-50',
                default => 'border-b border-slate-100 bg-slate-50/80',
            },
        ])>
            <h2 @class([
                'text-sm font-semibold',
                $panel === 'dark-cap' ? 'text-white' : 'text-slate-800',
            ])>{{ $title }}</h2>
        </header>
    @endif
    <div @class([
        $panel === 'hub' ? 'p-4' : ($title ? '' : ''),
        $panel === 'timeline' ? 'space-y-0' : '',
    ])>
        {{ $slot }}
    </div>
</section>
