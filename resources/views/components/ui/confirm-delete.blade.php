@props([
    'action',
    'title' => '¿Confirmar eliminación?',
    'message' => 'Esta acción no se puede deshacer.',
    'confirmLabel' => 'Sí, eliminar',
])

<div x-data="{ open: false }" class="inline">
    <button type="button" {{ $attributes->merge(['class' => 'inline-flex rounded-lg bg-red-50 px-2.5 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100']) }}
            @click="open = true">
        {{ $slot->isEmpty() ? 'Eliminar' : $slot }}
    </button>

    <div x-show="open" x-cloak class="fixed inset-0 z-[80] flex items-center justify-center p-4" role="alertdialog">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-slate-900/50" @click="open = false"></div>
        <div x-show="open" x-transition class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl ring-1 ring-slate-200" @click.stop>
            <h3 class="text-lg font-bold text-slate-900">{{ $title }}</h3>
            <p class="mt-2 text-sm text-slate-600">{{ $message }}</p>
            <div class="mt-6 flex flex-wrap justify-end gap-2">
                <button type="button" @click="open = false"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Cancelar
                </button>
                <form method="POST" action="{{ $action }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                        {{ $confirmLabel }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
