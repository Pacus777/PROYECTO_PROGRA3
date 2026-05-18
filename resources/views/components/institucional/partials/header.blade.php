@php
    /** @var array<string, string> $theme */
    $headerType = $theme['header'];
@endphp

@if($headerType === 'hero')
    <header class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-r {{ $theme['header_from'] }} {{ $theme['header_via'] }} {{ $theme['header_to'] }} px-8 py-10 shadow-xl">
        <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute bottom-0 right-1/4 h-32 w-32 rounded-full bg-white/5"></div>
        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                @if($eyebrow)<p class="text-xs font-semibold uppercase tracking-[0.2em] {{ $theme['header_subtext'] }}">{{ $eyebrow }}</p>@endif
                <h1 class="mt-2 text-3xl font-bold {{ $theme['header_text'] }} sm:text-4xl">{{ $title }}</h1>
                @if($subtitle)<p class="mt-3 text-sm leading-relaxed {{ $theme['header_subtext'] }}">{{ $subtitle }}</p>@endif
            </div>
            @isset($actions)<div class="flex flex-wrap gap-2">{{ $actions }}</div>@endisset
        </div>
    </header>

@elseif($headerType === 'split')
    <header class="mb-8 overflow-hidden rounded-2xl bg-white shadow-md ring-1 {{ $theme['accent_ring'] }}">
        <div class="grid lg:grid-cols-5">
            <div class="bg-gradient-to-br {{ $theme['header_from'] }} {{ $theme['header_to'] }} px-6 py-8 lg:col-span-2">
                @if($eyebrow)<p class="text-xs font-semibold uppercase tracking-wider text-violet-200">{{ $eyebrow }}</p>@endif
                <h1 class="mt-2 text-2xl font-bold text-white lg:text-3xl">{{ $title }}</h1>
            </div>
            <div class="flex flex-col justify-center px-6 py-6 lg:col-span-3">
                @if($subtitle)<p class="text-sm leading-relaxed text-slate-600">{{ $subtitle }}</p>@endif
                @isset($actions)<div class="mt-4 flex flex-wrap gap-2">{{ $actions }}</div>@endisset
            </div>
        </div>
    </header>

@elseif($headerType === 'stripe')
    <header class="mb-8 flex flex-col gap-4 rounded-2xl border-l-[6px] {{ $theme['stripe'] }} bg-white px-6 py-6 shadow-md sm:flex-row sm:items-center sm:justify-between">
        <div>
            @if($eyebrow)<p class="text-xs font-bold uppercase tracking-wider text-cyan-600">{{ $eyebrow }}</p>@endif
            <h1 class="mt-1 text-2xl font-bold text-slate-900">{{ $title }}</h1>
            @if($subtitle)<p class="mt-2 max-w-2xl text-sm text-slate-600">{{ $subtitle }}</p>@endif
        </div>
        @isset($actions)<div class="flex shrink-0 flex-wrap gap-2">{{ $actions }}</div>@endisset
    </header>

@elseif($headerType === 'pipeline')
    <header class="mb-8 overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-blue-100">
        <div class="bg-gradient-to-r {{ $theme['header_from'] }} {{ $theme['header_to'] }} px-6 py-5">
            @if($eyebrow)<p class="text-xs font-semibold uppercase tracking-wider text-blue-100">{{ $eyebrow }}</p>@endif
            <h1 class="text-2xl font-bold text-white">{{ $title }}</h1>
            @if($subtitle)<p class="mt-1 text-sm text-blue-100">{{ $subtitle }}</p>@endif
        </div>
        <div class="flex flex-wrap items-center gap-2 border-t border-blue-50 bg-blue-50/40 px-4 py-3 text-[11px] font-semibold text-blue-800">
            <span class="rounded-full bg-blue-600 px-2.5 py-1 text-white">1. Recibida</span>
            <span class="text-blue-300">→</span>
            <span class="rounded-full bg-white px-2.5 py-1 ring-1 ring-blue-200">2. Documentos</span>
            <span class="text-blue-300">→</span>
            <span class="rounded-full bg-white px-2.5 py-1 ring-1 ring-blue-200">3. Evaluación</span>
            <span class="text-blue-300">→</span>
            <span class="rounded-full bg-white px-2.5 py-1 ring-1 ring-blue-200">4. Resultado</span>
        </div>
        @isset($actions)<div class="border-t border-slate-100 px-4 py-3">{{ $actions }}</div>@endisset
    </header>

@elseif($headerType === 'podium')
    <header class="mb-8 overflow-hidden rounded-2xl shadow-lg">
        <div class="bg-gradient-to-r {{ $theme['header_from'] }} {{ $theme['header_to'] }} px-6 py-6 text-center sm:text-left">
            <div class="flex flex-col items-center gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    @if($eyebrow)<p class="text-xs font-semibold uppercase tracking-wider text-emerald-100">{{ $eyebrow }}</p>@endif
                    <h1 class="mt-1 flex items-center gap-2 text-2xl font-bold text-white sm:text-3xl">
                        <span class="text-3xl">🏆</span> {{ $title }}
                    </h1>
                    @if($subtitle)<p class="mt-2 text-sm text-emerald-50">{{ $subtitle }}</p>@endif
                </div>
                @isset($actions)<div class="flex flex-wrap gap-2">{{ $actions }}</div>@endisset
            </div>
        </div>
        <div class="flex h-2">
            <span class="flex-1 bg-amber-400"></span>
            <span class="w-8 flex-none bg-slate-300"></span>
            <span class="flex-1 bg-amber-700"></span>
        </div>
    </header>

@elseif($headerType === 'queue')
    <header class="mb-8 flex gap-4">
        <div class="flex w-2 shrink-0 flex-col items-center rounded-full bg-gradient-to-b from-amber-400 via-slate-400 to-slate-600"></div>
        <div class="flex-1 rounded-2xl bg-white p-6 shadow-md ring-1 ring-slate-200">
            @if($eyebrow)<p class="text-xs font-bold uppercase tracking-wider text-amber-700">{{ $eyebrow }}</p>@endif
            <h1 class="mt-1 text-2xl font-bold text-slate-900">{{ $title }}</h1>
            @if($subtitle)<p class="mt-2 text-sm text-slate-600">{{ $subtitle }}</p>@endif
            @isset($actions)<div class="mt-4 flex flex-wrap gap-2">{{ $actions }}</div>@endisset
        </div>
    </header>

@elseif($headerType === 'folder')
    <header class="relative mb-8">
        <div class="absolute left-6 top-0 h-8 w-24 rounded-t-xl bg-rose-200/80"></div>
        <div class="relative mt-4 overflow-hidden rounded-2xl rounded-tl-none bg-white shadow-lg ring-1 ring-rose-100">
            <div class="bg-gradient-to-r {{ $theme['header_from'] }} {{ $theme['header_to'] }} px-6 py-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        @if($eyebrow)<p class="text-xs font-semibold uppercase text-rose-100">{{ $eyebrow }}</p>@endif
                        <h1 class="mt-1 text-2xl font-bold text-white">{{ $title }}</h1>
                        @if($subtitle)<p class="mt-2 text-sm text-rose-100">{{ $subtitle }}</p>@endif
                    </div>
                    @isset($actions)<div class="flex flex-wrap gap-2">{{ $actions }}</div>@endisset
                </div>
            </div>
        </div>
    </header>

@elseif($headerType === 'timeline')
    <header class="mb-8 rounded-2xl border border-zinc-200 bg-zinc-900 px-6 py-6 text-white shadow-lg">
        @if($eyebrow)<p class="text-xs font-mono uppercase tracking-widest text-zinc-400">{{ $eyebrow }}</p>@endif
        <h1 class="mt-2 text-2xl font-bold">{{ $title }}</h1>
        @if($subtitle)<p class="mt-2 text-sm text-zinc-400">{{ $subtitle }}</p>@endif
        @isset($actions)<div class="mt-4 flex flex-wrap gap-2">{{ $actions }}</div>@endisset
    </header>

@elseif($headerType === 'analytics')
    <header class="mb-8 grid gap-4 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl bg-gradient-to-br {{ $theme['header_from'] }} {{ $theme['header_via'] }} {{ $theme['header_to'] }} p-6 text-white shadow-lg">
            @if($eyebrow)<p class="text-xs font-semibold uppercase tracking-wider text-fuchsia-200">{{ $eyebrow }}</p>@endif
            <h1 class="mt-2 text-2xl font-bold sm:text-3xl">{{ $title }}</h1>
            @if($subtitle)<p class="mt-2 text-sm text-fuchsia-100">{{ $subtitle }}</p>@endif
        </div>
        <div class="flex flex-col justify-center rounded-2xl border border-fuchsia-100 bg-white/80 p-5 shadow-sm backdrop-blur-sm">
            @isset($actions)<div class="flex flex-col gap-2">{{ $actions }}</div>@endisset
        </div>
    </header>

@elseif($headerType === 'action')
    <header class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r {{ $theme['header_from'] }} {{ $theme['header_to'] }} shadow-lg">
        <div class="flex flex-col gap-4 px-6 py-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-4">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-2xl">⚡</span>
                <div>
                    @if($eyebrow)<p class="text-xs font-semibold uppercase text-orange-100">{{ $eyebrow }}</p>@endif
                    <h1 class="text-2xl font-bold text-white">{{ $title }}</h1>
                    @if($subtitle)<p class="mt-1 text-sm text-orange-50">{{ $subtitle }}</p>@endif
                </div>
            </div>
            @isset($actions)<div class="flex flex-wrap gap-2">{{ $actions }}</div>@endisset
        </div>
    </header>

@elseif($headerType === 'checklist')
    <header class="mb-8 rounded-2xl border border-amber-200 bg-white p-6 shadow-md">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex gap-4">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5h11M9 12h11M9 19h11M4 6h.01M4 13h.01M4 20h.01"/></svg>
                </span>
                <div>
                    @if($eyebrow)<p class="text-xs font-bold uppercase text-amber-700">{{ $eyebrow }}</p>@endif
                    <h1 class="text-2xl font-bold text-slate-900">{{ $title }}</h1>
                    @if($subtitle)<p class="mt-2 text-sm text-slate-600">{{ $subtitle }}</p>@endif
                </div>
            </div>
            @isset($actions)<div class="flex flex-wrap gap-2">{{ $actions }}</div>@endisset
        </div>
    </header>

@else
    <header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            @if($eyebrow)<p class="text-xs font-medium uppercase tracking-wider text-slate-400">{{ $eyebrow }}</p>@endif
            <h1 class="mt-1 text-2xl font-bold text-slate-900 sm:text-3xl">{{ $title }}</h1>
            @if($subtitle)<p class="mt-2 max-w-2xl text-sm text-slate-500">{{ $subtitle }}</p>@endif
        </div>
        @isset($actions)<div class="flex flex-wrap gap-2">{{ $actions }}</div>@endisset
    </header>
@endif
