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

    <x-institucional.page
        module="dashboard"
        eyebrow="Panel Institucional"
        :title="'Bienvenido, '.$nombreUsuario"
        subtitle="Aquí tienes el resumen de tu institución hoy."
    >
        <x-slot:actions>
            <a href="{{ route('admin.institucional.postulaciones.index') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-indigo-700 shadow-lg transition hover:bg-indigo-50">
                Ver postulaciones
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
            <a href="{{ route('admin.institucional.ofertas.index') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-white/20 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/30">
                Nueva oferta
            </a>
        </x-slot:actions>

        <div class="mb-8 grid grid-cols-2 gap-6 xl:grid-cols-4">
            <a href="{{ route('admin.institucional.postulaciones.index') }}"
               class="group cursor-pointer rounded-2xl bg-gradient-to-br from-indigo-650 to-indigo-800 p-6 text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-indigo-500/10">
                <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 border border-white/5 transition-all group-hover:bg-white/20">
                    <svg class="h-5 w-5 text-indigo-100" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                </div>
                <p class="text-4xl font-black tracking-tight">{{ $totalPostulaciones }}</p>
                <p class="mt-1.5 text-xs font-bold uppercase tracking-wider text-indigo-200">Postulaciones</p>
            </a>

            <a href="{{ route('admin.institucional.ofertas.index') }}"
               class="group cursor-pointer rounded-2xl bg-gradient-to-br from-cyan-600 to-blue-700 p-6 text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-blue-500/10">
                <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 border border-white/5 transition-all group-hover:bg-white/20">
                    <svg class="h-5 w-5 text-cyan-100" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7L12 3 4 7l8 4 8-4zM4 12l8 4 8-4M4 17l8 4 8-4"/></svg>
                </div>
                <p class="text-4xl font-black tracking-tight">{{ $cuposDisponibles }}</p>
                <p class="mt-1.5 text-xs font-bold uppercase tracking-wider text-cyan-200">Cupos disponibles</p>
            </a>

            <a href="{{ route('admin.institucional.resultados.index') }}"
               class="group cursor-pointer rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-650 p-6 text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-emerald-500/10">
                <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 border border-white/5 transition-all group-hover:bg-white/20">
                    <svg class="h-5 w-5 text-emerald-100" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-4xl font-black tracking-tight">{{ $totalAprobados }}</p>
                <p class="mt-1.5 text-xs font-bold uppercase tracking-wider text-emerald-200">Cupos asignados</p>
            </a>

            <a href="{{ route('admin.institucional.criterios.index') }}"
               class="group cursor-pointer rounded-2xl bg-gradient-to-br from-amber-500 to-orange-655 p-6 text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-orange-500/10">
                <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 border border-white/5 transition-all group-hover:bg-white/20">
                    <svg class="h-5 w-5 text-amber-100" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5h11M9 12h11M9 19h11M4 6h.01M4 13h.01M4 20h.01"/></svg>
                </div>
                <p class="text-4xl font-black tracking-tight">{{ $ofertasActivas }}</p>
                <p class="mt-1.5 text-xs font-bold uppercase tracking-wider text-amber-250">Ofertas activas</p>
            </a>
        </div>

        @include('admin.institucional.dashboard.charts', ['institucionalDashboard' => $institucionalDashboard])

        <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <x-institucional.panel module="dashboard" title="Postulaciones por estado" class="lg:col-span-2">
                <div class="p-6">
                    <div class="mb-6 flex items-center justify-end">
                        <a href="{{ route('admin.institucional.postulaciones.index') }}"
                           class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-100">
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
                </div>
            </x-institucional.panel>

            <x-institucional.panel module="dashboard" title="Acciones rápidas">
                <div class="space-y-3.5 p-6">
                    <a href="{{ route('admin.institucional.ofertas.index') }}"
                       class="flex items-center gap-4 rounded-xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-250 p-4 shadow-[0_3px_10px_rgba(15,23,42,0.015)] transition-all duration-300 hover:border-indigo-350 hover:bg-indigo-50/40 hover:shadow-md">
                        <span class="rounded-lg bg-indigo-50 p-2 text-indigo-600 border border-indigo-100/30 shadow-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7L12 3 4 7l8 4 8-4zM4 12l8 4 8-4"/></svg>
                        </span>
                        <span class="text-sm font-bold text-slate-800">Nueva oferta</span>
                    </a>
                    <a href="{{ route('admin.institucional.postulaciones.index') }}"
                       class="flex items-center gap-4 rounded-xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-250 p-4 shadow-[0_3px_10px_rgba(15,23,42,0.015)] transition-all duration-300 hover:border-blue-350 hover:bg-blue-50/40 hover:shadow-md">
                        <span class="rounded-lg bg-blue-50 p-2 text-blue-600 border border-blue-100/30 shadow-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                        </span>
                        <span class="text-sm font-bold text-slate-800">Ver postulaciones</span>
                    </a>
                    <a href="{{ route('admin.institucional.criterios.index') }}"
                       class="flex items-center gap-4 rounded-xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-250 p-4 shadow-[0_3px_10px_rgba(15,23,42,0.015)] transition-all duration-300 hover:border-violet-350 hover:bg-violet-50/40 hover:shadow-md">
                        <span class="rounded-lg bg-violet-50 p-2 text-violet-600 border border-violet-100/30 shadow-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5h11M9 12h11M9 19h11M4 6h.01M4 13h.01M4 20h.01"/></svg>
                        </span>
                        <span class="text-sm font-bold text-slate-800">Iniciar evaluación</span>
                    </a>
                    <a href="{{ route('admin.institucional.resultados.index') }}"
                       class="flex items-center gap-4 rounded-xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-250 p-4 shadow-[0_3px_10px_rgba(15,23,42,0.015)] transition-all duration-300 hover:border-amber-350 hover:bg-amber-50/40 hover:shadow-md">
                        <span class="rounded-lg bg-amber-50 p-2 text-amber-600 border border-amber-100/30 shadow-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8M12 17v4M7 4h10v5a5 5 0 01-10 0V4z"/></svg>
                        </span>
                        <span class="text-sm font-bold text-slate-800">Ver resultados</span>
                    </a>
                    <a href="{{ route('admin.institucional.academic.index') }}"
                       class="flex items-center gap-4 rounded-xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-250 p-4 shadow-[0_3px_10px_rgba(15,23,42,0.015)] transition-all duration-300 hover:border-teal-350 hover:bg-teal-50/40 hover:shadow-md">
                        <span class="rounded-lg bg-teal-50 p-2 text-teal-600 border border-teal-100/30 shadow-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6"/></svg>
                        </span>
                        <span class="text-sm font-bold text-slate-800">Gestión académica</span>
                    </a>
                </div>
            </x-institucional.panel>
        </div>

        <x-institucional.panel module="dashboard" title="Postulaciones recientes">
            <div class="p-6">
                <div class="mb-5 flex items-center justify-end">
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
                                    <tr class="text-slate-650 transition-colors hover:bg-slate-50/40">
                                        <td class="px-4 py-3.5 font-bold text-slate-850">{{ $nom ?: '—' }}</td>
                                        <td class="px-4 py-3.5 text-slate-500 font-light">{{ $ofertaTxt }}</td>
                                        <td class="px-4 py-3.5">
                                            @php
                                                $estado = $pos->estadoPostulacion->nombre_ept ?? '—';
                                            @endphp
                                            <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-semibold
                                                @if(strtolower($estado) === 'aprobada') bg-emerald-50 border-emerald-200/50 text-emerald-705
                                                @elseif(strtolower($estado) === 'rechazada') bg-rose-50 border-rose-200/50 text-rose-705
                                                @elseif(strtolower($estado) === 'en_evaluacion') bg-amber-50 border-amber-200/50 text-amber-705
                                                @else bg-slate-50 border-slate-200/50 text-slate-600 @endif">
                                                <span class="h-1.5 w-1.5 rounded-full 
                                                    @if(strtolower($estado) === 'aprobada') bg-emerald-500
                                                    @elseif(strtolower($estado) === 'rechazada') bg-rose-500
                                                    @elseif(strtolower($estado) === 'en_evaluacion') bg-amber-500
                                                    @else bg-slate-400 @endif"></span>
                                                {{ $estado }}
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
            </div>
        </x-institucional.panel>
    </x-institucional.page>
@endsection
