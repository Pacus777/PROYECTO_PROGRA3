<header
    x-data="navbarState"
    x-init="init()"
    :class="scrolled ? 'shadow-md border-slate-200' : 'border-slate-100'"
    class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b transition-all duration-300"
>
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="h-20 flex items-center justify-between gap-4">
            <a href="#inicio" class="flex items-center gap-2">
                <svg class="w-7 h-7 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 18h16M6 15l6-8 6 8M9 15v3m6-3v3" />
                </svg>
                <span class="font-extrabold text-xl text-slate-900">AdmisiónEscolar</span>
            </a>

            <nav class="hidden lg:flex items-center gap-8 text-sm font-medium text-slate-600">
                <a href="#inicio" class="hover:text-blue-600 transition-colors duration-200">Inicio</a>
                <a href="#como-funciona" class="hover:text-blue-600 transition-colors duration-200">Cómo funciona</a>
                <a href="#beneficios" class="hover:text-blue-600 transition-colors duration-200">Beneficios</a>
                <a href="#contacto" class="hover:text-blue-600 transition-colors duration-200">Contacto</a>
            </nav>

            <div class="hidden lg:flex items-center gap-3">
                <a href="{{ route('login.show') }}" class="px-5 py-2.5 rounded-xl border border-blue-600 text-blue-600 font-semibold hover:bg-blue-50 transition-colors duration-200">Iniciar sesión</a>
                <a href="{{ route('login.show') }}" class="px-5 py-2.5 rounded-xl text-white bg-gradient-to-r from-blue-600 to-cyan-500 font-semibold shadow-lg shadow-blue-200 hover:opacity-90 hover:scale-105 active:scale-95 transition-all duration-200">Acceso tutor</a>
            </div>

            <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 rounded-lg border border-slate-200 text-slate-700" aria-label="Abrir menú">
                <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M6 18L18 6"/></svg>
            </button>
        </div>
    </div>

    <div
        x-show="mobileOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="lg:hidden border-t border-slate-100 bg-white/95"
    >
        <div class="max-w-7xl mx-auto px-6 py-4 space-y-3 text-slate-700">
            <a @click="mobileOpen=false" href="#inicio" class="block">Inicio</a>
            <a @click="mobileOpen=false" href="#como-funciona" class="block">Cómo funciona</a>
            <a @click="mobileOpen=false" href="#beneficios" class="block">Beneficios</a>
            <a @click="mobileOpen=false" href="#contacto" class="block">Contacto</a>
            <div class="pt-3 flex flex-col gap-2">
                <a href="{{ route('login.show') }}" class="text-center px-4 py-2.5 rounded-xl border border-blue-600 text-blue-600 font-semibold">Iniciar sesión</a>
                <a href="{{ route('login.show') }}" class="text-center px-4 py-2.5 rounded-xl text-white bg-gradient-to-r from-blue-600 to-cyan-500 font-semibold">Acceso tutor</a>
            </div>
        </div>
    </div>
</header>
