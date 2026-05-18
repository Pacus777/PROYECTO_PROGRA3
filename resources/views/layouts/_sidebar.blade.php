{{-- Sidebar premium reutilizable para dashboard --}}
<aside
    :class="[
        mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        sidebarOpen ? 'w-64' : 'w-20'
    ]"
    class="custom-scrollbar fixed left-0 top-0 z-50 flex h-full flex-col overflow-y-auto overflow-x-hidden border-r border-slate-100 bg-white shadow-xl transition-all duration-300 lg:shadow-none"
>
    <div class="flex h-16 items-center border-b border-slate-100 px-4">
        <a href="{{ $dashboardRole === \App\Support\Roles::TUTOR ? route('tutor.dashboard') : route('dashboard') }}" class="flex min-w-0 items-center gap-3">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8.5L12 4l9 4.5-9 4.5-9-4.5zm3 4.5v3.5c0 1.9 2.69 3.5 6 3.5s6-1.6 6-3.5V13"/>
                </svg>
            </span>
            <span x-show="sidebarOpen" x-transition class="truncate text-lg font-bold text-indigo-700">AdmisiónEscolar</span>
        </a>

        <button
            type="button"
            @click="sidebarOpen = !sidebarOpen"
            class="ml-auto hidden rounded-lg bg-indigo-50 p-1.5 text-indigo-600 transition hover:bg-indigo-100 lg:inline-flex"
        >
            <svg x-show="sidebarOpen" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            <svg x-show="!sidebarOpen" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    <div class="border-b border-slate-100 px-4 py-4">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 text-sm font-bold text-white">
                {{ $dashboardInitial ?: 'U' }}
            </div>
            <div x-show="sidebarOpen" x-transition class="min-w-0">
                <p class="truncate text-sm font-semibold text-slate-800">{{ $dashboardUserName }}</p>
                <p class="truncate text-xs text-slate-400">{{ $dashboardRoleLabel ?? \App\Support\Roles::label($dashboardRole) }}</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 py-3">
        @if($dashboardRole === \App\Support\Roles::ADMIN_GENERAL)
            <p class="px-4 pb-2 pt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400" x-show="sidebarOpen">Ministerio de Educación</p>
            <a href="{{ route('dashboard') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-8 9 8v8a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-8z"/></svg>
                <span x-show="sidebarOpen" x-transition>Inicio</span>
            </a>
            <a href="{{ route('admin.usuarios.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.usuarios.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-1a4 4 0 00-5-3.87M17 20H7m10 0v-1c0-1.66-1.34-3-3-3h-4c-1.66 0-3 1.34-3 3v1m0 0H2v-1a4 4 0 015-3.87M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM8 10a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                <span x-show="sidebarOpen" x-transition>Cuentas de acceso</span>
            </a>
            <a href="{{ route('admin.unidades.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.unidades.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 21h16M6 21V7l6-4 6 4v14M9 9h.01M9 12h.01M9 15h.01M15 9h.01M15 12h.01M15 15h.01"/></svg>
                <span x-show="sidebarOpen" x-transition>Unidades Educativas</span>
            </a>
            <a href="{{ route('admin.estudiantes.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.estudiantes.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6"/></svg>
                <span x-show="sidebarOpen" x-transition>Postulantes</span>
            </a>
            <a href="{{ route('admin.tutores.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.tutores.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span x-show="sidebarOpen" x-transition>Tutores</span>
            </a>
            <a href="{{ route('admin.gestiones.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.gestiones.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span x-show="sidebarOpen" x-transition>Gestiones</span>
            </a>

            <p class="px-4 pb-2 pt-4 text-[10px] font-bold uppercase tracking-widest text-slate-400" x-show="sidebarOpen">Postulaciones (nivel país)</p>
            <a href="{{ route('admin.postulaciones.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.postulaciones.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                <span x-show="sidebarOpen" x-transition>Postulaciones</span>
            </a>

            <p class="px-4 pb-2 pt-4 text-[10px] font-bold uppercase tracking-widest text-slate-400" x-show="sidebarOpen">Reportes y catálogos</p>
            <a href="{{ route('admin.reportes.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.reportes.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 16v-3m4 3V8m4 8v-5"/></svg>
                <span x-show="sidebarOpen" x-transition>Descargar reportes</span>
            </a>
            <a href="{{ route('admin.estados-postulacion.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.estados-postulacion.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5h11M9 12h11M9 19h11M4 6h.01M4 13h.01M4 20h.01"/></svg>
                <span x-show="sidebarOpen" x-transition>Estados postulación</span>
            </a>
            <a href="{{ route('admin.tipos-documento.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.tipos-documento.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 3v5h5"/></svg>
                <span x-show="sidebarOpen" x-transition>Tipos de documento</span>
            </a>
        @elseif($dashboardRole === \App\Support\Roles::TUTOR)
            <p class="px-4 pb-2 pt-2 text-[10px] font-bold uppercase tracking-widest text-teal-600" x-show="sidebarOpen">Tutor</p>
            <a href="{{ route('tutor.dashboard') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('tutor.dashboard') ? 'bg-teal-50 text-teal-800 ring-1 ring-teal-100' : 'text-slate-600 hover:bg-teal-50 hover:text-teal-800' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-8 9 8v8a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-8z"/></svg>
                <span x-show="sidebarOpen" x-transition>Inicio</span>
            </a>
            <a href="{{ route('dashboard') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-slate-100 text-slate-800' : 'text-slate-600 hover:bg-slate-50' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span x-show="sidebarOpen" x-transition>Resumen cuenta</span>
            </a>

            <p class="px-4 pb-2 pt-4 text-[10px] font-bold uppercase tracking-widest text-slate-400" x-show="sidebarOpen">Mis tutelados</p>
            <a href="{{ route('tutor.estudiantes.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('tutor.estudiantes.*') ? 'bg-teal-50 text-teal-800 ring-1 ring-teal-100' : 'text-slate-600 hover:bg-teal-50 hover:text-teal-800' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-1a4 4 0 00-5-3.87M9 20H4v-1a4 4 0 015-3.87m0-6.13a4 4 0 110-8 4 4 0 010 8zm8 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span x-show="sidebarOpen" x-transition>Estudiantes</span>
            </a>

            <p class="px-4 pb-2 pt-4 text-[10px] font-bold uppercase tracking-widest text-slate-400" x-show="sidebarOpen">Admisión</p>
            <a href="{{ route('tutor.postulaciones.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('tutor.postulaciones.index') || request()->routeIs('tutor.postulaciones.show') ? 'bg-teal-50 text-teal-800 ring-1 ring-teal-100' : 'text-slate-600 hover:bg-teal-50 hover:text-teal-800' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                <span x-show="sidebarOpen" x-transition>Postulaciones</span>
            </a>
            <a href="{{ route('tutor.postulaciones.create') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('tutor.postulaciones.create') ? 'bg-teal-50 text-teal-800 ring-1 ring-teal-100' : 'text-slate-600 hover:bg-teal-50 hover:text-teal-800' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span x-show="sidebarOpen" x-transition>Nueva postulación</span>
            </a>
            <a href="{{ route('tutor.documentos.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('tutor.documentos.*') ? 'bg-teal-50 text-teal-800 ring-1 ring-teal-100' : 'text-slate-600 hover:bg-teal-50 hover:text-teal-800' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 3v5h5"/></svg>
                <span x-show="sidebarOpen" x-transition>Documentos</span>
            </a>
            <a href="{{ route('tutor.seguimiento.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('tutor.seguimiento.*') ? 'bg-teal-50 text-teal-800 ring-1 ring-teal-100' : 'text-slate-600 hover:bg-teal-50 hover:text-teal-800' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-show="sidebarOpen" x-transition>Seguimiento</span>
            </a>
            <a href="{{ route('tutor.resultados.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('tutor.resultados.*') ? 'bg-teal-50 text-teal-800 ring-1 ring-teal-100' : 'text-slate-600 hover:bg-teal-50 hover:text-teal-800' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8M12 17v4M7 4h10v5a5 5 0 01-10 0V4z"/></svg>
                <span x-show="sidebarOpen" x-transition>Resultados</span>
            </a>

            <p class="px-4 pb-2 pt-4 text-[10px] font-bold uppercase tracking-widest text-slate-400" x-show="sidebarOpen">Cuenta</p>
            <a href="{{ route('tutor.perfil.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('tutor.perfil.*') ? 'bg-teal-50 text-teal-800 ring-1 ring-teal-100' : 'text-slate-600 hover:bg-teal-50 hover:text-teal-800' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-show="sidebarOpen" x-transition>Mi perfil</span>
            </a>
        @elseif($dashboardRole === \App\Support\Roles::ADMIN_INSTITUCIONAL)
            <p class="px-4 pb-2 pt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400" x-show="sidebarOpen">Principal</p>
            <a href="{{ route('admin.institucional.dashboard') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.institucional.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-8 9 8v8a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-8z"/></svg>
                <span x-show="sidebarOpen" x-transition>Inicio</span>
            </a>

            <p class="px-4 pb-2 pt-4 text-[10px] font-bold uppercase tracking-widest text-slate-400" x-show="sidebarOpen">Gestión académica</p>
            <a href="{{ route('admin.institucional.academic.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.institucional.academic.*') || request()->routeIs('admin.institucional.niveles.*') || request()->routeIs('admin.institucional.cursos.*') || request()->routeIs('admin.institucional.paralelos.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6"/></svg>
                <span x-show="sidebarOpen" x-transition>Académico</span>
            </a>
            <a href="{{ route('admin.institucional.ofertas.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.institucional.ofertas.*') || request()->routeIs('admin.institucional.cupos.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7L12 3 4 7l8 4 8-4zM4 12l8 4 8-4M4 17l8 4 8-4"/></svg>
                <span x-show="sidebarOpen" x-transition>Ofertas</span>
            </a>
            <a href="{{ route('admin.institucional.postulaciones.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.institucional.postulaciones.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                <span x-show="sidebarOpen" x-transition>Postulaciones</span>
            </a>

            <p class="px-4 pb-2 pt-4 text-[10px] font-bold uppercase tracking-widest text-slate-400" x-show="sidebarOpen">Evaluación</p>
            <a href="{{ route('admin.institucional.criterios.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.institucional.criterios.*') || request()->routeIs('admin.institucional.evaluaciones.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5h11M9 12h11M9 19h11M4 6h.01M4 13h.01M4 20h.01"/></svg>
                <span x-show="sidebarOpen" x-transition>Evaluación</span>
            </a>
            <a href="{{ route('admin.institucional.resultados.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.institucional.resultados.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8M12 17v4M7 4h10v5a5 5 0 01-10 0V4z"/></svg>
                <span x-show="sidebarOpen" x-transition>Resultados</span>
            </a>
            <a href="{{ route('admin.institucional.resultados.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.institucional.asignacion.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 3L4 14h7l-1 7 9-11h-7l1-7z"/></svg>
                <span x-show="sidebarOpen" x-transition>Asignación</span>
            </a>

            <a href="{{ route('admin.institucional.documentos.index') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.institucional.documentos.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 3v5h5"/></svg>
                <span x-show="sidebarOpen" x-transition>Documentos / OCR</span>
            </a>

            <p class="px-4 pb-2 pt-4 text-[10px] font-bold uppercase tracking-widest text-slate-400" x-show="sidebarOpen">Próximamente</p>
            @foreach([['Lista de Espera','clock'],['Historial','archive'],['Reportes','chart']] as $coming)
                <a href="#" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition-all hover:bg-indigo-50 hover:text-indigo-700">
                    @if($coming[1] === 'clock')
                        <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($coming[1] === 'doc')
                        <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 3v5h5"/></svg>
                    @elseif($coming[1] === 'archive')
                        <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16v13a1 1 0 01-1 1H5a1 1 0 01-1-1V7zm2-4h12v4H6V3zm4 8h4"/></svg>
                    @else
                        <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16M7 16V8m5 8V5m5 11v-6"/></svg>
                    @endif
                    <span x-show="sidebarOpen" x-transition>{{ $coming[0] }}</span>
                    <span x-show="sidebarOpen" class="ml-auto rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-600">Pronto</span>
                </a>
            @endforeach
        @else
            <p class="px-4 pb-2 pt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400" x-show="sidebarOpen">Principal</p>
            <a href="{{ route('dashboard') }}" class="group mx-3 mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-8 9 8v8a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-8z"/></svg>
                <span x-show="sidebarOpen" x-transition>Inicio</span>
            </a>
        @endif
    </nav>

    @php
        $helpTips = match ($dashboardRole ?? '') {
            \App\Support\Roles::ADMIN_GENERAL => [
                'title' => 'Ayuda — Ministerio',
                'lines' => [
                    'Gestiona unidades, postulantes y cuentas desde el menú lateral.',
                    'En «Descargar reportes» obtienes listados en Excel o PDF.',
                    'Si un tutor no ve a su estudiante, revisa el vínculo en Tutores o Postulantes.',
                ],
                'link' => ['label' => 'Ir a reportes', 'url' => route('admin.reportes.index')],
            ],
            \App\Support\Roles::ADMIN_INSTITUCIONAL => [
                'title' => 'Ayuda — Unidad educativa',
                'lines' => [
                    'Registra ofertas y revisa postulaciones de tu colegio.',
                    'En Documentos / OCR validas los archivos que suben los tutores.',
                    'Los resultados y la asignación se publican desde Evaluación y Resultados.',
                ],
                'link' => ['label' => 'Ver postulaciones', 'url' => route('admin.institucional.postulaciones.index')],
            ],
            \App\Support\Roles::TUTOR => [
                'title' => 'Ayuda — Tutor',
                'lines' => [
                    'Vincula estudiantes en «Estudiantes» antes de postular.',
                    'Crea una postulación y sube los documentos solicitados.',
                    'Consulta el estado en Seguimiento y en Resultados cuando estén publicados.',
                ],
                'link' => ['label' => 'Nueva postulación', 'url' => route('tutor.postulaciones.create')],
            ],
            default => [
                'title' => 'Ayuda',
                'lines' => [
                    'Usa el menú lateral para navegar por el sistema.',
                    'Si no encuentras una opción, contacta al administrador de tu unidad educativa.',
                ],
                'link' => ['label' => 'Volver al inicio', 'url' => route('dashboard')],
            ],
        };
    @endphp

    <div class="mt-auto border-t border-slate-100 p-4" x-data="{ helpOpen: false }">
        <button
            type="button"
            @click="helpOpen = true"
            class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-medium text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-700"
        >
            <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition>¿Necesitas ayuda?</span>
        </button>
        <p x-show="sidebarOpen" x-transition class="mt-2 px-3 text-[10px] text-slate-300">v1.0.0</p>

        <div
            x-show="helpOpen"
            x-cloak
            class="fixed inset-0 z-[70] flex items-end justify-center p-4 sm:items-center"
            role="dialog"
            aria-modal="true"
            aria-labelledby="help-dialog-title"
        >
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="helpOpen = false"></div>
            <div
                x-show="helpOpen"
                x-transition
                class="relative w-full max-w-md rounded-2xl border border-slate-100 bg-white p-6 shadow-2xl"
                @click.stop
            >
                <div class="mb-4 flex items-start justify-between gap-3">
                    <h2 id="help-dialog-title" class="text-lg font-bold text-slate-900">{{ $helpTips['title'] }}</h2>
                    <button
                        type="button"
                        @click="helpOpen = false"
                        class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                        aria-label="Cerrar"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <ul class="space-y-2.5 text-sm leading-relaxed text-slate-600">
                    @foreach($helpTips['lines'] as $line)
                        <li class="flex gap-2">
                            <span class="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-indigo-500"></span>
                            <span>{{ $line }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-6 flex flex-wrap gap-2">
                    <a
                        href="{{ $helpTips['link']['url'] }}"
                        class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                        @click="helpOpen = false"
                    >
                        {{ $helpTips['link']['label'] }}
                    </a>
                    <button
                        type="button"
                        @click="helpOpen = false"
                        class="inline-flex items-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</aside>

