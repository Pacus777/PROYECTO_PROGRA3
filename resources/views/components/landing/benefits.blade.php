<section id="beneficios" class="bg-white py-24 md:py-32">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div x-data="revealOnScroll" x-init="init()" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" class="transition-all duration-700 text-center max-w-3xl mx-auto">
            <span class="inline-flex items-center px-4 py-1.5 rounded-full border border-blue-100 bg-blue-50 text-blue-600 text-xs font-semibold tracking-widest uppercase">¿Por qué elegirnos?</span>
            <h2 class="mt-5 text-4xl font-bold text-slate-900">Todo lo que necesitas para una admisión sin complicaciones</h2>
            <p class="mt-4 text-base text-slate-600 leading-relaxed">Diseñamos cada etapa para reducir tiempos, aumentar transparencia y mejorar la experiencia de las familias.</p>
        </div>

        <div class="mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $cards = [
                    ['t'=>'Desde el colegio','d'=>'Elija la unidad educativa, revise convocatorias abiertas y regístrese como tutor en el mismo lugar.','c'=>'text-blue-500','icon'=>'clipboard'],
                    ['t'=>'Seguimiento en Tiempo Real','d'=>'Revisa el estado de tu postulación en cualquier momento con notificaciones automáticas.','c'=>'text-cyan-500','icon'=>'refresh'],
                    ['t'=>'Proceso Transparente','d'=>'Criterios de evaluación claros y resultados publicados con total imparcialidad.','c'=>'text-blue-700','icon'=>'shield'],
                    ['t'=>'Asignación Rápida','d'=>'Sistema automatizado que agiliza la evaluación y asignación de cupos escolares.','c'=>'text-cyan-600','icon'=>'rocket'],
                ];
            @endphp
            @foreach($cards as $card)
                <article class="bg-white border border-slate-100 rounded-2xl p-8 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center mb-6 {{ $card['c'] }}">
                        @if($card['icon']==='clipboard')
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3h6l1 2h3v16H5V5h3l1-2zm-1 8h8m-8 4h8"/></svg>
                        @elseif($card['icon']==='refresh')
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v6h6M20 20v-6h-6M20 9a8 8 0 00-13.66-3.66L4 10M4 15a8 8 0 0013.66 3.66L20 14"/></svg>
                        @elseif($card['icon']==='shield')
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4zm-2.5 9.5l2 2 4-4"/></svg>
                        @else
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7M12 21s8-4 8-10V5l-8-2-8 2v6c0 6 8 10 8 10z"/></svg>
                        @endif
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-3">{{ $card['t'] }}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">{{ $card['d'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
