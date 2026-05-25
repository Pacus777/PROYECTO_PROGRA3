@extends('layouts.dashboard')

@section('title', 'Tutor | Postulaciones')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Postulaciones</span>
@endsection

@section('content')
    {{-- Encabezado con Diseño Premium --}}
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center animate-fadeInUp">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Tutor / Gestión de Admisiones</p>
            <h1 class="text-3xl font-black tracking-tight text-slate-900 mt-1">Mis Postulaciones</h1>
            <p class="mt-1.5 text-xs text-slate-450 font-light leading-relaxed">
                Historial completo y avance en tiempo real de las postulaciones registradas para tus estudiantes vinculados.
            </p>
        </div>
        <a href="{{ route('tutor.postulaciones.create') }}" 
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 px-5 py-3 text-sm font-bold text-white shadow-md shadow-indigo-100/50 hover:shadow-lg hover:shadow-indigo-200/50 transition-all duration-300 hover:-translate-y-0.5 active:translate-y-0 active:scale-95">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nueva postulación
        </a>
    </div>

    {{-- Filtro de Búsqueda y Estado --}}
    <form method="GET" class="mb-6 flex flex-wrap items-center gap-4 rounded-2xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-4 shadow-[0_8px_30px_rgba(15,23,42,0.02),0_1px_2px_rgba(0,0,0,0.01)] animate-fadeInUp delay-75">
        <div class="flex items-center gap-2.5 shrink-0 text-slate-500">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-650 border border-indigo-100/30">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Filtro Rápido</span>
        </div>
        <select name="id_ept_pos" class="rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs font-bold text-slate-700 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100 shadow-inner min-w-[200px]">
            <option value="">Todos los estados</option>
            @foreach($estados as $estado)
                <option value="{{ $estado->id_ept }}" @selected(request('id_ept_pos') == $estado->id_ept)>{{ $estado->nombre_ept }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-xl bg-slate-800 hover:bg-slate-900 px-5 py-2.5 text-xs font-bold text-white transition-all duration-300 shadow-sm flex items-center gap-1.5 active:scale-95">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Aplicar Filtro
        </button>
        @if(request('id_ept_pos'))
            <a href="{{ route('tutor.postulaciones.index') }}" class="rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-650 transition">Limpiar</a>
        @endif
    </form>

    {{-- Listado Principal con Estructura de Relieve 3D --}}
    <div class="rounded-3xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-5 shadow-[0_12px_36px_rgba(15,23,42,0.03),0_1px_3px_rgba(0,0,0,0.015)] animate-fadeInUp delay-100">
        @if($postulaciones->total() === 0)
            <div class="py-12 flex flex-col items-center justify-center text-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-4 border border-slate-200/50">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm font-bold text-slate-800">No hay postulaciones registradas</p>
                <p class="text-xs text-slate-400 font-light mt-1 max-w-sm">
                    @if(request('id_ept_pos'))
                        No se encontraron postulaciones activas con el estado seleccionado. Intenta limpiar el filtro.
                    @else
                        Aún no has registrado ninguna solicitud de admisión. Comienza creando una nueva postulación para tu estudiante.
                    @endif
                </p>
            </div>
        @else
            {{-- Contenedor de Bajo Relieve (Fosa Táctil) --}}
            <div class="shadow-inner bg-slate-50/50 rounded-2xl border border-slate-250/50 p-2">
                <div class="overflow-x-auto rounded-xl">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200/60 bg-[#F8FAFC]/90">
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">ID</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">Prioridad</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">Estudiante</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">Curso Asignado</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">Fecha de Registro</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">Estado</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">Avance de Expediente</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">Puntaje</th>
                                <th class="px-5 py-4 text-right text-[10px] font-bold uppercase tracking-wider text-slate-450">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($postulaciones as $pos)
                                @php
                                    $nom = trim(($pos->estudiante->persona->nombres_per ?? '').' '.($pos->estudiante->persona->ap_paterno_per ?? ''));
                                @endphp
                                <tr class="text-slate-700 hover:bg-indigo-50/20 transition duration-300">
                                    <td class="px-5 py-4 text-xs font-bold text-slate-400">#{{ $pos->id_pos }}</td>
                                    
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 rounded-xl bg-indigo-50 px-2.5 py-1 text-[11px] font-bold text-indigo-700 border border-indigo-100/30 shadow-sm">
                                            <svg class="h-3 w-3 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                                            Opción {{ $pos->prioridad_pos }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-50 to-indigo-100/60 text-indigo-650 border border-indigo-100/30">
                                                <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            </div>
                                            <div>
                                                <span class="block text-sm font-bold text-slate-800">{{ $nom ?: 'Estudiante #'.$pos->id_est }}</span>
                                                <span class="block text-[9px] text-indigo-500 font-bold uppercase tracking-wider mt-0.5">RUDE: {{ $pos->estudiante->rude_est ?? 'Sin RUDE' }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 rounded-xl bg-slate-50 border border-slate-200/50 px-2.5 py-1 text-xs font-bold text-slate-700">
                                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                            {{ $pos->ofertaAcademica->curso->nombre_cur ?? '—' }} {{ $pos->ofertaAcademica->paralelo->nombre_par ?? '' }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2 text-slate-500">
                                            <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span class="text-xs font-semibold">{{ optional($pos->fecha_pos)->format('d/m/Y H:i') ?? '—' }}</span>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @php
                                            $estadoNombre = strtolower($pos->estadoPostulacion->nombre_ept ?? '');
                                            $colorClases = match(true) {
                                                str_contains($estadoNombre, 'acept') || str_contains($estadoNombre, 'asign') || str_contains($estadoNombre, 'complet') => 'bg-emerald-50 text-emerald-700 border-emerald-100/50',
                                                str_contains($estadoNombre, 'revis') || str_contains($estadoNombre, 'espera') => 'bg-amber-50 text-amber-700 border-amber-100/50',
                                                str_contains($estadoNombre, 'rechaz') || str_contains($estadoNombre, 'cancel') => 'bg-rose-50 text-rose-700 border-rose-100/50',
                                                default => 'bg-indigo-50 text-indigo-700 border-indigo-100/50'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-[11px] font-bold {{ $colorClases }} shadow-sm">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current animate-pulse"></span>
                                            {{ $pos->estadoPostulacion->nombre_ept ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        @php
                                            $porcentaje = $pos->porcentajeDocumental();
                                            $etapaTexto = [
                                                'registrada' => 'Registrada',
                                                'documentos_revision' => 'Docs. en revisión',
                                                'documentos_completos' => 'Docs. completos',
                                                'resultado' => 'Resultado generado',
                                                'asignado' => 'Cupo asignado',
                                                'lista_espera' => 'Lista de espera',
                                            ];
                                            $etapa = $pos->etapaTutor();
                                        @endphp
                                        <div class="min-w-[140px]">
                                            <div class="mb-1.5 flex items-center justify-between text-xs font-bold">
                                                <span class="text-slate-600">
                                                    {{ $etapaTexto[$etapa] ?? 'En proceso' }}
                                                </span>
                                                <span class="text-indigo-650">{{ $porcentaje }}%</span>
                                            </div>
                                            <div class="h-2 overflow-hidden rounded-full bg-slate-200/80 shadow-inner">
                                                <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-indigo-650 shadow-[0_0_8px_rgba(99,102,241,0.3)]" style="width: {{ $porcentaje }}%"></div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if($pos->resultado && isset($pos->resultado->puntaje_total_res))
                                            <span class="inline-flex h-7 w-12 items-center justify-center rounded-xl bg-teal-50 border border-teal-100 text-xs font-black text-teal-800 shadow-sm">
                                                {{ $pos->resultado->puntaje_total_res }}
                                            </span>
                                        @else
                                            <span class="text-xs font-bold text-slate-400">—</span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <a href="{{ route('tutor.postulaciones.show', $pos) }}" 
                                           class="inline-flex items-center justify-center rounded-xl bg-indigo-50 hover:bg-indigo-600 hover:text-white px-3.5 py-2 text-xs font-bold text-indigo-650 transition-all duration-300 border border-indigo-100/40 hover:shadow-md active:scale-95">
                                            <svg class="h-3.5 w-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detalles
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Paginación Premium --}}
            <div class="mt-5 border-t border-slate-100 pt-4">
                {{ $postulaciones->links() }}
            </div>
        @endif
    </div>
@endsection
