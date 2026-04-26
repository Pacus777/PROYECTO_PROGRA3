<section id="como-funciona" class="bg-gradient-to-b from-slate-50 to-blue-50/40 py-24 md:py-32">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            <span class="inline-flex items-center px-4 py-1.5 rounded-full border border-blue-100 bg-blue-50 text-blue-600 text-xs font-semibold tracking-widest uppercase">Proceso de admisión</span>
            <h2 class="mt-5 text-4xl font-bold text-slate-900">Tres pasos para asegurar tu cupo</h2>
        </div>

        <div class="relative mt-14">
            <div class="hidden lg:block absolute top-28 left-0 right-0 border-t-2 border-dashed border-blue-200"></div>
            <div class="grid lg:grid-cols-3 gap-10 relative">
                @php
                    $steps = [
                        ['n'=>'01','t'=>'Crea tu cuenta','d'=>'Regístrate como tutor o estudiante. Solo necesitas tu correo y datos básicos del postulante.'],
                        ['n'=>'02','t'=>'Completa la postulación','d'=>'Llena el formulario de admisión, adjunta los documentos requeridos y envía tu solicitud.'],
                        ['n'=>'03','t'=>'Recibe tu resultado','d'=>'Sigue el estado en tiempo real. Recibirás una notificación cuando se publiquen los resultados y cupos asignados.'],
                    ];
                @endphp
                @foreach($steps as $s)
                    <article class="text-center bg-white/70 border border-slate-100 rounded-2xl p-8 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <p class="text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-400">{{ $s['n'] }}</p>
                        <div class="w-14 h-14 mx-auto mt-4 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m-4-4l4 2 4-2"/></svg>
                        </div>
                        <h3 class="mt-5 text-xl font-semibold text-slate-900">{{ $s['t'] }}</h3>
                        <p class="mt-3 text-slate-600 leading-relaxed">{{ $s['d'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
