<section id="inicio" class="relative overflow-hidden bg-gradient-to-br from-slate-50 via-blue-50/30 to-cyan-50/20">
    <div class="absolute inset-0 opacity-40 [background-image:radial-gradient(#bfdbfe_1px,transparent_1px)] [background-size:22px_22px]"></div>
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-24 md:py-32 relative">
        <div class="grid lg:grid-cols-2 gap-14 items-center">
            <div x-data="revealOnScroll" x-init="init()" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" class="order-2 lg:order-1 transition-all duration-700">
                <span class="inline-flex items-center gap-2 bg-blue-50 text-blue-600 text-xs font-semibold px-4 py-1.5 rounded-full border border-blue-100">
                    ✦ Sistema de Admisión Digital 2025
                </span>

                <h1 class="mt-6 text-5xl md:text-6xl font-extrabold text-slate-900 leading-tight">
                    Postula a la mejor institución<br>
                    <span class="relative inline-block">
                        de forma <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-cyan-500">simple y transparente</span>
                        <span class="absolute left-0 -bottom-1.5 h-2 w-full bg-gradient-to-r from-blue-200 to-cyan-200 rounded-full -z-10"></span>
                    </span>
                </h1>

                <p class="mt-6 text-lg text-slate-600 max-w-xl leading-relaxed">
                    Los tutores y apoderados gestionan la admisión de sus hijos con el <strong class="font-semibold text-slate-700">RUDE</strong>. Sin cuenta para el estudiante: registro, postulación y seguimiento en un solo lugar.
                </p>

                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="{{ route('login.show') }}" class="px-8 py-4 rounded-xl text-base font-semibold text-white bg-gradient-to-r from-blue-600 to-cyan-500 shadow-lg shadow-blue-300/50 hover:scale-105 active:scale-95 transition-transform">Ingresar como tutor →</a>
                    <a href="{{ route('login.show') }}" class="px-8 py-4 rounded-xl text-base font-semibold border-2 border-slate-200 text-slate-700 hover:border-blue-400 hover:text-blue-600 transition-all">Iniciar sesión</a>
                </div>

                <div class="mt-8 flex flex-wrap gap-6 text-sm font-medium text-slate-600">
                    <span class="inline-flex items-center gap-2"><svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.7-9.3a1 1 0 10-1.4-1.4L9 10.59 7.7 9.3a1 1 0 10-1.4 1.4l2 2a1 1 0 001.4 0l4-4z"/></svg>500+ Postulaciones</span>
                    <span class="inline-flex items-center gap-2"><svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.06 3.264a1 1 0 00.95.69h3.432c.969 0 1.371 1.24.588 1.81l-2.777 2.018a1 1 0 00-.364 1.118l1.06 3.264c.3.921-.755 1.688-1.538 1.118l-2.777-2.018a1 1 0 00-1.176 0l-2.777 2.018c-.783.57-1.838-.197-1.539-1.118l1.061-3.264a1 1 0 00-.364-1.118L2.02 8.69c-.783-.57-.38-1.81.588-1.81H6.04a1 1 0 00.95-.69l1.06-3.264z"/></svg>98% Satisfacción</span>
                    <span class="inline-flex items-center gap-2"><svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2M12 22a10 10 0 110-20 10 10 0 010 20z"/></svg>3 min Registro</span>
                </div>
            </div>

            <div class="order-1 lg:order-2 relative h-[500px] lg:h-[600px]">
                <div class="absolute -z-10 right-8 top-8 w-96 h-96 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-full blur-3xl opacity-60"></div>
                {{-- Imagen local: coloca el archivo en public/images/landing-hero.jpg (o cambia el nombre y esta ruta) --}}
                <img src="{{ asset('images/landing-hero.jpg') }}" alt="Familia y proceso de admisión escolar" class="absolute inset-0 w-full h-full object-cover rounded-3xl shadow-2xl border border-white">

                <div class="absolute bottom-10 -left-4 bg-white rounded-2xl shadow-xl p-4 border border-slate-100 animate-bounce [animation-duration:3s]">
                    <p class="text-xs text-slate-500">Postulación enviada</p>
                    <p class="font-semibold text-slate-900 mt-1">Familia Quispe <span class="text-emerald-600">✓</span></p>
                </div>
                <div class="absolute top-12 -right-4 bg-white rounded-2xl shadow-xl p-4 border border-slate-100 animate-bounce [animation-duration:3s] [animation-delay:400ms]">
                    <p class="text-xs text-slate-500">Resultado</p>
                    <p class="font-semibold text-slate-900 mt-1">Cupo asignado 🎉</p>
                </div>
            </div>
        </div>
    </div>
</section>
