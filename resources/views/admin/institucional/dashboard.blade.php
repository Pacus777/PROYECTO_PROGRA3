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
        $nombreUsuario = trim(($dashboardUser->persona->nombres_per ?? '').' '.($dashboardUser->persona->ap_paterno_per ?? '')) ?: 'Personal del colegio';
        $totalPostulaciones = (int) ($stats['postulaciones'] ?? 0);
        $ofertasActivas = (int) ($stats['ofertas'] ?? 0);
        $cuposDisponibles = (int) ($stats['cupos'] ?? 0);
        $totalAprobados = (int) ($stats['aprobados'] ?? 0);
    @endphp

    {{-- Encabezado --}}
    <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500">
        <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 right-32 h-32 w-32 rounded-full bg-white/5"></div>
        <div class="relative z-10 flex flex-col justify-between gap-6 p-8 lg:flex-row lg:items-center">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">Panel Institucional</p>
                <h1 class="mt-3 text-3xl font-bold text-white">Bienvenido, {{ $nombreUsuario }}</h1>
                <p class="mt-2 text-sm text-white/80">Aquí tienes el resumen de tu institución hoy.</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('admin.institucional.postulaciones.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-indigo-700 shadow-lg transition hover:bg-indigo-50">
                        Ver postulaciones
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('admin.institucional.ofertas.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-white/20 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/30">
                        Nueva oferta
                    </a>
                </div>
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

    {{-- Tarjetas de stats reales --}}
    <div class="mb-8 grid grid-cols-2 gap-5 xl:grid-cols-4">
        <a href="{{ route('admin.institucional.postulaciones.index') }}"
           class="group cursor-pointer rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 p-6 text-white shadow-lg transition-transform duration-200 hover:scale-105">
            <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
            </div>
            <p class="text-4xl font-black">{{ $totalPostulaciones }}</p>
            <p class="mt-1 text-sm text-indigo-100">Postulaciones</p>
        </a>

        <a href="{{ route('admin.institucional.ofertas.index') }}"
           class="group cursor-pointer rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-500 p-6 text-white shadow-lg transition-transform duration-200 hover:scale-105">
            <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7L12 3 4 7l8 4 8-4zM4 12l8 4 8-4M4 17l8 4 8-4"/></svg>
            </div>
            <p class="text-4xl font-black">{{ $cuposDisponibles }}</p>
            <p class="mt-1 text-sm text-cyan-100">Cupos disponibles</p>
            <p class="mt-0.5 text-xs text-cyan-200">{{ $ofertasActivas }} ofertas</p>
        </a>

        <a href="{{ route('admin.institucional.resultados.index') }}"
           class="group cursor-pointer rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 p-6 text-white shadow-lg transition-transform duration-200 hover:scale-105">
            <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-4xl font-black">{{ $totalAprobados }}</p>
            <p class="mt-1 text-sm text-emerald-100">Aprobados</p>
        </a>

        <a href="{{ route('admin.institucional.criterios.index') }}"
           class="group cursor-pointer rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 p-6 text-white shadow-lg transition-transform duration-200 hover:scale-105">
            <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5h11M9 12h11M9 19h11M4 6h.01M4 13h.01M4 20h.01"/></svg>
            </div>
            <p class="text-4xl font-black">{{ $ofertasActivas }}</p>
            <p class="mt-1 text-sm text-amber-100">Ofertas activas</p>
        </a>
    </div>

    <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Barra de estados real --}}
        <section class="rounded-2xl bg-white p-6 shadow-sm lg:col-span-2">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-800">Postulaciones por estado</h2>
                <a href="{{ route('admin.institucional.postulaciones.index') }}"
                   class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-600 hover:bg-indigo-100 transition">
                    Ver todas →
                </a>
            </div>
            @if($porEstado->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                    </div>
                    <p class="mt-3 text-sm font-medium text-slate-500">Sin postulaciones registradas aún.</p>
                </div>
            @else
                @php
                    $colors = ['from-amber-400 to-amber-300','from-blue-500 to-cyan-400','from-emerald-500 to-teal-400','from-red-400 to-pink-400','from-violet-500 to-purple-400','from-slate-400 to-slate-300'];
                @endphp
                <div class="space-y-4">
                    @foreach($porEstado as $i => $fila)
                        @php
                            $pct = $totalPostulaciones > 0 ? round($fila['total'] * 100 / $totalPostulaciones) : 0;
                            $color = $colors[$i % count($colors)];
                        @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-xs">
                                <span class="font-medium text-slate-600">{{ $fila['nombre'] }}</span>
                                <span class="font-semibold text-slate-500">{{ $fila['total'] }} ({{ $pct }}%)</span>
                            </div>
                            <div class="h-3 rounded-full bg-slate-100">
                                <div class="h-3 rounded-full bg-gradient-to-r {{ $color }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Acciones rápidas --}}
        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-800">Acciones rápidas</h2>
            <div class="space-y-3">
                <a href="{{ route('admin.institucional.ofertas.index') }}"
                   class="flex items-center gap-4 rounded-xl border border-slate-100 p-4 transition hover:border-indigo-200 hover:bg-indigo-50">
                    <span class="rounded-lg bg-indigo-100 p-2 text-indigo-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7L12 3 4 7l8 4 8-4zM4 12l8 4 8-4"/></svg>
                    </span>
                    <span class="text-sm font-medium text-slate-700">Nueva oferta</span>
                </a>
                <a href="{{ route('admin.institucional.postulaciones.index') }}"
                   class="flex items-center gap-4 rounded-xl border border-slate-100 p-4 transition hover:border-indigo-200 hover:bg-indigo-50">
                    <span class="rounded-lg bg-blue-100 p-2 text-blue-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                    </span>
                    <span class="text-sm font-medium text-slate-700">Ver postulaciones</span>
                </a>
                <a href="{{ route('admin.institucional.criterios.index') }}"
                   class="flex items-center gap-4 rounded-xl border border-slate-100 p-4 transition hover:border-indigo-200 hover:bg-indigo-50">
                    <span class="rounded-lg bg-violet-100 p-2 text-violet-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5h11M9 12h11M9 19h11M4 6h.01M4 13h.01M4 20h.01"/></svg>
                    </span>
                    <span class="text-sm font-medium text-slate-700">Iniciar evaluación</span>
                </a>
                <a href="{{ route('admin.institucional.resultados.index') }}"
                   class="flex items-center gap-4 rounded-xl border border-slate-100 p-4 transition hover:border-indigo-200 hover:bg-indigo-50">
                    <span class="rounded-lg bg-amber-100 p-2 text-amber-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8M12 17v4M7 4h10v5a5 5 0 01-10 0V4z"/></svg>
                    </span>
                    <span class="text-sm font-medium text-slate-700">Ver resultados</span>
                </a>
                <a href="{{ route('admin.institucional.academic.index') }}"
                   class="flex items-center gap-4 rounded-xl border border-slate-100 p-4 transition hover:border-indigo-200 hover:bg-indigo-50">
                    <span class="rounded-lg bg-teal-100 p-2 text-teal-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6"/></svg>
                    </span>
                    <span class="text-sm font-medium text-slate-700">Gestión académica</span>
                </a>
            </div>
        </section>
    </div>

    {{-- Postulaciones recientes reales --}}
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-800">Postulaciones recientes</h2>
            <a href="{{ route('admin.institucional.postulaciones.index') }}"
               class="text-sm font-medium text-indigo-600 hover:underline">Ver todas →</a>
        </div>
        @if($recientes->isEmpty())
            <div class="flex flex-col items-center justify-center py-14 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-indigo-500">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                </div>
                <p class="mt-4 font-semibold text-slate-700">Sin postulaciones aún</p>
                <p class="mt-2 text-sm text-slate-400">Aparecerán aquí en cuanto los tutores registren postulaciones.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Estudiante</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Oferta</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Estado</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-400"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recientes as $pos)
                            @php
                                $nom = trim(($pos->estudiante->persona->nombres_per ?? '').' '.($pos->estudiante->persona->ap_paterno_per ?? ''));
                                $oac = $pos->ofertaAcademica;
                                $ofertaTxt = $oac
                                    ? implode(' · ', array_filter([$oac->curso->nombre_cur ?? null, $oac->paralelo->nombre_par ?? null]))
                                    : '—';
                            @endphp
                            <tr class="text-slate-700 transition hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $nom ?: '—' }}</td>
                                <td class="px-4 py-3">{{ $ofertaTxt }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                        {{ $pos->estadoPostulacion->nombre_ept ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.institucional.postulaciones.show', $pos) }}"
                                       class="font-semibold text-indigo-600 hover:underline">Ver →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
