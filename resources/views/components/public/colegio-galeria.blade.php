@props(['fotos' => [], 'nombre' => 'Colegio'])

@if(count($fotos) > 0)
    <div {{ $attributes->merge(['class' => '']) }} x-data="{ activa: 0, fotos: @js(array_values($fotos)) }">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 shadow-sm">
            <div class="relative aspect-[16/10] sm:aspect-[16/9]">
                <template x-for="(foto, i) in fotos" :key="i">
                    <img :src="foto" :alt="'{{ $nombre }} — foto ' + (i + 1)"
                         x-show="activa === i"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="absolute inset-0 h-full w-full object-cover"
                         loading="lazy">
                </template>
                <template x-if="fotos.length > 1">
                    <div class="absolute inset-x-0 bottom-0 flex justify-between p-3">
                        <button type="button" @click="activa = activa > 0 ? activa - 1 : fotos.length - 1"
                                class="rounded-full bg-black/40 p-2 text-white backdrop-blur hover:bg-black/55" aria-label="Anterior">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" @click="activa = activa < fotos.length - 1 ? activa + 1 : 0"
                                class="rounded-full bg-black/40 p-2 text-white backdrop-blur hover:bg-black/55" aria-label="Siguiente">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>
        <template x-if="fotos.length > 1">
            <div class="mt-3 grid grid-cols-4 sm:grid-cols-6 gap-2">
                <template x-for="(foto, i) in fotos" :key="'thumb-' + i">
                    <button type="button" @click="activa = i"
                            :class="activa === i ? 'ring-2 ring-blue-500 ring-offset-2' : 'opacity-70 hover:opacity-100'"
                            class="overflow-hidden rounded-lg aspect-square">
                        <img :src="foto" alt="" class="h-full w-full object-cover">
                    </button>
                </template>
            </div>
        </template>
    </div>
@endif
