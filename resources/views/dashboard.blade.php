@extends('layouts.dashboard')

@section('title', 'Dashboard | Sistema de Admisión Escolar')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Inicio</span>
@endsection

@section('content')
    @php
        $rolNombre = $usuario->rol->nombre_rol ?? '';
        $isAdminGeneral = $rolNombre === \App\Support\Roles::ADMIN_GENERAL && !empty($adminDashboard);
    @endphp
    <div class="w-full space-y-8 animate-fadeInUp">
        
        <!-- Header Banner Premium estilo SaaS -->
        <div class="relative overflow-hidden rounded-3xl bg-[#090822] border border-white/10 p-8 sm:p-10 text-white shadow-2xl">
            <!-- Blobs Orgánicos Desenfoques Cohesivos -->
            <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-indigo-600/25 blur-3xl will-change-transform"></div>
            <div class="absolute -left-20 -bottom-20 h-72 w-72 rounded-full bg-purple-600/20 blur-3xl will-change-transform"></div>
            <div class="absolute right-1/3 top-1/4 h-36 w-36 rounded-full bg-pink-500/10 blur-2xl will-change-transform"></div>
            
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-indigo-500/10 border border-indigo-400/30 px-3.5 py-1 text-[10px] sm:text-xs font-bold uppercase tracking-[0.2em] text-indigo-300">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-450 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-400"></span>
                        </span>
                        {{ \App\Support\Roles::label($rolNombre) }}
                    </span>
                    <h1 class="mt-5 text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-none bg-gradient-to-r from-white via-indigo-50 to-indigo-150 bg-clip-text text-transparent">
                        {{ \App\Support\Roles::panelTitle($rolNombre) }}
                    </h1>
                    <p class="mt-3 max-w-2xl text-sm sm:text-base text-indigo-200/70 leading-relaxed font-light">
                        {{ \App\Support\Roles::panelSubtitle($rolNombre) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Sección de Resumen Rápido con diseño de Relieve y Profundidad -->
        <section class="rounded-3xl bg-gradient-to-b from-white to-[#F8F9FD] p-8 shadow-[0_10px_35px_rgba(15,23,42,0.03),0_1px_3px_rgba(0,0,0,0.02)] border border-slate-200/90 border-t-indigo-600 border-t-4 transition-all duration-350 hover:shadow-[0_15px_40px_rgba(99,102,241,0.05),0_5px_15px_rgba(0,0,0,0.02)]">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-6">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-650 border border-indigo-100/50 shadow-sm">
                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <h2 class="text-xl font-bold text-slate-800">Resumen rápido</h2>
            </div>
            
            <p class="text-slate-650 leading-relaxed font-light text-[15px]">
                {{ \App\Support\Roles::description($rolNombre) }}
            </p>

            @if($isAdminGeneral)
                <div class="mt-8 border-t border-slate-200/55 pt-8">
                    @include('admin.dashboard.charts', ['adminDashboard' => $adminDashboard])
                </div>

                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <a href="{{ route('admin.usuarios.index') }}"
                       class="group flex items-center gap-4 rounded-2xl bg-gradient-to-b from-white to-[#FAFBFD] border border-indigo-100/30 p-4 shadow-[0_12px_30px_rgba(99,102,241,0.05)] transition-all duration-350 hover:-translate-y-1 hover:bg-indigo-50/20 hover:shadow-xl hover:shadow-indigo-200/25 hover:border-transparent">
                        <span class="rounded-xl bg-indigo-50 p-3 text-indigo-600 transition-colors group-hover:bg-indigo-100 border border-indigo-100/30 shadow-sm">
                            <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-1a4 4 0 00-5-3.87M9 20H4v-1a4 4 0 015-3.87m0-6.13a4 4 0 110-8 4 4 0 010 8zm8 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-800 group-hover:text-indigo-950">Usuarios</p>
                            <p class="text-[11px] text-slate-450 truncate font-light">Gestionar accesos</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.estudiantes.index') }}"
                       class="group flex items-center gap-4 rounded-2xl bg-gradient-to-b from-white to-[#FAFBFD] border border-violet-100/30 p-4 shadow-[0_12px_30px_rgba(139,92,246,0.05)] transition-all duration-350 hover:-translate-y-1 hover:bg-violet-50/20 hover:shadow-xl hover:shadow-violet-200/25 hover:border-transparent">
                        <span class="rounded-xl bg-violet-50 p-3 text-violet-600 transition-colors group-hover:bg-violet-100 border border-violet-100/30 shadow-sm">
                            <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-800 group-hover:text-violet-950">Postulantes</p>
                            <p class="text-[11px] text-slate-450 truncate font-light">Padrón de estudiantes</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.tutores.index') }}"
                       class="group flex items-center gap-4 rounded-2xl bg-gradient-to-b from-white to-[#FAFBFD] border border-fuchsia-100/30 p-4 shadow-[0_12px_30px_rgba(217,70,239,0.05)] transition-all duration-350 hover:-translate-y-1 hover:bg-fuchsia-50/20 hover:shadow-xl hover:shadow-fuchsia-200/25 hover:border-transparent">
                        <span class="rounded-xl bg-fuchsia-50 p-3 text-fuchsia-600 transition-colors group-hover:bg-fuchsia-100 border border-fuchsia-100/30 shadow-sm">
                            <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-800 group-hover:text-fuchsia-950">Tutores</p>
                            <p class="text-[11px] text-slate-450 truncate font-light">Vínculos familiares</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.gestiones.index') }}"
                       class="group flex items-center gap-4 rounded-2xl bg-gradient-to-b from-white to-[#FAFBFD] border border-cyan-100/30 p-4 shadow-[0_12px_30px_rgba(6,182,212,0.05)] transition-all duration-350 hover:-translate-y-1 hover:bg-cyan-50/20 hover:shadow-xl hover:shadow-cyan-200/25 hover:border-transparent">
                        <span class="rounded-xl bg-cyan-50 p-3 text-cyan-600 transition-colors group-hover:bg-cyan-100 border border-cyan-100/30 shadow-sm">
                            <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-800 group-hover:text-cyan-950">Gestiones</p>
                            <p class="text-[11px] text-slate-450 truncate font-light">Ciclos académicos</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.unidades.index') }}"
                       class="group flex items-center gap-4 rounded-2xl bg-gradient-to-b from-white to-[#FAFBFD] border border-emerald-100/30 p-4 shadow-[0_12px_30px_rgba(16,185,129,0.05)] transition-all duration-350 hover:-translate-y-1 hover:bg-emerald-50/20 hover:shadow-xl hover:shadow-emerald-200/25 hover:border-transparent">
                        <span class="rounded-xl bg-emerald-50 p-3 text-emerald-600 transition-colors group-hover:bg-emerald-100 border border-emerald-100/30 shadow-sm">
                            <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 21h16M6 21V7l6-4 6 4v14"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-800 group-hover:text-emerald-950">Unidades</p>
                            <p class="text-[11px] text-slate-450 truncate font-light">Colegios y centros</p>
                        </div>
                    </a>
                </div>
            @endif

            @if(($usuario->rol->nombre_rol ?? '') === \App\Support\Roles::ADMIN_INSTITUCIONAL)
                <div class="mt-8 border-t border-slate-200/60 pt-6">
                    <a href="{{ route('admin.institucional.dashboard') }}"
                       class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-650 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-indigo-200/40 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-indigo-300/40">
                        Ir al panel institucional
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            @endif

            @if(!empty($tutorDashboard['active']))
                @php
                    $td = $tutorDashboard;
                    $stats = $td['stats'] ?? [];
                    $totalPos = (int) ($stats['total_postulaciones'] ?? 0);
                    $nombreTutor = trim(($usuario->persona->nombres_per ?? '').' '.($usuario->persona->ap_paterno_per ?? '')) ?: 'Tutor';
                @endphp

                <div class="mt-10 space-y-8 border-t border-slate-200/60 pt-8">
                    
                    <!-- Tarjeta de Identificación Tutor con Relieve -->
                    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 to-indigo-950 p-6 text-white shadow-xl border border-slate-800">
                        <div class="absolute -right-20 -bottom-20 h-48 w-48 rounded-full bg-teal-500/10 blur-2xl"></div>
                        <div class="relative z-10">
                            <span class="inline-flex items-center rounded-full bg-teal-400/10 border border-teal-400/20 px-3 py-0.5 text-[10px] font-bold uppercase tracking-wider text-teal-300">
                                Rol Tutor
                            </span>
                            <h3 class="mt-3 text-2xl font-black">Hola, {{ $nombreTutor }}</h3>
                            <p class="mt-2 text-sm text-indigo-150 leading-relaxed font-light">
                                Resumen integrado de tus estudiantes vinculados y sus postulaciones. Esta información refleja directamente el estado de las postulaciones registradas en las unidades educativas.
                            </p>
                        </div>
                    </div>

                    @if(!empty($td['warning']))
                        <div class="rounded-2xl border border-amber-200 bg-amber-50/70 p-6 text-amber-900 shadow-[0_4px_15px_rgba(245,158,11,0.03)]">
                            <div class="flex gap-3">
                                <svg class="h-6 w-6 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <div>
                                    @if($td['warning'] === 'sin_registro_tutor')
                                        <p class="font-bold text-slate-800">Perfil de tutor no encontrado</p>
                                        <p class="mt-1 text-sm text-slate-650 leading-relaxed">Tu usuario posee el rol de <strong>tutor</strong>, pero no existe un registro activo de tutor vinculado a tu persona en la base de datos. Por favor, solicita a un administrador que complete este vínculo.</p>
                                    @elseif($td['warning'] === 'sin_estudiantes')
                                        <p class="font-bold text-slate-800">Vincular estudiantes a tu perfil</p>
                                        <p class="mt-1 text-sm text-slate-650 leading-relaxed">Para poder ver estadísticas e historial de postulaciones, necesitas vincular estudiantes a tu cuenta desde la sección correspondiente.</p>
                                        <div class="mt-3">
                                            <a href="{{ route('tutor.estudiantes.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-650 hover:text-indigo-850">
                                                Ir a Estudiantes
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Cards de Métricas Premium con Elevación y Relieve -->
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                            
                            <article class="group relative overflow-hidden rounded-2xl bg-gradient-to-b from-white to-[#FAFBFD] border border-emerald-100/30 p-5 shadow-[0_12px_30px_rgba(16,185,129,0.06)] transition-all duration-350 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-200/20 hover:border-transparent">
                                <div class="absolute -right-4 -bottom-4 h-16 w-16 rounded-full bg-emerald-50 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Estudiantes</p>
                                    <span class="rounded-xl bg-emerald-50 p-2.5 text-emerald-600 border border-emerald-100/35 shadow-sm">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6"/></svg>
                                    </span>
                                </div>
                                <p class="mt-3 text-3xl font-black text-slate-800 leading-none">{{ (int) ($stats['total_estudiantes'] ?? 0) }}</p>
                                <p class="mt-2 text-xs text-slate-500 font-light">Vinculados a tu perfil</p>
                            </article>

                            <article class="group relative overflow-hidden rounded-2xl bg-gradient-to-b from-white to-[#FAFBFD] border border-indigo-100/30 p-5 shadow-[0_12px_30px_rgba(99,102,241,0.06)] transition-all duration-350 hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-200/20 hover:border-transparent">
                                <div class="absolute -right-4 -bottom-4 h-16 w-16 rounded-full bg-indigo-50 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Postulaciones</p>
                                    <span class="rounded-xl bg-indigo-50 p-2.5 text-indigo-600 border border-indigo-100/35 shadow-sm">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                                    </span>
                                </div>
                                <p class="mt-3 text-3xl font-black text-slate-800 leading-none">{{ $totalPos }}</p>
                                <p class="mt-2 text-xs text-slate-500 font-light">Total de tus tutelados</p>
                            </article>

                            <article class="group relative overflow-hidden rounded-2xl bg-gradient-to-b from-white to-[#FAFBFD] border border-amber-100/30 p-5 shadow-[0_12px_30px_rgba(245,158,11,0.06)] transition-all duration-350 hover:-translate-y-1 hover:shadow-xl hover:shadow-amber-200/20 hover:border-transparent">
                                <div class="absolute -right-4 -bottom-4 h-16 w-16 rounded-full bg-amber-50 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">En evaluación</p>
                                    <span class="rounded-xl bg-amber-50 p-2.5 text-amber-600 border border-amber-100/35 shadow-sm">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </span>
                                </div>
                                <p class="mt-3 text-3xl font-black text-slate-800 leading-none">{{ (int) ($stats['en_evaluacion'] ?? 0) }}</p>
                                <p class="mt-2 text-xs text-slate-500 font-light">Estado actual</p>
                            </article>

                            <article class="group relative overflow-hidden rounded-2xl bg-gradient-to-b from-white to-[#FAFBFD] border border-teal-100/30 p-5 shadow-[0_12px_30px_rgba(20,184,166,0.06)] transition-all duration-350 hover:-translate-y-1 hover:shadow-xl hover:shadow-teal-200/20 hover:border-transparent">
                                <div class="absolute -right-4 -bottom-4 h-16 w-16 rounded-full bg-teal-50 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Aprobadas</p>
                                    <span class="rounded-xl bg-teal-50 p-2.5 text-teal-600 border border-teal-100/35 shadow-sm">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </span>
                                </div>
                                <p class="mt-3 text-3xl font-black text-slate-800 leading-none">{{ (int) ($stats['aprobadas'] ?? 0) }}</p>
                                <p class="mt-2 text-xs text-slate-500 font-light">Vacante asignada</p>
                            </article>

                        </div>

                        <!-- Gráficas y Avance del Tutor -->
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                            
                            <div class="rounded-2xl border border-slate-100/50 bg-gradient-to-b from-white to-[#F9FAFD] p-6 shadow-[0_15px_35px_rgba(148,163,184,0.06),0_1px_2px_rgba(0,0,0,0.005)] lg:col-span-2">
                                <h3 class="text-[17px] font-bold text-slate-800">Postulaciones por estado</h3>
                                <p class="mt-0.5 text-xs text-slate-400 font-light">Avance y estadísticas de admisión en tiempo real.</p>
                                
                                @php $porEstado = $stats['por_estado'] ?? collect(); @endphp
                                @if($porEstado->isEmpty())
                                    <p class="mt-10 text-center text-sm text-slate-400 font-light py-6">Tus estudiantes no poseen postulaciones activeas.</p>
                                @else
                                    <ul class="mt-8 space-y-5">
                                        @foreach($porEstado as $fila)
                                            @php
                                                $n = (int) ($fila['total'] ?? 0);
                                                $pct = $totalPos > 0 ? round($n * 100 / $totalPos) : 0;
                                            @endphp
                                            <li>
                                                <div class="mb-2 flex justify-between text-xs font-semibold text-slate-650">
                                                    <span class="flex items-center gap-2">
                                                        <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                                                        {{ $fila['nombre'] ?? '—' }}
                                                    </span>
                                                    <span>{{ $n }} ({{ $pct }}%)</span>
                                                </div>
                                                <div class="h-3 overflow-hidden rounded-full bg-slate-100 border border-slate-200/50 shadow-inner">
                                                    <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-cyan-450 transition-all duration-500" style="width: {{ $pct }}%"></div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                            <div class="rounded-2xl border border-slate-100/50 bg-gradient-to-b from-white to-[#F9FAFD] p-6 shadow-[0_15px_35px_rgba(148,163,184,0.06),0_1px_2px_rgba(0,0,0,0.005)]">
                                <h3 class="text-[17px] font-bold text-slate-800">Resumen de Seguimiento</h3>
                                <p class="mt-0.5 text-xs text-slate-400 font-light">Filtro consolidado por estados.</p>
                                
                                <ul class="mt-6 space-y-3.5 text-sm text-slate-700">
                                    <li class="flex justify-between items-center border-b border-slate-100 pb-3">
                                        <span class="flex items-center gap-2.5 text-slate-650 font-light">
                                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                            Enviadas
                                        </span>
                                        <strong class="font-bold text-slate-800 bg-slate-100 border border-slate-200/60 px-2 py-0.5 rounded-lg text-xs">{{ (int) ($stats['enviadas'] ?? 0) }}</strong>
                                    </li>
                                    <li class="flex justify-between items-center border-b border-slate-100 pb-3">
                                        <span class="flex items-center gap-2.5 text-slate-650 font-light">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                            Rechazadas
                                        </span>
                                        <strong class="font-bold text-slate-800 bg-slate-100 border border-slate-200/60 px-2 py-0.5 rounded-lg text-xs">{{ (int) ($stats['rechazadas'] ?? 0) }}</strong>
                                    </li>
                                    <li class="flex justify-between items-center border-b border-slate-100 pb-3">
                                        <span class="flex items-center gap-2.5 text-slate-650 font-light">
                                            <span class="h-1.5 w-1.5 rounded-full bg-violet-500"></span>
                                            Con resultado (ranking)
                                        </span>
                                        <strong class="font-bold text-slate-800 bg-slate-100 border border-slate-200/60 px-2 py-0.5 rounded-lg text-xs">{{ (int) ($stats['con_resultado'] ?? 0) }}</strong>
                                    </li>
                                    <li class="flex justify-between items-center">
                                        <span class="flex items-center gap-2.5 text-slate-650 font-light">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            En lista de espera
                                        </span>
                                        <strong class="font-bold text-slate-800 bg-slate-100 border border-slate-200/60 px-2 py-0.5 rounded-lg text-xs">{{ (int) ($stats['en_lista_espera'] ?? 0) }}</strong>
                                    </li>
                                </ul>
                            </div>

                        </div>

                        <!-- Estudiantes Vinculados con Relieve -->
                        <div class="rounded-2xl border border-slate-200 bg-gradient-to-b from-white to-[#F9FAFD] p-6 shadow-[0_8px_30px_rgba(15,23,42,0.02),0_1px_2px_rgba(0,0,0,0.015)]">
                            <h3 class="text-[17px] font-bold text-slate-800">Estudiantes vinculados</h3>
                            <p class="mt-0.5 text-xs text-slate-400 font-light mb-4">Padrón de estudiantes registrados bajo tu tutela.</p>
                            
                            <div class="flex flex-wrap gap-2.5">
                                @foreach($td['estudiantes'] as $est)
                                    @php
                                        $nom = trim(($est->persona->nombres_per ?? '').' '.($est->persona->ap_paterno_per ?? '').' '.($est->persona->ap_materno_per ?? ''));
                                    @endphp
                                    <span class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-b from-white to-indigo-50/20 border border-slate-200 px-4 py-2 text-xs font-medium text-indigo-700 shadow-sm transition-all hover:border-indigo-300 hover:bg-indigo-50/30">
                                        <span class="h-2 w-2 rounded-full bg-indigo-400"></span>
                                        <span class="font-bold text-slate-800">{{ $nom ?: ('ID '.$est->id_est) }}</span>
                                        @if($est->codigo_est)
                                            <span class="text-indigo-450 border-l border-slate-200 pl-2.5 font-mono text-[10px]">{{ $est->codigo_est }}</span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Postulaciones Recientes con Relieve Inset -->
                        <div class="rounded-2xl border border-slate-200 bg-gradient-to-b from-white to-[#F9FAFD] p-6 shadow-[0_8px_30px_rgba(15,23,42,0.02),0_1px_2px_rgba(0,0,0,0.015)]">
                            <div class="mb-5 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h3 class="text-[17px] font-bold text-slate-800">Postulaciones recientes</h3>
                                    <p class="text-xs text-slate-400 font-light">Listado histórico rápido de postulaciones.</p>
                                </div>
                                <a href="{{ route('tutor.postulaciones.index') }}" class="text-xs font-bold text-indigo-650 hover:underline">Ver historial completo →</a>
                            </div>

                            @php $recent = $td['recent'] ?? collect(); @endphp
                            @if($recent->isEmpty())
                                <div class="py-12 text-center text-slate-450 font-light text-sm">
                                    Aún no has creado postulaciones para tus estudiantes.
                                </div>
                            @else
                                <div class="overflow-x-auto rounded-xl border border-slate-200 bg-slate-50/60 shadow-inner">
                                    <table class="min-w-full text-left text-sm">
                                        <thead>
                                            <tr class="border-b border-slate-200 bg-slate-100/80 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                                <th class="px-5 py-3.5">Estudiante</th>
                                                <th class="px-5 py-3.5">Oferta Académica</th>
                                                <th class="px-5 py-3.5">Colegio / Unidad</th>
                                                <th class="px-5 py-3.5">Estado</th>
                                                <th class="px-5 py-3.5">Fecha</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-200 bg-white">
                                            @foreach($recent as $pos)
                                                @php
                                                    $oac = $pos->ofertaAcademica;
                                                    $estNom = trim(($pos->estudiante->persona->nombres_per ?? '').' '.($pos->estudiante->persona->ap_paterno_per ?? ''));
                                                    $ofertaTxt = $oac
                                                        ? trim(implode(' · ', array_filter([
                                                            $oac->gestion->nombre_ges ?? null,
                                                            $oac->nivel->nombre_niv ?? null,
                                                            $oac->curso->nombre_cur ?? null,
                                                            $oac->paralelo->nombre_par ?? null,
                                                        ])))
                                                        : '';
                                                    $estado = $pos->estadoPostulacion->nombre_ept ?? '—';
                                                @endphp
                                                <tr class="text-slate-650 transition-colors hover:bg-slate-50/40">
                                                    <td class="px-5 py-3.5 font-bold text-slate-800">{{ $estNom ?: '—' }}</td>
                                                    <td class="px-5 py-3.5">{{ $ofertaTxt ?: ($oac?->descripcion_oac ?? '—') }}</td>
                                                    <td class="px-5 py-3.5 text-slate-500 font-light">{{ $oac?->unidadEducativa?->nombre_ued ?? '—' }}</td>
                                                    <td class="px-5 py-3.5">
                                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-xs font-semibold
                                                            @if(strtolower($estado) === 'aprobada') bg-emerald-50 border-emerald-200/50 text-emerald-700
                                                            @elseif(strtolower($estado) === 'rechazada') bg-rose-50 border-rose-200/50 text-rose-700
                                                            @elseif(strtolower($estado) === 'en_evaluacion') bg-amber-50 border-amber-200/50 text-amber-700
                                                            @else bg-slate-50 border-slate-200/50 text-slate-600 @endif">
                                                            <span class="h-1.5 w-1.5 rounded-full 
                                                                @if(strtolower($estado) === 'aprobada') bg-emerald-500
                                                                @elseif(strtolower($estado) === 'rechazada') bg-rose-500
                                                                @elseif(strtolower($estado) === 'en_evaluacion') bg-amber-500
                                                                @else bg-slate-400 @endif"></span>
                                                            {{ $estado }}
                                                        </span>
                                                    </td>
                                                    <td class="px-5 py-3.5 whitespace-nowrap text-xs text-slate-450">{{ optional($pos->fecha_pos)->format('d/m/Y H:i') ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- Ayuda y Enlaces API -->
                        <div class="rounded-xl bg-[#F0F2FA] border border-slate-250 p-4 flex items-center justify-between gap-4">
                            <p class="text-xs text-slate-500 font-light">
                                ¿Quieres programar automatizaciones? Utiliza la API REST bajo el prefijo <code class="rounded bg-slate-200 border border-slate-300 px-1.5 py-0.5 text-[10px] font-mono">/api/v1/postulaciones</code> autenticado con tokens Sanctum.
                            </p>
                            <a href="{{ route('tutor.documentos.index') }}" class="text-xs font-bold text-indigo-650 shrink-0 hover:underline">Subir archivos obligatorios</a>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Datos de Usuario en tarjetas Minimalistas con Relieve -->
            <div class="mt-10 border-t border-slate-200/60 pt-8">
                <h3 class="text-sm font-bold text-slate-450 uppercase tracking-wider mb-4">Información de Cuenta</h3>
                
                <div class="grid gap-4 sm:grid-cols-3">
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-[0_4px_12px_rgba(15,23,42,0.01)] flex gap-3.5 items-center">
                        <span class="rounded-xl bg-indigo-50 p-2 text-indigo-600 border border-indigo-100/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                        <div class="min-w-0">
                            <strong class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Usuario</strong>
                            <span class="mt-0.5 block text-sm font-semibold text-slate-800 truncate">
                                {{ trim(($usuario->persona->nombres_per ?? '').' '.($usuario->persona->ap_paterno_per ?? '').' '.($usuario->persona->ap_materno_per ?? '')) ?: $usuario->correo_usu }}
                            </span>
                        </div>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-[0_4px_12px_rgba(15,23,42,0.01)] flex gap-3.5 items-center">
                        <span class="rounded-xl bg-violet-50 p-2 text-violet-600 border border-violet-100/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                        </span>
                        <div class="min-w-0">
                            <strong class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Rol asignado</strong>
                            <span class="mt-0.5 block text-sm font-semibold text-slate-800">{{ \App\Support\Roles::label($usuario->rol->nombre_rol ?? null) }}</span>
                        </div>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-[0_4px_12px_rgba(15,23,42,0.01)] flex gap-3.5 items-center">
                        <span class="rounded-xl bg-cyan-50 p-2 text-cyan-600 border border-cyan-100/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </span>
                        <div class="min-w-0">
                            <strong class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Correo Electrónico</strong>
                            <span class="mt-0.5 block text-sm font-semibold text-slate-850 truncate">{{ $usuario->correo_usu }}</span>
                        </div>
                    </article>
                </div>
            </div>

            <!-- Botones de Acción de Cuenta -->
            <div class="mt-8 flex justify-end">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-rose-50 border border-rose-200 px-5 py-2.5 text-xs font-bold text-rose-600 transition-all duration-300 hover:bg-rose-100 hover:text-rose-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H9m-4 8h8a2 2 0 002-2V6a2 2 0 00-2-2H5"/></svg>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </section>
    </div>
@endsection
