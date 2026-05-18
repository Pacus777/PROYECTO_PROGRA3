@props([
    'name',
    'title' => '',
    'subtitle' => '',
    'maxWidth' => 'max-w-2xl',
    'open' => false,
])

<div
    x-data="{ open: @js((bool) $open) }"
    x-on:open-modal.window="if ($event.detail === @js($name)) { open = true; document.body.classList.add('overflow-hidden'); }"
    x-on:close-modal.window="if (! $event.detail || $event.detail === @js($name)) { open = false; document.body.classList.remove('overflow-hidden'); }"
    @keydown.escape.window="if (open) { open = false; document.body.classList.remove('overflow-hidden'); }"
    {{ $attributes }}
>
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-[70] flex items-end justify-center p-4 sm:items-center sm:p-6"
        role="dialog"
        aria-modal="true"
    >
        <div
            x-show="open"
            x-transition.opacity
            class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
            @click="open = false; document.body.classList.remove('overflow-hidden')"
        ></div>

        <div
            x-show="open"
            x-transition
            class="relative flex max-h-[min(90vh,820px)] w-full {{ $maxWidth }} flex-col overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200"
            @click.stop
        >
            @if($title)
                <div class="flex shrink-0 items-start justify-between gap-3 border-b border-slate-100 bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-4 text-white">
                    <div class="min-w-0 pr-2">
                        <h2 class="text-lg font-bold leading-tight">{{ $title }}</h2>
                        @if($subtitle)
                            <p class="mt-0.5 text-sm text-white/80">{{ $subtitle }}</p>
                        @endif
                    </div>
                    <button
                        type="button"
                        class="rounded-lg p-1.5 text-white/90 transition hover:bg-white/15"
                        @click="open = false; document.body.classList.remove('overflow-hidden')"
                        aria-label="Cerrar"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            <div class="custom-scrollbar min-h-0 flex-1 overflow-y-auto px-5 py-4 sm:px-6">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="shrink-0 border-t border-slate-100 bg-slate-50/80 px-5 py-3 sm:px-6">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
