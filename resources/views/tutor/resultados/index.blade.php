@extends('layouts.dashboard')

@section('title', 'Tutor | Resultados')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Resultados</span>
@endsection

@section('content')
    @php
        $totalAsignados = $postulaciones->filter(fn($p) => $p->asignaciones->isNotEmpty())->count();
        $totalEspera = $postulaciones->filter(fn($p) => $p->listasEspera->isNotEmpty())->count();
        $evaluadas = $postulaciones->filter(fn($p) => $p->resultado && isset($p->resultado->puntaje_total_res));
        $promedioPuntaje = $evaluadas->count() > 0 ? number_format($evaluadas->avg('resultado.puntaje_total_res'), 1) : '—';
    @endphp

    {{-- Cabecera Premium --}}
    <div class="mb-8 animate-fadeInUp">
        <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Tutor / Cierre de Gestión</p>
        <h1 class="text-3xl font-black tracking-tight text-slate-900 mt-1">Resultados y Asignaciones</h1>
        <p class="mt-1.5 text-xs text-slate-450 font-light">Consulta las asignaciones finales de cupo, puntajes de postulación y estados en las listas de espera oficiales.</p>
    </div>

    {{-- Tarjetas KPI de Resumen Táctil --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-3 animate-fadeInUp delay-75">
        <div class="rounded-2xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-4 shadow-[0_4px_20px_rgba(15,23,42,0.015)] flex items-center gap-3.5">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-650 border border-emerald-100/30">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Cupos Asignados</span>
                <span class="text-lg font-black text-slate-800">{{ $totalAsignados }}</span>
            </div>
        </div>
        <div class="rounded-2xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-4 shadow-[0_4px_20px_rgba(15,23,42,0.015)] flex items-center gap-3.5">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-650 border border-amber-100/30">
                <svg class="h-5 w-5 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">En Lista de Espera</span>
                <span class="text-lg font-black text-slate-800">{{ $totalEspera }}</span>
            </div>
        </div>
        <div class="rounded-2xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-4 shadow-[0_4px_20px_rgba(15,23,42,0.015)] flex items-center gap-3.5">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-teal-50 text-teal-650 border border-teal-100/30">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Puntaje Promedio</span>
                <span class="text-lg font-black text-slate-800">{{ $promedioPuntaje }} <span class="text-xs text-slate-400 font-light">pts</span></span>
            </div>
        </div>
    </div>

    {{-- Listado de Resultados en Relieve 3D --}}
    <div class="rounded-3xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-5 shadow-[0_12px_36px_rgba(15,23,42,0.03),0_1px_3px_rgba(0,0,0,0.015)] animate-fadeInUp delay-100">
        @if($postulaciones->count() === 0)
            <div class="py-12 flex flex-col items-center justify-center text-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-4 border border-slate-200/50">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm font-bold text-slate-800">No hay postulaciones registradas</p>
                <p class="text-xs text-slate-400 font-light mt-1">Los puntajes y asignaciones se publican de acuerdo al calendario de cierres oficial.</p>
            </div>
        @else
            {{-- Contenedor de Inset (Bajorrelieve) --}}
            <div class="shadow-inner bg-slate-50/50 rounded-2xl border border-slate-250/50 p-2">
                <div class="overflow-x-auto rounded-xl">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200/60 bg-[#F8FAFC]/90">
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">ID</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">Estudiante</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">Oferta Académica</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">Estado</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">Puntaje</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">Asignación de Cupo</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">Lista Espera</th>
                                <th class="px-5 py-4 text-right text-[10px] font-bold uppercase tracking-wider text-slate-450">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($postulaciones as $pos)
                                @php
                                    $nom = trim(($pos->estudiante->persona->nombres_per ?? '').' '.($pos->estudiante->persona->ap_paterno_per ?? ''));
                                    $oac = $pos->ofertaAcademica;
                                    $ofertaTxt = $oac ? trim(implode(' · ', array_filter([$oac->nivel->nombre_niv ?? null, $oac->curso->nombre_cur ?? null, $oac->paralelo->nombre_par ?? null]))) : '—';
                                    $asi = $pos->asignaciones->first();
                                    $les = $pos->listasEspera->first();
                                    
                                    // Mapeo colores de estado
                                    $estadoNombre = strtolower($pos->estadoPostulacion->nombre_ept ?? '');
                                    $estadoColor = match(true) {
                                        str_contains($estadoNombre, 'acept') || str_contains($estadoNombre, 'asign') || str_contains($estadoNombre, 'complet') => 'bg-emerald-50 text-emerald-700 border-emerald-100/50',
                                        str_contains($estadoNombre, 'revis') || str_contains($estadoNombre, 'espera') => 'bg-amber-50 text-amber-700 border-amber-100/50',
                                        str_contains($estadoNombre, 'rechaz') || str_contains($estadoNombre, 'cancel') => 'bg-rose-50 text-rose-700 border-rose-100/50',
                                        default => 'bg-indigo-50 text-indigo-700 border-indigo-100/50'
                                    };
                                @endphp
                                <tr class="text-slate-700 hover:bg-indigo-50/20 transition duration-300">
                                    <td class="px-5 py-4 text-xs font-bold text-slate-400">#{{ $pos->id_pos }}</td>
                                    
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 border border-slate-200/30">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            </div>
                                            <div>
                                                <span class="block text-sm font-bold text-slate-800">{{ $nom ?: 'Estudiante' }}</span>
                                                <span class="block text-[9px] text-slate-450">ID: {{ $pos->id_est }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4">
                                        <div>
                                            <span class="text-xs font-bold text-slate-700 block">{{ $ofertaTxt }}</span>
                                            <span class="text-[9px] text-indigo-650 font-bold block uppercase tracking-wider mt-0.5">{{ $oac->unidadEducativa->nombre_ued ?? '—' }}</span>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-[11px] font-bold {{ $estadoColor }} shadow-sm">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                            {{ $pos->estadoPostulacion->nombre_ept ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if($pos->resultado && isset($pos->resultado->puntaje_total_res))
                                            <span class="inline-flex items-center gap-1 rounded-xl bg-teal-50 border border-teal-100 px-2.5 py-1 text-xs font-black text-teal-800 shadow-sm">
                                                <svg class="h-3 w-3 text-teal-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/></svg>
                                                {{ $pos->resultado->puntaje_total_res }} pts
                                            </span>
                                        @else
                                            <span class="text-xs font-bold text-slate-400">—</span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if($asi)
                                            <div class="flex flex-col">
                                                <span class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 border border-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700 shadow-sm w-fit">
                                                    <svg class="h-3 w-3 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    {{ ucfirst($asi->estado_asi ?? 'Asignado') }}
                                                </span>
                                                @if($asi->cupo && $asi->cupo->ofertaAcademica)
                                                    <span class="text-[9px] text-slate-450 font-light mt-1">Cupo Asignado (#{{ $asi->cupo->id_oac_cup }})</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs font-bold text-slate-400">—</span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if($les)
                                            <span class="inline-flex items-center gap-1 rounded-xl bg-amber-50 border border-amber-100 px-2.5 py-1 text-xs font-bold text-amber-800 shadow-sm">
                                                <svg class="h-3 w-3 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                Orden de Espera #{{ $les->orden_les }}
                                            </span>
                                        @else
                                            <span class="text-xs font-bold text-slate-400">—</span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <a href="{{ route('tutor.postulaciones.show', $pos) }}" 
                                           class="inline-flex items-center justify-center rounded-xl bg-indigo-50 hover:bg-indigo-650 hover:text-white px-3.5 py-2 text-xs font-bold text-indigo-650 transition duration-300 border border-indigo-100/40 active:scale-95 shadow-sm">
                                            Detalles
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Paginación --}}
            <div class="mt-5 border-t border-slate-100 pt-4">
                {{ $postulaciones->links() }}
            </div>
        @endif
    </div>
@endsection
