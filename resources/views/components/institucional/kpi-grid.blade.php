@props([
    'module' => 'default',
    'items' => [],
    'cols' => 'sm:grid-cols-2 lg:grid-cols-4',
])

@php
    $theme = \App\Support\InstitucionalModuleTheme::for($module);
    $kpiStyle = $theme['kpi'];
@endphp

@if(count($items) > 0)
    <div @class([
        'mb-6',
        'flex gap-3 overflow-x-auto pb-1 snap-x' => $kpiStyle === 'pill',
        'grid gap-3' => $kpiStyle !== 'pill',
        $kpiStyle !== 'pill' ? $cols : '',
    ])>
        @foreach($items as $i => $item)
            @php
                $label = $item['label'] ?? '';
                $value = $item['value'] ?? 0;
                $hint = $item['hint'] ?? null;
                $tone = $item['tone'] ?? 'default';
            @endphp

            @if($kpiStyle === 'gradient')
                <article @class([
                    'rounded-2xl bg-gradient-to-br p-4 text-white shadow-lg',
                    'min-w-[140px] snap-start shrink-0' => false,
                    match($i % 4) {
                        0 => 'from-indigo-500 to-indigo-700',
                        1 => 'from-violet-500 to-purple-700',
                        2 => 'from-cyan-500 to-blue-600',
                        default => 'from-emerald-500 to-teal-600',
                    },
                ])>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-white/80">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-black">{{ $value }}</p>
                    @if($hint)<p class="mt-0.5 text-[10px] text-white/75">{{ $hint }}</p>@endif
                </article>
            @elseif($kpiStyle === 'pill')
                <article class="min-w-[150px] shrink-0 snap-start rounded-full border border-white/60 bg-white/90 px-5 py-3 shadow-sm backdrop-blur-sm {{ $theme['accent_ring'] }} ring-1">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $label }}</p>
                    <p class="text-xl font-black {{ $theme['accent_text'] }}">{{ $value }}</p>
                </article>
            @elseif($kpiStyle === 'outline')
                <article class="rounded-xl border-2 border-dashed {{ $theme['accent_ring'] }} bg-white/80 px-4 py-3 backdrop-blur-sm">
                    <p class="text-[10px] font-bold uppercase text-slate-500">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-black {{ $theme['accent_text'] }}">{{ $value }}</p>
                </article>
            @elseif($kpiStyle === 'medal')
                <article class="relative overflow-hidden rounded-2xl bg-white p-4 shadow-md ring-1 ring-emerald-100">
                    <div class="absolute -right-3 -top-3 h-16 w-16 rounded-full {{ $theme['accent_bg_soft'] }} opacity-60"></div>
                    <p class="text-[10px] font-bold uppercase text-slate-500">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-black text-emerald-700">{{ $value }}</p>
                </article>
            @elseif($kpiStyle === 'queue')
                <article class="flex items-center gap-3 rounded-xl bg-white px-4 py-3 shadow-sm ring-1 ring-slate-200">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-lg font-black text-amber-700">{{ $loop->iteration }}</span>
                    <div>
                        <p class="text-[10px] font-bold uppercase text-slate-500">{{ $label }}</p>
                        <p class="text-xl font-black text-slate-900">{{ $value }}</p>
                    </div>
                </article>
            @elseif($kpiStyle === 'dot')
                <article class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-rose-100">
                    <span class="mb-2 inline-block h-2 w-2 rounded-full {{ $theme['accent_bg'] }}"></span>
                    <p class="text-[10px] font-bold uppercase text-slate-500">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-black text-rose-700">{{ $value }}</p>
                </article>
            @elseif($kpiStyle === 'tile')
                <article class="rounded-xl bg-gradient-to-br from-white to-fuchsia-50/80 p-4 shadow-sm ring-1 ring-fuchsia-100">
                    <p class="text-[10px] font-bold uppercase text-fuchsia-600/80">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-black text-slate-900">{{ $value }}</p>
                    @if($hint)<p class="mt-0.5 text-[10px] text-slate-500">{{ $hint }}</p>@endif
                </article>
            @elseif($kpiStyle === 'score')
                <article class="rounded-2xl border-l-4 border-l-amber-500 bg-amber-50/50 p-4 shadow-sm ring-1 ring-amber-100">
                    <p class="text-[10px] font-bold uppercase text-amber-800/80">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-black text-amber-900">{{ $value }}</p>
                    @if($hint)<p class="mt-0.5 text-[10px] text-amber-700/80">{{ $hint }}</p>@endif
                </article>
            @elseif($kpiStyle === 'ring')
                <article class="rounded-2xl bg-white p-4 text-center shadow-md ring-2 {{ $theme['accent_ring'] }}">
                    <p class="text-[10px] font-bold uppercase text-slate-500">{{ $label }}</p>
                    <p class="mt-1 text-3xl font-black {{ $theme['accent_text'] }}">{{ $value }}</p>
                </article>
            @else
                <article class="rounded-xl bg-white/90 p-4 shadow-sm ring-1 ring-white/80 backdrop-blur-sm">
                    <p class="text-[10px] font-bold uppercase text-slate-500">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-black text-slate-900">{{ $value }}</p>
                    @if($hint)<p class="mt-0.5 text-[10px] text-slate-500">{{ $hint }}</p>@endif
                </article>
            @endif
        @endforeach
    </div>
@endif
