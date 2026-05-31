<section id="colegios" class="relative bg-gradient-to-b from-white to-slate-50/80 py-20 md:py-28 overflow-hidden">
    <div class="absolute inset-0 opacity-30 [background-image:radial-gradient(#93c5fd_1px,transparent_1px)] [background-size:24px_24px]"></div>

    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative">
        <div x-data="revealOnScroll" x-init="init()" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" class="transition-all duration-700 text-center max-w-2xl mx-auto">
            <span class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-blue-600">
                Catálogo de colegios
            </span>
            <h2 class="mt-5 text-3xl md:text-4xl font-bold text-slate-900">Elija dónde postular</h2>
            <p class="mt-4 text-slate-600 leading-relaxed">
                Cada tarjeta muestra si hay <strong class="text-emerald-700">convocatorias abiertas</strong>.
                Entre al colegio, regístrese como tutor y complete la postulación en pocos pasos.
            </p>
        </div>

        @if($colegiosDestacados->isEmpty())
            <div class="mt-12 rounded-3xl border border-dashed border-slate-200 bg-white px-8 py-14 text-center text-slate-500 shadow-sm">
                <p class="text-lg font-semibold text-slate-700">Aún no hay colegios publicados</p>
                <p class="mt-2 text-sm">Las unidades educativas aparecerán aquí cuando estén disponibles.</p>
            </div>
        @else
            <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($colegiosDestacados as $colegio)
                    @include('public.colegios._colegio-card', ['unidad' => $colegio, 'compact' => true])
                @endforeach
            </div>

            <div class="mt-10 text-center">
                <a href="{{ route('colegios.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border-2 border-blue-200 bg-white px-8 py-3.5 text-sm font-semibold text-blue-700 shadow-sm hover:border-blue-400 hover:bg-blue-50 transition">
                    Ver todos los colegios
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>
        @endif
    </div>
</section>
