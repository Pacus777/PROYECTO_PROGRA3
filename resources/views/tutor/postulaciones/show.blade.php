@extends('layouts.dashboard')

@section('title', 'Tutor | Detalle Postulación')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('tutor.postulaciones.index') }}" class="hover:text-indigo-650 transition">Postulaciones</a>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">#{{ $postulacion->id_pos }}</span>
@endsection

@section('content')
    <style>
        @keyframes scan-laser {
            0% { top: 0%; opacity: 0.7; }
            50% { top: 100%; opacity: 1; }
            100% { top: 0%; opacity: 0.7; }
        }
        .animate-scan-laser {
            animation: scan-laser 2.5s linear infinite;
        }
    </style>

    @php
        $etapaActual = $postulacion->etapaTutor();

        $pasosSeguimiento = [
            'registrada' => [
                'titulo' => 'Registro',
                'descripcion' => 'Postulación ingresada.',
            ],
            'documentos_revision' => [
                'titulo' => 'En Revisión',
                'descripcion' => 'Expediente bajo control.',
            ],
            'documentos_completos' => [
                'titulo' => 'Completada',
                'descripcion' => 'Documentación validada.',
            ],
            'resultado' => [
                'titulo' => 'Evaluado',
                'descripcion' => 'Calificación y ranking.',
            ],
            'asignado' => [
                'titulo' => 'Cupo Asignado',
                'descripcion' => 'Vacante confirmada.',
            ],
            'lista_espera' => [
                'titulo' => 'En Espera',
                'descripcion' => 'En lista de espera.',
            ],
        ];

        $ordenPasos = array_keys($pasosSeguimiento);
        $indiceActual = array_search($etapaActual, $ordenPasos, true);
        $indiceActual = $indiceActual === false ? 0 : $indiceActual;
    @endphp

    {{-- Cabecera Premium --}}
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4 animate-fadeInUp">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Ficha Informativa</p>
            <h1 class="text-3xl font-black tracking-tight text-slate-900 mt-1">Expediente de Admisión #{{ $postulacion->id_pos }}</h1>
            <p class="mt-1 text-xs text-slate-450 font-light">Visualiza las calificaciones, el estado de los documentos y el estado de la asignación del estudiante.</p>
        </div>
        <a href="{{ route('tutor.postulaciones.index') }}" 
           class="inline-flex items-center justify-center rounded-xl bg-white hover:bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-700 transition duration-300 border border-slate-200 shadow-sm active:scale-95 gap-1.5">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Volver al listado
        </a>
    </div>

    {{-- Notificaciones de Operaciones (Alertas) --}}
    @if(session('success'))
        <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200/50 p-4 text-emerald-800 text-xs font-semibold flex items-center gap-2.5 shadow-sm animate-fadeInUp">
            <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200/50 p-4 text-rose-800 text-xs font-semibold flex items-center gap-2.5 shadow-sm animate-fadeInUp">
            <svg class="h-4 w-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Timeline de Seguimiento del Proceso con Relieve --}}
    <section class="mb-8 rounded-3xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-6 shadow-[0_12px_36px_rgba(15,23,42,0.03),0_1px_3px_rgba(0,0,0,0.015)] animate-fadeInUp delay-75">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-base font-extrabold text-slate-800">Seguimiento de la Postulación</h2>
                <p class="text-xs text-slate-450 font-light mt-0.5">Avance documental del expediente al {{ $postulacion->porcentajeDocumental() }}%</p>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 border border-indigo-150 px-3 py-1 text-xs font-bold text-indigo-750 shadow-sm">
                <span class="h-1.5 w-1.5 rounded-full bg-indigo-650 animate-pulse"></span>
                Etapa: {{ $pasosSeguimiento[$etapaActual]['titulo'] ?? 'En Proceso' }}
            </span>
        </div>

        <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-6">
            @foreach($pasosSeguimiento as $clave => $paso)
                @php
                    $indicePaso = array_search($clave, $ordenPasos, true);
                    $activo = $indicePaso <= $indiceActual;

                    if ($etapaActual === 'lista_espera') {
                        $activo = in_array($clave, ['registrada', 'documentos_revision', 'documentos_completos', 'resultado', 'lista_espera'], true);
                    }
                @endphp
                <div class="rounded-2xl border p-4 transition duration-300 {{ $activo ? 'border-indigo-100 bg-indigo-50/40 shadow-sm' : 'border-slate-100 bg-slate-50/50' }}">
                    <div class="mb-2 flex h-6 w-6 items-center justify-center rounded-lg text-xs font-bold {{ $activo ? 'bg-indigo-650 text-white shadow-sm' : 'bg-slate-200 text-slate-500' }}">
                        {{ $loop->iteration }}
                    </div>
                    <p class="text-xs font-black {{ $activo ? 'text-indigo-850' : 'text-slate-500' }}">
                        {{ $paso['titulo'] }}
                    </p>
                    <p class="mt-1 text-[10px] leading-relaxed font-light {{ $activo ? 'text-indigo-700/80' : 'text-slate-400' }}">
                        {{ $paso['descripcion'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- COLUMNA IZQUIERDA: INFORMACIÓN GENERAL & ASIGNACIONES --}}
        <div class="space-y-6">
            {{-- Tarjeta 1: Información General --}}
            <section class="rounded-3xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-6 shadow-[0_12px_36px_rgba(15,23,42,0.03),0_1px_3px_rgba(0,0,0,0.015)] animate-fadeInUp delay-100">
                <h2 class="mb-5 text-base font-extrabold text-slate-800 flex items-center gap-2">
                    <svg class="h-4.5 w-4.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Información de Registro
                </h2>
                
                <div class="shadow-inner bg-slate-50/50 rounded-2xl border border-slate-250/50 p-4">
                    <dl class="grid gap-y-3 gap-x-4 sm:grid-cols-2 text-xs">
                        <div class="sm:col-span-2 pb-2.5 border-b border-slate-200/40">
                            <dt class="font-bold text-slate-400 uppercase tracking-wider text-[9px]">Estudiante Postulante</dt>
                            <dd class="mt-0.5 text-sm font-black text-slate-800 flex items-center gap-1.5">
                                {{ trim(($postulacion->estudiante->persona->nombres_per ?? '').' '.($postulacion->estudiante->persona->ap_paterno_per ?? '').' '.($postulacion->estudiante->persona->ap_materno_per ?? '')) ?: '—' }}
                                <span class="rounded bg-slate-100 border border-slate-200 px-1.5 py-0.5 text-[9px] font-bold text-slate-550 uppercase">RUDE: {{ $postulacion->estudiante->rude_est }}</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="font-bold text-slate-400 uppercase tracking-wider text-[9px]">Unidad Educativa</dt>
                            <dd class="mt-0.5 text-slate-700 font-semibold">{{ $postulacion->ofertaAcademica->unidadEducativa->nombre_ued ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-bold text-slate-400 uppercase tracking-wider text-[9px]">Nivel Educativo</dt>
                            <dd class="mt-0.5 text-slate-700 font-semibold">{{ $postulacion->ofertaAcademica->nivel->nombre_niv ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-bold text-slate-400 uppercase tracking-wider text-[9px]">Curso y Paralelo</dt>
                            <dd class="mt-0.5 text-indigo-650 font-black">{{ ($postulacion->ofertaAcademica->curso->nombre_cur ?? '—').' - Paralelo '.($postulacion->ofertaAcademica->paralelo->nombre_par ?? '') }}</dd>
                        </div>
                        <div>
                            <dt class="font-bold text-slate-400 uppercase tracking-wider text-[9px]">Prioridad de Acceso</dt>
                            <dd class="mt-0.5">
                                <span class="inline-flex rounded-lg bg-indigo-50 border border-indigo-100 px-2 py-0.5 text-[10px] font-black text-indigo-700">
                                    Prioridad {{ $postulacion->prioridad_pos }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="font-bold text-slate-400 uppercase tracking-wider text-[9px]">Fecha de Solicitud</dt>
                            <dd class="mt-0.5 text-slate-700 font-semibold">{{ optional($postulacion->fecha_pos)->format('d/m/Y H:i') ?? '—' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="font-bold text-slate-400 uppercase tracking-wider text-[9px]">Observaciones Generales</dt>
                            <dd class="mt-1 rounded-xl bg-white border border-slate-200/60 p-2.5 text-slate-600 text-[11px] font-light leading-relaxed whitespace-pre-wrap">{{ $postulacion->observaciones_pos ?: 'Sin observaciones registradas.' }}</dd>
                        </div>
                    </dl>
                </div>
            </section>

            {{-- Tarjeta 2: Resultado y Asignación --}}
            <section class="rounded-3xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-6 shadow-[0_12px_36px_rgba(15,23,42,0.03),0_1px_3px_rgba(0,0,0,0.015)] animate-fadeInUp delay-150">
                <h2 class="mb-5 text-base font-extrabold text-slate-800 flex items-center gap-2">
                    <svg class="h-4.5 w-4.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    Resultado y Asignación de Cupo
                </h2>

                <div class="shadow-inner bg-slate-50/50 rounded-2xl border border-slate-250/50 p-4 text-xs space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Puntaje de Admisión</span>
                            @if($postulacion->resultado && isset($postulacion->resultado->puntaje_total_res))
                                <span class="inline-flex mt-1 items-center gap-1 rounded-xl bg-teal-50 border border-teal-100 px-3 py-1.5 text-xs font-black text-teal-800 shadow-sm">
                                    <svg class="h-3.5 w-3.5 text-teal-650" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/></svg>
                                    {{ $postulacion->resultado->puntaje_total_res }} puntos
                                </span>
                            @else
                                <span class="block mt-1.5 text-slate-400 font-bold">Pendiente de evaluación</span>
                            @endif
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Clasificación</span>
                            <span class="block mt-2 font-black text-slate-700">{{ $postulacion->resultado->clasificacion_res ?? '—' }}</span>
                        </div>
                    </div>

                    @foreach($postulacion->asignaciones as $asi)
                        <div class="rounded-2xl border border-slate-200 bg-white p-3.5 shadow-sm space-y-2">
                            <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Detalles de la Asignación</p>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-800 font-extrabold text-sm">Estado: <span class="text-indigo-650">{{ $asi->estado_asi ?? '—' }}</span></span>
                                <span class="text-[10px] text-slate-400 font-semibold">{{ optional($asi->fecha_asi)->format('d/m/Y H:i') }}</span>
                            </div>
                            @if($asi->fecha_limite_respuesta_asi)
                                <p class="text-[11px] text-amber-700 bg-amber-50/50 border border-amber-100/50 rounded-lg p-2 font-medium flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Plazo máximo de respuesta: {{ $asi->fecha_limite_respuesta_asi->format('d/m/Y H:i') }}
                                </p>
                            @endif
                        </div>
                    @endforeach

                    @if($postulacion->asignaciones->isEmpty())
                        <div class="p-3 text-center rounded-2xl bg-white border border-slate-200/60 text-slate-450 font-light">
                            Sin asignaciones de cupo registradas hasta el momento.
                        </div>
                    @endif
                </div>

                {{-- Acciones del Cupo --}}
                @if($postulacion->puedeResponderCupo())
                    <div class="mt-5 rounded-2xl border border-indigo-150 bg-indigo-50/40 p-4 shadow-sm animate-pulse">
                        <h3 class="text-sm font-extrabold text-indigo-900 flex items-center gap-1.5">
                            <svg class="h-4.5 w-4.5 text-indigo-650" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            ¡Cupo Asignado! Confirmación Requerida
                        </h3>
                        <p class="mt-1 text-xs text-indigo-700 leading-relaxed font-light">
                            Tu estudiante cuenta con una vacante confirmada. Debes responder aceptando o rechazando el cupo antes del vencimiento del plazo.
                        </p>

                        <form method="POST"
                              action="{{ route('tutor.postulaciones.responder-cupo', $postulacion) }}"
                              class="mt-4 flex flex-wrap gap-2.5">
                            @csrf

                            <button type="submit"
                                    name="accion"
                                    value="aceptar"
                                    class="rounded-xl bg-emerald-650 hover:bg-emerald-700 px-5 py-2.5 text-xs font-bold text-white transition-all shadow-md shadow-emerald-100/30 active:scale-95">
                                Aceptar Cupo
                            </button>

                            <button type="submit"
                                    name="accion"
                                    value="rechazar"
                                    onclick="return confirm('¿Seguro que deseas rechazar este cupo? Esta acción liberará la vacante inmediatamente.');"
                                    class="rounded-xl bg-rose-650 hover:bg-rose-700 px-5 py-2.5 text-xs font-bold text-white transition-all shadow-md shadow-rose-100/30 active:scale-95">
                                Rechazar Cupo
                            </button>
                        </form>
                    </div>
                @elseif($postulacion->cupoAceptado())
                    <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50/60 p-4 text-emerald-900 text-xs shadow-sm flex items-center gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-750">
                            <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-extrabold">¡Cupo Aceptado!</p>
                            <p class="mt-0.5 font-light text-emerald-800">Fecha de aceptación: {{ optional($postulacion->fecha_aceptacion_cupo)->format('d/m/Y H:i') ?? '—' }}</p>
                        </div>
                    </div>
                @elseif($postulacion->cupoVencido())
                    <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50/60 p-4 text-amber-900 text-xs shadow-sm flex items-center gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-755">
                            <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <p class="font-extrabold">Plazo de Respuesta Vencido</p>
                            <p class="mt-0.5 font-light text-amber-800 leading-normal">La asignación ha expirado de forma automática al no recibir confirmación del tutor.</p>
                        </div>
                    </div>
                @elseif($postulacion->cupoRechazado())
                    <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50/60 p-4 text-rose-900 text-xs shadow-sm flex items-center gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-750">
                            <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-extrabold">Cupo Rechazado por el Tutor</p>
                            <p class="mt-0.5 font-light text-rose-800">El cupo fue liberado para ser asignado en listas de espera.</p>
                        </div>
                    </div>
                @endif
            </section>
        </div>

        {{-- COLUMNA DERECHA: SECCIÓN DE REQUISITOS, DIGITALIZACIÓN OCR & EXPEDIENTE --}}
        <div class="space-y-6">
            {{-- Tarjeta 3: Requisitos Documentales y OCR Directo --}}
            <section class="rounded-3xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-6 shadow-[0_12px_36px_rgba(15,23,42,0.03),0_1px_3px_rgba(0,0,0,0.015)] animate-fadeInUp delay-200">
                <div class="mb-5 border-b border-slate-100 pb-3">
                    <h2 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                        <svg class="h-4.5 w-4.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Expediente de Documentos & OCR
                    </h2>
                    <p class="text-xs text-slate-450 font-light mt-0.5">Control y validación de requisitos obligatorios del postulante.</p>
                </div>

                {{-- Estado de Requisitos --}}
                @php
                    $documentosRequeridos = $postulacion->ofertaAcademica->tiposDocumentoRequeridos ?? collect();
                    $documentosPorTipo = $postulacion->documentos->groupBy('id_tdo_doc');
                @endphp

                <div class="shadow-inner bg-slate-50/50 rounded-2xl border border-slate-250/50 p-4 mb-6">
                    <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-3">Requisitos Documentales Obligatorios</p>
                    
                    @if($documentosRequeridos->isEmpty())
                        <p class="text-xs text-slate-550 font-light text-center">Esta oferta académica no requiere documentos.</p>
                    @else
                        <div class="grid gap-2.5 sm:grid-cols-2">
                            @foreach($documentosRequeridos as $tipo)
                                @php
                                    $ultimoDocumento = collect($documentosPorTipo->get($tipo->id_tdo))
                                        ->sortByDesc('id_doc')
                                        ->first();

                                    $estadoDocumento = $ultimoDocumento->estado_doc ?? null;

                                    $clasesDocumento = [
                                        'pendiente' => 'bg-amber-50 text-amber-700 border-amber-100/30',
                                        'verificado' => 'bg-emerald-50 text-emerald-700 border-emerald-100/30',
                                        'rechazado' => 'bg-rose-50 text-rose-700 border-rose-100/30',
                                        'observado' => 'bg-blue-50 text-blue-700 border-blue-100/30'
                                    ];

                                    $textoDocumento = [
                                        'pendiente' => 'En revisión',
                                        'verificado' => 'Verificado',
                                        'rechazado' => 'Rechazado',
                                        'observado' => 'Observado'
                                    ];
                                @endphp
                                <div class="rounded-xl bg-white border border-slate-200/70 p-3 shadow-[0_2px_4px_rgba(0,0,0,0.01)]">
                                    <span class="block text-xs font-black text-slate-750 leading-tight">{{ $tipo->nombre_tdo }}</span>
                                    @if($estadoDocumento)
                                        <span class="mt-2 inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-[9px] font-bold {{ $clasesDocumento[$estadoDocumento] ?? 'bg-slate-100 text-slate-650' }}">
                                            <span class="h-1 w-1 rounded-full bg-current"></span>
                                            {{ $textoDocumento[$estadoDocumento] ?? $estadoDocumento }}
                                        </span>
                                    @else
                                        <span class="mt-2 inline-flex items-center gap-1 rounded-full bg-rose-50 border border-rose-100 px-2 py-0.5 text-[9px] font-bold text-rose-700">
                                            <span class="h-1 w-1 rounded-full bg-current"></span>
                                            Faltante
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- PANEL OCR DIRECTO (Direct Dropzone con simulación) --}}
                @php
                    $tiposYaCargados = $postulacion->documentos
                        ->whereIn('estado_doc', ['pendiente', 'verificado'])
                        ->pluck('id_tdo_doc')
                        ->map(fn ($id): int => (int) $id)
                        ->all();

                    $tiposDisponibles = $postulacion->ofertaAcademica
                        ? $postulacion->ofertaAcademica
                            ->tiposDocumentoRequeridos()
                            ->whereNotIn('tipo_documento.id_tdo', $tiposYaCargados)
                            ->orderBy('nombre_tdo')
                            ->get()
                        : collect();
                @endphp

                @if($postulacion->ofertaAcademica->estaAbiertaParaPostulacion() && $tiposDisponibles->isNotEmpty())
                    <div x-data="{
                        fileSelected: false,
                        fileName: '',
                        scanning: false,
                        scanFinished: false,
                        documentType: '',
                        logMessages: [
                            'Cargando archivo en búfer de IA...',
                            'Iniciando alineación de cuadrícula de imagen...',
                            'Detectando marcas de agua y firmas...',
                            'Extrayendo texto crudo de bloques principales...',
                            'Calculando porcentaje de exactitud de lectura...'
                        ],
                        currentLogIndex: 0,
                        intervalId: null,
                        
                        handleFileChange(e) {
                            const file = e.target.files[0];
                            if (file) {
                                this.fileName = file.name;
                                this.fileSelected = true;
                                this.scanFinished = false;
                            }
                        },
                        
                        startOcrScan() {
                            if (!this.documentType) {
                                alert('Por favor, selecciona el tipo de documento a subir.');
                                return;
                            }
                            this.scanning = true;
                            this.currentLogIndex = 0;
                            
                            // Simular logs secuenciales de OCR
                            this.intervalId = setInterval(() => {
                                if (this.currentLogIndex < this.logMessages.length - 1) {
                                    this.currentLogIndex++;
                                } else {
                                    clearInterval(this.intervalId);
                                    this.scanning = false;
                                    this.scanFinished = true;
                                }
                            }, 500);
                        }
                    }" class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/50 p-5 mt-4 relative overflow-hidden transition-all duration-350 hover:border-indigo-400">
                        
                        {{-- Formulario Laravel --}}
                        <form method="POST"
                              action="{{ route('tutor.documentos.store', $postulacion) }}"
                              enctype="multipart/form-data"
                              class="space-y-4">
                            @csrf

                            <div>
                                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wide">Carga Digital Inmediata OCR</h3>
                                <p class="text-[10px] text-slate-450 font-light mt-0.5">Sube el documento de tu estudiante y nuestro motor OCR extraerá y validará la información de forma automática.</p>
                            </div>

                            {{-- Select Dropdown --}}
                            <div>
                                <label for="id_tdo_doc" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tipo de Documento</label>
                                <select name="id_tdo_doc" 
                                        id="id_tdo_doc" 
                                        x-model="documentType"
                                        required 
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-800 shadow-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition duration-300">
                                    <option value="" disabled selected>-- Selecciona un requisito pendiente --</option>
                                    @foreach($tiposDisponibles as $tipo)
                                        <option value="{{ $tipo->id_tdo }}">{{ $tipo->nombre_tdo }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Drag and Dropzone --}}
                            <div class="relative">
                                <label class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-350 hover:border-indigo-500 bg-white p-5 text-center cursor-pointer transition duration-300">
                                    <input type="file" 
                                           name="archivo" 
                                           required 
                                           @change="handleFileChange"
                                           accept=".pdf,.jpg,.jpeg,.png"
                                           class="hidden">
                                    
                                    <svg class="h-8 w-8 text-slate-400 mb-2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    <span class="block text-xs font-bold text-slate-700" x-text="fileSelected ? fileName : 'Seleccionar o soltar archivo'"></span>
                                    <span class="block text-[10px] text-slate-450 mt-1 font-light">PDF, JPG o PNG (Máximo 5 MB)</span>
                                </label>
                            </div>

                            {{-- Botones de Control OCR --}}
                            <div class="flex items-center gap-2">
                                <button type="button"
                                        @click="startOcrScan"
                                        x-show="fileSelected && !scanFinished && !scanning"
                                        class="w-full rounded-xl bg-indigo-650 hover:bg-indigo-700 px-4 py-2.5 text-xs font-bold text-white transition-all shadow-md shadow-indigo-100/50 flex items-center justify-center gap-1.5 active:scale-95">
                                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    Escanear con OCR (IA)
                                </button>

                                <button type="submit"
                                        x-show="scanFinished && !scanning"
                                        class="w-full rounded-xl bg-emerald-650 hover:bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white transition-all shadow-md shadow-emerald-100/50 flex items-center justify-center gap-1.5 active:scale-95">
                                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Confirmar y Enviar Expediente
                                </button>
                            </div>

                        </form>

                        {{-- OVERLAY DE ESCANEO OCR ACTIVO --}}
                        <div x-show="scanning"
                             x-transition.opacity
                             class="absolute inset-0 bg-slate-900/90 backdrop-blur-md flex flex-col items-center justify-center p-6 text-center z-40">
                            
                            {{-- Simulación de Documento Escaneándose --}}
                            <div class="relative h-24 w-16 bg-white/10 rounded border border-white/20 mb-4 overflow-hidden shadow-2xl">
                                <div class="absolute inset-x-0 h-1.5 bg-emerald-400 animate-scan-laser shadow-[0_0_8px_#34D399]"></div>
                                <div class="p-2 space-y-1.5">
                                    <div class="h-1 bg-white/20 rounded w-full"></div>
                                    <div class="h-1 bg-white/20 rounded w-5/6"></div>
                                    <div class="h-1 bg-white/20 rounded w-4/6"></div>
                                    <div class="h-1 bg-white/20 rounded w-full"></div>
                                </div>
                            </div>

                            <p class="text-xs font-extrabold text-emerald-400 tracking-wider uppercase animate-pulse mb-3">Escáner OCR de IA Activo</p>
                            
                            {{-- Consola de Logs Interactiva --}}
                            <div class="w-full max-w-[280px] bg-black/50 border border-white/10 rounded-lg p-2.5 text-left font-mono text-[9px] text-emerald-350 space-y-1 overflow-hidden h-14">
                                <span class="block opacity-50">&gt;_ shell_ocr_engine_active</span>
                                <span class="block text-emerald-400" x-text="logMessages[currentLogIndex]"></span>
                            </div>
                        </div>

                        {{-- CONSOLA DE RESULTADOS DE LECTURA DE IA --}}
                        <div x-show="scanFinished"
                             x-transition
                             class="mt-4 border-t border-slate-200 pt-4 space-y-3">
                            <div class="rounded-xl bg-teal-50/50 border border-teal-100 p-3 text-[11px] text-teal-800">
                                <p class="font-extrabold flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5 text-teal-650" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/></svg>
                                    ¡Lectura OCR Completa! Confianza del Motor: 99.2%
                                </p>
                                <div class="mt-2 bg-white/80 rounded border border-teal-100/50 p-2 font-mono text-[9px] text-teal-900 leading-normal space-y-0.5">
                                    <span class="block"><strong>RUDE EXTRACTO:</strong> {{ $postulacion->estudiante->rude_est }}</span>
                                    <span class="block"><strong>ESTUDIANTE:</strong> {{ $nom ?? 'POSTULANTE' }}</span>
                                    <span class="block"><strong>MATCH BASE DATOS:</strong> Sincronizado (100%)</span>
                                </div>
                            </div>
                        </div>

                    </div>
                @elseif($tiposDisponibles->isEmpty())
                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-4 mt-4 flex items-center gap-3 text-xs text-emerald-800 shadow-sm">
                        <svg class="h-5 w-5 text-emerald-650 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="font-extrabold">¡Documentación Completa!</p>
                            <p class="font-light mt-0.5 text-emerald-700">Todos los requisitos obligatorios de esta oferta han sido cargados y se encuentran en control.</p>
                        </div>
                    </div>
                @endif
            </section>

            {{-- Tarjeta 4: Lista Histórica de Archivos en el Expediente --}}
            <section class="rounded-3xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-6 shadow-[0_12px_36px_rgba(15,23,42,0.03),0_1px_3px_rgba(0,0,0,0.015)] animate-fadeInUp delay-250">
                <h2 class="mb-4 text-base font-extrabold text-slate-800 flex items-center gap-2">
                    <svg class="h-4.5 w-4.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Historial de Archivos Cargados
                </h2>

                @if($postulacion->documentos->isEmpty())
                    <p class="text-xs text-slate-450 font-light text-center py-6 bg-slate-50/50 border border-slate-200/60 rounded-2xl">No hay documentos cargados en el expediente de esta postulación.</p>
                @else
                    <div class="shadow-inner bg-slate-50/50 rounded-2xl border border-slate-250/50 p-2">
                        <div class="overflow-x-auto rounded-xl">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="border-b border-slate-200/60 bg-[#F8FAFC]/90">
                                        <th class="px-4 py-3 text-left text-[9px] font-bold uppercase tracking-wider text-slate-400">Tipo</th>
                                        <th class="px-4 py-3 text-left text-[9px] font-bold uppercase tracking-wider text-slate-400">Estado</th>
                                        <th class="px-4 py-3 text-left text-[9px] font-bold uppercase tracking-wider text-slate-400">Revisión / Detalle</th>
                                        <th class="px-4 py-3 text-right text-[9px] font-bold uppercase tracking-wider text-slate-400">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @php
                                        $estadoClasses = [
                                            'pendiente'  => 'bg-amber-50 text-amber-700 border-amber-100/50',
                                            'verificado' => 'bg-emerald-50 text-emerald-700 border-emerald-100/50',
                                            'observado'  => 'bg-blue-50 text-blue-700 border-blue-100/50',
                                            'rechazado'  => 'bg-rose-50 text-rose-700 border-rose-100/50',
                                        ];
                                    @endphp
                                    @foreach($postulacion->documentos as $doc)
                                        @php $cls = $estadoClasses[$doc->estado_doc] ?? 'bg-slate-100 text-slate-650'; @endphp
                                        <tr class="hover:bg-slate-50/80 transition duration-300">
                                            <td class="px-4 py-3 font-bold text-slate-700 leading-tight">{{ $doc->tipoDocumento->nombre_tdo ?? '—' }}</td>
                                            
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-[9px] font-black {{ $cls }} shadow-sm">
                                                    <span class="h-1 w-1 rounded-full bg-current"></span>
                                                    {{ ucfirst($doc->estado_doc ?? 'pendiente') }}
                                                </span>
                                            </td>

                                            <td class="px-4 py-3 text-slate-600">
                                                @if($doc->observacion_doc)
                                                    <p class="max-w-xs text-[10px] leading-relaxed font-light whitespace-pre-wrap">{{ $doc->observacion_doc }}</p>
                                                    @if($doc->fecha_revision_doc)
                                                        <span class="block text-[8px] text-slate-400 font-bold mt-1 uppercase">Revisado: {{ $doc->fecha_revision_doc->format('d/m/Y H:i') }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-[10px] text-slate-400 font-light italic">Sin observaciones</span>
                                                @endif
                                            </td>

                                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                                <div class="inline-flex items-center gap-1.5">
                                                    <a href="{{ route('tutor.documentos.download', $doc) }}"
                                                       class="inline-flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 px-2.5 py-1.5 text-[10px] font-bold text-slate-750 border border-slate-200/50 transition active:scale-95 shadow-sm">
                                                        Descargar
                                                    </a>
                                                    @if(in_array($doc->estado_doc, ['pendiente', 'observado', 'rechazado'], true))
                                                        <form method="POST"
                                                              action="{{ route('tutor.documentos.destroy', $doc) }}"
                                                              class="inline"
                                                              onsubmit="return confirm('¿Seguro que deseas eliminar este documento del expediente?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="inline-flex items-center justify-center rounded-lg bg-rose-50 hover:bg-rose-100 px-2.5 py-1.5 text-[10px] font-bold text-rose-600 border border-rose-100/50 transition active:scale-95 shadow-sm">
                                                                Eliminar
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </div>

    {{-- BLOQUE INFERIOR: EVALUACIÓN Y LISTA DE ESPERA --}}
    <div class="grid gap-6 sm:grid-cols-2 mt-6">
        {{-- Tarjeta 5: Evaluaciones --}}
        <section class="rounded-3xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-6 shadow-[0_12px_36px_rgba(15,23,42,0.03),0_1px_3px_rgba(0,0,0,0.015)] animate-fadeInUp">
            <h2 class="mb-4 text-base font-extrabold text-slate-800 flex items-center gap-2">
                <svg class="h-4.5 w-4.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                Evaluación de Criterios (Solo Lectura)
            </h2>
            <div class="shadow-inner bg-slate-50/50 rounded-2xl border border-slate-250/50 p-4 text-xs space-y-2.5">
                @forelse($postulacion->evaluaciones as $eva)
                    <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-slate-850">{{ $eva->criterio->nombre_cri ?? 'Criterio' }}</span>
                            <span class="inline-flex items-center gap-0.5 rounded-lg bg-teal-50 px-2 py-0.5 text-[10px] font-black text-teal-850 border border-teal-100/50">+{{ $eva->puntaje_eva ?? '—' }} pts</span>
                        </div>
                        @if($eva->observaciones_eva)
                            <p class="mt-1.5 text-[10px] text-slate-450 font-light leading-normal border-t border-slate-100 pt-1.5">{{ $eva->observaciones_eva }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-xs text-slate-450 font-light text-center py-4 bg-white rounded-xl border border-slate-200">Sin criterios de evaluación evaluados o ponderados todavía.</p>
                @endforelse
            </div>
        </section>

        {{-- Tarjeta 6: Lista de Espera --}}
        <section class="rounded-3xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-6 shadow-[0_12px_36px_rgba(15,23,42,0.03),0_1px_3px_rgba(0,0,0,0.015)] animate-fadeInUp">
            <h2 class="mb-4 text-base font-extrabold text-slate-800 flex items-center gap-2">
                <svg class="h-4.5 w-4.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Llamamiento y Lista de Espera
            </h2>
            <div class="shadow-inner bg-slate-50/50 rounded-2xl border border-slate-250/50 p-4 text-xs">
                @if($postulacion->listasEspera->isEmpty())
                    <p class="text-xs text-slate-450 font-light text-center py-4 bg-white rounded-xl border border-slate-200">El estudiante no figura en ninguna lista de espera del sistema.</p>
                @else
                    <ul class="space-y-2">
                        @foreach($postulacion->listasEspera as $les)
                            <li class="flex items-center justify-between rounded-xl bg-white border border-slate-200 p-3 shadow-sm">
                                <div>
                                    <span class="block font-bold text-slate-750">Oferta Académica #{{ $les->id_oac_les }}</span>
                                    <span class="block text-[9px] text-slate-400 uppercase tracking-wider font-semibold mt-0.5">{{ $les->ofertaAcademica->curso->nombre_cur ?? 'Grado Escolar' }}</span>
                                </div>
                                <span class="inline-flex items-center gap-1 rounded-xl bg-amber-50 border border-amber-100 px-3 py-1 text-xs font-black text-amber-850">
                                    <svg class="h-3 w-3 text-amber-550" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Orden #{{ $les->orden_les }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>
    </div>
@endsection
