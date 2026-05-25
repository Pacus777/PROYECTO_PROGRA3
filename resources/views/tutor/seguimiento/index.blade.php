@extends('layouts.dashboard')

@section('title', 'Tutor | Seguimiento')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Seguimiento</span>
@endsection

@section('content')
    {{-- Cabecera con Diseño Premium --}}
    <div class="mb-8 animate-fadeInUp">
        <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Tutor / Línea de Tiempo</p>
        <h1 class="text-3xl font-black tracking-tight text-slate-900 mt-1">Seguimiento de Admisión</h1>
        <p class="mt-1.5 text-xs text-slate-450 font-light">Monitorea el avance de las postulaciones de tus estudiantes paso a paso desde el registro hasta la asignación final.</p>
    </div>

    @if($postulaciones->isEmpty())
        <div class="rounded-3xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-12 shadow-[0_12px_36px_rgba(15,23,42,0.03),0_1px_3px_rgba(0,0,0,0.015)] text-center max-w-xl animate-fadeInUp">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mx-auto mb-4 border border-slate-200/50">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01"/></svg>
            </div>
            <p class="text-sm font-bold text-slate-800">No hay postulaciones registradas para seguimiento</p>
            <p class="text-xs text-slate-400 font-light mt-1">Cuando registres una postulación para tus estudiantes, aparecerá en esta línea de tiempo interactiva.</p>
            <a href="{{ route('tutor.postulaciones.create') }}" class="mt-5 inline-flex items-center gap-1.5 rounded-xl bg-indigo-650 hover:bg-indigo-700 px-5 py-2.5 text-xs font-bold text-white transition-all shadow-md shadow-indigo-100/50 active:scale-95">
                Nueva Postulación
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </a>
        </div>
    @else
        {{-- Estructura de Timeline en Relieve --}}
        <div class="relative max-w-4xl pl-10 before:absolute before:left-[1.35rem] before:top-4 before:h-[calc(100%-2rem)] before:w-[3px] before:bg-gradient-to-b before:from-indigo-600/35 before:via-violet-600/20 before:to-transparent before:rounded animate-fadeInUp delay-75">
            @foreach($postulaciones as $pos)
                @php
                    $nom = trim(($pos->estudiante->persona->nombres_per ?? '').' '.($pos->estudiante->persona->ap_paterno_per ?? ''));
                    $curso = ($pos->ofertaAcademica->curso->nombre_cur ?? '—').' '.($pos->ofertaAcademica->paralelo->nombre_par ?? '');
                    $enEspera = $pos->listasEspera->isNotEmpty();
                    $etapa = $pos->etapaTutor();
                    
                    // Colores de estado
                    $estadoNombre = strtolower($pos->estadoPostulacion->nombre_ept ?? '');
                    $estadoColor = match(true) {
                        str_contains($estadoNombre, 'acept') || str_contains($estadoNombre, 'asign') || str_contains($estadoNombre, 'complet') => 'bg-emerald-50 text-emerald-700 border-emerald-100/50',
                        str_contains($estadoNombre, 'revis') || str_contains($estadoNombre, 'espera') => 'bg-amber-50 text-amber-700 border-amber-100/50',
                        str_contains($estadoNombre, 'rechaz') || str_contains($estadoNombre, 'cancel') => 'bg-rose-50 text-rose-700 border-rose-100/50',
                        default => 'bg-indigo-50 text-indigo-700 border-indigo-100/50'
                    };
                @endphp
                <div class="relative mb-8 last:mb-0">
                    {{-- Nodo indicador pulsante con relieve --}}
                    <span class="absolute -left-[2.15rem] top-6 flex h-6 w-6 items-center justify-center rounded-full bg-white text-indigo-650 border border-indigo-200/80 shadow-md ring-4 ring-[#EEF0F6]">
                        <span class="h-2.5 w-2.5 rounded-full bg-indigo-650 animate-pulse"></span>
                    </span>
                    
                    {{-- Tarjeta en Relieve 3D --}}
                    <div class="rounded-3xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-6 shadow-[0_8px_30px_rgba(15,23,42,0.02),0_1px_2px_rgba(0,0,0,0.01)] transition-all duration-350 hover:shadow-md hover:-translate-y-0.5">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        Registrado el {{ optional($pos->fecha_pos)->format('d/m/Y H:i') ?? '—' }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center gap-3 mt-1">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-50 to-indigo-100/50 text-indigo-650 border border-indigo-100/30">
                                        <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-extrabold text-slate-800 leading-snug">
                                            {{ $nom ?: 'Estudiante #'.$pos->id_est }}
                                        </h3>
                                        <p class="text-xs text-indigo-500 font-bold uppercase tracking-wider mt-0.5">
                                            {{ $curso }} ({{ $pos->ofertaAcademica->unidadEducativa->nombre_ued ?? 'Colegio' }})
                                        </p>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-450 font-light mt-1.5">
                                    Identificador único de postulación: <strong class="text-slate-650 font-bold">#{{ $pos->id_pos }}</strong>
                                </p>
                            </div>
                            
                            {{-- Estados y Resultados a la derecha --}}
                            <div class="flex flex-col items-end gap-2.5">
                                <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-bold {{ $estadoColor }} shadow-sm">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current animate-pulse"></span>
                                    {{ $pos->estadoPostulacion->nombre_ept ?? '—' }}
                                </span>
                                
                                @if($pos->resultado)
                                    <span class="inline-flex h-7 items-center justify-center rounded-xl bg-teal-50 border border-teal-100 px-2.5 text-xs font-black text-teal-800 shadow-sm gap-1">
                                        <svg class="h-3.5 w-3.5 text-teal-650" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/></svg>
                                        {{ $pos->resultado->puntaje_total_res ?? '—' }} pts
                                    </span>
                                @endif
                                
                                @if($enEspera)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800 shadow-sm">
                                        <svg class="h-3 w-3 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        Lista de Espera
                                    </span>
                                @endif
                                
                                <a href="{{ route('tutor.postulaciones.show', $pos) }}" 
                                   class="inline-flex items-center justify-center rounded-xl bg-indigo-50 hover:bg-indigo-650 hover:text-white px-3.5 py-2 text-xs font-bold text-indigo-650 transition duration-300 border border-indigo-100/40 active:scale-95 shadow-sm">
                                    Ver Detalle
                                </a>
                            </div>
                        </div>

                        {{-- Proceso en Pasos (Rastreador Físico Premium) --}}
                        <div class="mt-6 border-t border-slate-100 pt-5">
                            <div class="flex items-center justify-between max-w-2xl text-[10px] font-bold text-slate-400">
                                {{-- Step 1: Registro --}}
                                <div class="flex flex-col items-center gap-1">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full border-2 text-[9px] font-black transition-all duration-300 {{ $etapa !== 'borrador' ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-white border-slate-200' }}">1</span>
                                    <span class="{{ $etapa !== 'borrador' ? 'text-indigo-650' : '' }}">Registro</span>
                                </div>
                                
                                <div class="h-0.5 w-full bg-slate-200/80 mx-2"></div>

                                {{-- Step 2: Documentos --}}
                                @php
                                    $docsListos = in_array($etapa, ['documentos_completos', 'resultado', 'asignado', 'lista_espera']);
                                    $docsEnProgreso = $etapa === 'documentos_revision';
                                @endphp
                                <div class="flex flex-col items-center gap-1">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full border-2 text-[9px] font-black transition-all duration-300 {{ $docsListos ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : ($docsEnProgreso ? 'bg-amber-500 border-amber-500 text-white animate-pulse' : 'bg-white border-slate-200') }}">2</span>
                                    <span class="{{ $docsListos ? 'text-indigo-650' : ($docsEnProgreso ? 'text-amber-500' : '') }}">Expediente</span>
                                </div>

                                <div class="h-0.5 w-full bg-slate-200/80 mx-2"></div>

                                {{-- Step 3: Evaluación --}}
                                @php
                                    $evalListos = in_array($etapa, ['resultado', 'asignado', 'lista_espera']);
                                @endphp
                                <div class="flex flex-col items-center gap-1">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full border-2 text-[9px] font-black transition-all duration-300 {{ $evalListos ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-white border-slate-200' }}">3</span>
                                    <span class="{{ $evalListos ? 'text-indigo-650' : '' }}">Puntaje</span>
                                </div>

                                <div class="h-0.5 w-full bg-slate-200/80 mx-2"></div>

                                {{-- Step 4: Cierre --}}
                                @php
                                    $cierreListo = in_array($etapa, ['asignado', 'lista_espera']);
                                    $cierreColor = $etapa === 'asignado' ? 'bg-emerald-600 border-emerald-600' : ($etapa === 'lista_espera' ? 'bg-amber-600 border-amber-600' : 'bg-indigo-600 border-indigo-600');
                                @endphp
                                <div class="flex flex-col items-center gap-1">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full border-2 text-[9px] font-black transition-all duration-300 {{ $cierreListo ? $cierreColor.' text-white shadow-sm' : 'bg-white border-slate-200' }}">4</span>
                                    <span class="{{ $cierreListo ? 'text-slate-800' : '' }}">Cierre</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
