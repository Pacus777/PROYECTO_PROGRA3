@extends('layouts.dashboard')

@section('title', 'Panel institucional | AdmisiónEscolar')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="font-medium text-slate-500">Dashboard institucional</span>
@endsection

@section('content')
    @php
        $nombreUsuario = trim(($dashboardUser->persona->nombres_per ?? '').' '.($dashboardUser->persona->ap_paterno_per ?? '')) ?: 'Administrador';
        $totalPostulaciones = (int) ($stats['postulaciones'] ?? 0);
        $ofertasActivas = (int) ($stats['ofertas'] ?? 0);
        $totalCupos = (int) ($stats['cupos'] ?? 0);
        $totalAprobados = (int) ($stats['aprobados'] ?? 0);
        $listaEspera = max($totalPostulaciones - $totalAprobados, 0);

        $pendientePct = 35;
        $evaluacionPct = 28;
        $aprobadaPct = 24;
        $rechazadaPct = 13;
    @endphp

    <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500">
        <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 right-32 h-32 w-32 rounded-full bg-white/5"></div>
        <div class="relative z-10 flex flex-col justify-between gap-6 p-8 lg:flex-row lg:items-center">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">Panel Institucional</p>
                <h1 class="mt-3 text-3xl font-bold text-white">Bienvenido, {{ $nombreUsuario }}</h1>
                <p class="mt-2 text-sm text-white/80">Aquí tienes el resumen de tu institución hoy.</p>
                <a href="{{ route('admin.institucional.postulaciones.index') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-white px-6 py-2.5 text-sm font-semibold text-indigo-700 shadow-lg transition hover:bg-indigo-50">
                    Ver postulaciones
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="hidden lg:block">
                <svg class="h-40 w-40 text-white/85" viewBox="0 0 200 200" fill="none">
                    <ellipse cx="100" cy="168" rx="52" ry="12" fill="rgba(255,255,255,.15)"/>
                    <path d="M38 86L100 58l62 28-62 29-62-29z" fill="rgba(255,255,255,.95)"/>
                    <path d="M58 98v29c0 17 21 30 42 30s42-13 42-30V98" stroke="rgba(255,255,255,.95)" stroke-width="8" stroke-linecap="round"/>
                    <circle cx="162" cy="86" r="8" fill="rgba(255,255,255,.95)"/>
                    <path d="M162 94v25" stroke="rgba(255,255,255,.95)" stroke-width="6" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <article class="cursor-pointer rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 p-6 text-white shadow-lg transition-transform duration-300 hover:scale-105">
            <div class="mb-5 flex items-start justify-between">
                <div class="rounded-xl bg-white/20 p-3">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                </div>
            </div>
            <p class="text-4xl font-black">{{ $totalPostulaciones }}</p>
            <p class="mt-1 text-sm text-indigo-100">Postulaciones</p>
            <p class="mt-1 text-xs text-indigo-200">+12% este mes</p>
        </article>

        <article class="cursor-pointer rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-500 p-6 text-white shadow-lg transition-transform duration-300 hover:scale-105">
            <div class="mb-5 rounded-xl bg-white/20 p-3 w-fit">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h7v7H3V7zm11 0h7v7h-7V7zM3 17h7v4H3v-4zm11 0h7v4h-7v-4z"/></svg>
            </div>
            <p class="text-4xl font-black">{{ $totalCupos }}</p>
            <p class="mt-1 text-sm text-cyan-100">Cupos disponibles</p>
            <p class="mt-1 text-xs text-cyan-200">{{ $ofertasActivas }} ofertas activas</p>
        </article>

        <article class="cursor-pointer rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 p-6 text-white shadow-lg transition-transform duration-300 hover:scale-105">
            <div class="mb-5 rounded-xl bg-white/20 p-3 w-fit">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-4xl font-black">{{ $totalAprobados }}</p>
            <p class="mt-1 text-sm text-emerald-100">Aprobados</p>
            <p class="mt-1 text-xs text-emerald-200">Resultado final consolidado</p>
        </article>

        <article class="cursor-pointer rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 p-6 text-white shadow-lg transition-transform duration-300 hover:scale-105">
            <div class="mb-5 rounded-xl bg-white/20 p-3 w-fit">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-4xl font-black">{{ $listaEspera }}</p>
            <p class="mt-1 text-sm text-amber-100">En lista de espera</p>
            <p class="mt-1 text-xs text-amber-200">Seguimiento pendiente</p>
        </article>
    </div>

    <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <section class="rounded-2xl bg-white p-6 shadow-sm lg:col-span-2">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-800">Postulaciones por Estado</h2>
                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-600">Este mes</span>
            </div>

            <div class="space-y-4">
                @foreach([
                    ['Pendiente', $pendientePct, 'from-amber-400 to-amber-300'],
                    ['En evaluación', $evaluacionPct, 'from-blue-500 to-cyan-400'],
                    ['Aprobada', $aprobadaPct, 'from-emerald-500 to-teal-400'],
                    ['Rechazada', $rechazadaPct, 'from-red-400 to-pink-400'],
                ] as [$label, $pct, $gradient])
                    <div>
                        <div class="mb-1 flex items-center justify-between text-xs">
                            <span class="font-medium text-slate-600">{{ $label }}</span>
                            <span class="font-semibold text-slate-500">{{ $pct }}%</span>
                        </div>
                        <div class="h-3 rounded-full bg-slate-100">
                            <div class="h-3 rounded-full bg-gradient-to-r {{ $gradient }}" style="--bar-w: {{ $pct }}%; width: {{ $pct }}%; animation: barGrow .8s ease-out;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-800">Acciones rápidas</h2>
            <div class="space-y-3">
                <a href="{{ route('admin.institucional.ofertas.index') }}" class="flex items-center gap-4 rounded-xl border border-slate-100 p-4 transition hover:border-indigo-200 hover:bg-indigo-50">
                    <span class="rounded-lg bg-indigo-100 p-2 text-indigo-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7L12 3 4 7l8 4 8-4zM4 12l8 4 8-4"/></svg></span>
                    <span class="text-sm font-medium text-slate-700">Nueva oferta</span>
                </a>
                <a href="{{ route('admin.institucional.postulaciones.index') }}" class="flex items-center gap-4 rounded-xl border border-slate-100 p-4 transition hover:border-indigo-200 hover:bg-indigo-50">
                    <span class="rounded-lg bg-blue-100 p-2 text-blue-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg></span>
                    <span class="text-sm font-medium text-slate-700">Ver postulaciones</span>
                </a>
                <a href="{{ route('admin.institucional.criterios.index') }}" class="flex items-center gap-4 rounded-xl border border-slate-100 p-4 transition hover:border-indigo-200 hover:bg-indigo-50">
                    <span class="rounded-lg bg-violet-100 p-2 text-violet-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5h11M9 12h11M9 19h11M4 6h.01M4 13h.01M4 20h.01"/></svg></span>
                    <span class="text-sm font-medium text-slate-700">Iniciar evaluación</span>
                </a>
                <a href="{{ route('admin.institucional.resultados.index') }}" class="flex items-center gap-4 rounded-xl border border-slate-100 p-4 transition hover:border-indigo-200 hover:bg-indigo-50">
                    <span class="rounded-lg bg-amber-100 p-2 text-amber-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8M12 17v4M7 4h10v5a5 5 0 01-10 0V4z"/></svg></span>
                    <span class="text-sm font-medium text-slate-700">Ver resultados</span>
                </a>
            </div>
        </section>
    </div>

    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-800">Postulaciones recientes</h2>
            <a href="{{ route('admin.institucional.postulaciones.index') }}" class="text-sm font-medium text-indigo-600 hover:underline">Ver todas →</a>
        </div>
        <div class="py-16 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-indigo-500">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M5 7v11a2 2 0 002 2h10a2 2 0 002-2V7M9 11h6"/>
                </svg>
            </div>
            <p class="mt-4 font-semibold text-slate-700">Vista rápida disponible en el módulo de postulaciones</p>
            <p class="mt-2 text-sm text-slate-400">Abre el listado completo para revisar filtros, estado y puntajes.</p>
        </div>
    </section>
@endsection

