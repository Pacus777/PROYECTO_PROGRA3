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
        'sharp' => 'overflow-hidden rounded-lg border border-slate-100/50 bg-gradient-to-b from-white to-[#FAFBFD] shadow-[0_12px_35px_rgba(148,163,184,0.07)]',
        'inset' => 'overflow-hidden rounded-2xl border border-blue-100 bg-[#F8FAFC] shadow-inner ring-4 ring-blue-50/20',
        'dark-cap' => 'overflow-hidden rounded-2xl bg-gradient-to-b from-white to-[#F8FBF9] border border-emerald-100 shadow-[0_12px_35px_rgba(16,185,129,0.06)]',
        'ledger' => 'overflow-hidden rounded-xl border border-amber-100/50 bg-amber-50/20 shadow-sm',
        'action' => 'overflow-hidden rounded-2xl border border-orange-100 bg-gradient-to-b from-orange-50/50 to-white shadow-md',
        'queue' => 'overflow-hidden rounded-2xl border border-slate-100/50 border-l-4 border-l-amber-500 bg-gradient-to-b from-white to-[#FAFBF9] shadow-[0_12px_35px_rgba(245,158,11,0.05)]',
        'folder' => 'overflow-hidden rounded-2xl bg-white shadow-[0_12px_35px_rgba(244,63,94,0.05)] ring-1 ring-rose-50',
        'timeline' => 'relative pl-6 before:absolute before:left-[11px] before:top-2 before:h-[calc(100%-1rem)] before:w-0.5 before:bg-zinc-300',
        'hub' => 'rounded-2xl border border-fuchsia-100 bg-gradient-to-b from-white to-[#FAF8FD] p-1 shadow-[0_12px_35px_rgba(217,70,239,0.06)] backdrop-blur-sm',
        'elevated' => 'overflow-hidden rounded-2xl bg-gradient-to-b from-white to-[#FAF8FD] border border-violet-100 shadow-[0_15px_40px_rgba(139,92,246,0.07)]',
        default => 'overflow-hidden rounded-2xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-100/55 shadow-[0_15px_40px_rgba(148,163,184,0.08),0_1px_2px_rgba(0,0,0,0.005)]',
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
