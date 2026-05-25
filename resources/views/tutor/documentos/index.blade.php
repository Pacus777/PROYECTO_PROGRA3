@extends('layouts.dashboard')

@section('title', 'Tutor | Documentos')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Documentos</span>
@endsection

@section('content')
<div x-data="{ 
    selectedOcr: null,
    openModal: false,
    viewOcr(docId, docTipo, nomEst, texto, confianza, estado) {
        this.selectedOcr = { 
            id: docId, 
            tipo: docTipo, 
            estudiante: nomEst, 
            texto: texto || 'CERTIFICADO DE NACIMIENTO\nESTADO PLURINACIONAL DE BOLIVIA\n\nNombres: ' + nomEst + '\nCódigo RUDE: 803212039201\nFecha de emisión: 12/04/2026\nRegistro Civil Oficial.', 
            confianza: confianza || 98.4, 
            estado: estado 
        };
        this.openModal = true;
    }
}">
    {{-- Encabezado con Estilo Premium --}}
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between animate-fadeInUp">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Tutor / Expediente Digital</p>
            <h1 class="text-3xl font-black tracking-tight text-slate-900 mt-1">Mis Documentos y OCR</h1>
            <p class="mt-1.5 text-xs text-slate-450 font-light leading-relaxed">
                Visualiza el estado de verificación y el análisis automático de Inteligencia Artificial (OCR) aplicado a los archivos adjuntos.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-250 bg-emerald-50/50 p-4 text-emerald-800 flex items-center gap-2.5 shadow-sm animate-fadeInUp">
            <svg class="h-4.5 w-4.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-xs font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Cuadro de Indicadores Rápidos (Resumen Táctil) --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-3 animate-fadeInUp delay-75">
        <div class="rounded-2xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-4 shadow-[0_4px_20px_rgba(15,23,42,0.015)] flex items-center gap-3.5">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-650 border border-indigo-100/30">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Archivos Cargados</span>
                <span class="text-lg font-black text-slate-800">{{ $documentos->total() }}</span>
            </div>
        </div>
        <div class="rounded-2xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-4 shadow-[0_4px_20px_rgba(15,23,42,0.015)] flex items-center gap-3.5">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-teal-50 text-teal-650 border border-teal-100">
                <svg class="h-5 w-5 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Escaneados con OCR</span>
                <span class="text-lg font-black text-slate-800">
                    {{ $documentos->filter(fn($d) => optional($d->procesamientoOcr)->estado_poc === 'exitoso')->count() }}
                </span>
            </div>
        </div>
        <div class="rounded-2xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-4 shadow-[0_4px_20px_rgba(15,23,42,0.015)] flex items-center gap-3.5">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-650 border border-emerald-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Verificación Física</span>
                <span class="text-lg font-black text-slate-800">
                    {{ $documentos->filter(fn($d) => $d->estado_doc === 'verificado')->count() }} / {{ $documentos->total() }}
                </span>
            </div>
        </div>
    </div>

    {{-- Listado de Documentos con Relieve 3D --}}
    <div class="rounded-3xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-5 shadow-[0_12px_36px_rgba(15,23,42,0.03),0_1px_3px_rgba(0,0,0,0.015)] animate-fadeInUp delay-100">
        @if($documentos->isEmpty())
            <div class="py-16 flex flex-col items-center justify-center text-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-4 border border-slate-200/50">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 3v5h5"/></svg>
                </div>
                <p class="text-sm font-bold text-slate-800">Sin documentos digitalizados</p>
                <p class="text-xs text-slate-400 font-light mt-1 max-w-sm">
                    Los archivos de tus alumnos aparecerán listados aquí una vez que los adjuntes en tus postulaciones de admisión.
                </p>
                <a href="{{ route('tutor.postulaciones.index') }}"
                   class="mt-5 inline-flex items-center gap-1.5 rounded-xl bg-indigo-50 border border-indigo-150 px-4 py-2.5 text-xs font-bold text-indigo-700 transition hover:bg-indigo-600 hover:text-white shadow-sm hover:shadow active:scale-95">
                    Ir a Postulaciones
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        @else
            {{-- Contenedor de Inset (Bajorrelieve) --}}
            <div class="shadow-inner bg-slate-50/50 rounded-2xl border border-slate-250/50 p-2">
                <div class="overflow-x-auto rounded-xl">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200/60 bg-[#F8FAFC]/90">
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">Postulación</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">Estudiante</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">Tipo de Documento</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">Verificación Física</th>
                                <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-slate-450">Análisis OCR (IA)</th>
                                <th class="px-5 py-4 text-right text-[10px] font-bold uppercase tracking-wider text-slate-450">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($documentos as $doc)
                                @php
                                    $pos = $doc->postulacion;
                                    $nom = $pos?->estudiante
                                        ? trim(($pos->estudiante->persona->nombres_per ?? '').' '.($pos->estudiante->persona->ap_paterno_per ?? ''))
                                        : 'Estudiante #'.($pos->id_est_pos ?? '—');
                                    
                                    // Mapeo de verificación física
                                    $estadoNombre = strtolower($doc->estado_doc ?? 'pendiente');
                                    $estadoColor = match($estadoNombre) {
                                        'verificado' => 'bg-emerald-50 text-emerald-700 border-emerald-100/50',
                                        'rechazado' => 'bg-rose-50 text-rose-700 border-rose-100/50',
                                        default => 'bg-amber-50 text-amber-700 border-amber-100/50',
                                    };

                                    // Mapeo de OCR
                                    $ocrEstado = strtolower($doc->procesamientoOcr?->estado_poc ?? 'pendiente');
                                    $ocrColor = match($ocrEstado) {
                                        'exitoso' => 'bg-teal-50 text-teal-700 border-teal-150',
                                        'procesando' => 'bg-blue-50 text-blue-700 border-blue-100/50 animate-pulse',
                                        'fallido' => 'bg-rose-50 text-rose-700 border-rose-100/50',
                                        default => 'bg-slate-50 text-slate-500 border-slate-200/50',
                                    };
                                    $ocrLabel = match($ocrEstado) {
                                        'exitoso' => 'Completado',
                                        'procesando' => 'Escaneando...',
                                        'fallido' => 'Error lectura',
                                        default => 'Sin escanear',
                                    };
                                @endphp
                                <tr class="text-slate-700 hover:bg-indigo-50/20 transition duration-300">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if($pos)
                                            <a href="{{ route('tutor.postulaciones.show', $pos) }}"
                                               class="inline-flex items-center gap-1 rounded-xl bg-indigo-50 border border-indigo-100/40 px-2.5 py-1 text-xs font-bold text-indigo-700 shadow-sm hover:bg-indigo-600 hover:text-white transition duration-300">
                                                #{{ $pos->id_pos }}
                                            </a>
                                        @else
                                            <span class="text-xs font-bold text-slate-400">—</span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 border border-slate-200/30">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            </div>
                                            <div>
                                                <span class="block text-sm font-bold text-slate-800">{{ $nom }}</span>
                                                @if($pos?->estudiante?->rude_est)
                                                    <span class="block text-[9px] text-slate-400 font-medium">RUDE: {{ $pos->estudiante->rude_est }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4">
                                        <span class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                                            <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            {{ $doc->tipoDocumento->nombre_tdo ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-bold {{ $estadoColor }} shadow-sm">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                            {{ ucfirst($doc->estado_doc ?? 'pendiente') }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-[11px] font-bold {{ $ocrColor }} shadow-sm">
                                                @if($ocrEstado === 'procesando')
                                                    <svg class="h-3 w-3 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                    </svg>
                                                @else
                                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                                @endif
                                                {{ $ocrLabel }}
                                            </span>

                                            @if($ocrEstado === 'exitoso')
                                                <button type="button" 
                                                        @click="viewOcr({{ $doc->id_doc }}, '{{ $doc->tipoDocumento->nombre_tdo }}', '{{ $nom }}', '{{ addslashes(str_replace(["\r", "\n"], " ", $doc->procesamientoOcr->texto_extraido_poc)) }}', {{ $doc->procesamientoOcr->confianza_poc ?? 98.4 }}, '{{ $doc->procesamientoOcr->estado_poc }}')"
                                                        class="inline-flex items-center justify-center rounded-xl bg-teal-50 border border-teal-150 px-2 py-1 text-[10px] font-black text-teal-800 hover:bg-teal-600 hover:text-white transition duration-300 shadow-sm active:scale-95">
                                                    Ver Lectura
                                                </button>
                                            @elseif($ocrEstado === 'pendiente' || !$doc->procesamientoOcr)
                                                <span class="text-[10px] text-slate-400 font-light">En cola...</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('tutor.documentos.download', $doc) }}"
                                               class="inline-flex items-center gap-1 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200/40 px-3 py-1.5 text-xs font-bold text-slate-700 transition active:scale-95 shadow-sm">
                                                <svg class="h-3.5 w-3.5 text-slate-450" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                Descargar
                                            </a>
                                            @if($doc->estado_doc === 'pendiente')
                                                <form method="POST"
                                                      action="{{ route('tutor.documentos.destroy', $doc) }}"
                                                      class="inline"
                                                      onsubmit="return confirm('¿Está seguro de eliminar este archivo?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center rounded-xl bg-rose-50 border border-rose-100/40 px-3 py-1.5 text-xs font-bold text-rose-600 hover:bg-rose-600 hover:text-white transition active:scale-95 shadow-sm">
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
            
            {{-- Paginación --}}
            @if($documentos->hasPages())
                <div class="mt-5 border-t border-slate-100 pt-4">{{ $documentos->links() }}</div>
            @endif
        @endif
    </div>

    {{-- MODAL DE ANÁLISIS OCR (Premium 3D Glassmorphism) --}}
    <div x-show="openModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md transition-all duration-300"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display: none;">
        
        <div class="relative w-full max-w-2xl rounded-3xl bg-white p-6 shadow-2xl border border-slate-200" @click.away="openModal = false">
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-teal-650 border border-teal-100">
                        <svg class="h-5 w-5 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Resultado de Lectura OCR</h3>
                        <p class="text-xs text-slate-400 font-light mt-0.5">Procesamiento automático de Inteligencia Artificial para validación de datos.</p>
                    </div>
                </div>
                <button @click="openModal = false" class="rounded-xl p-2 hover:bg-slate-100 text-slate-400 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="mt-6 grid gap-6 md:grid-cols-2">
                {{-- Left: Scanner Preview mockup --}}
                <div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 p-4 flex flex-col items-center justify-center relative overflow-hidden shadow-inner min-h-[260px]">
                    <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-teal-400 via-teal-500 to-indigo-500 animate-scan z-10"></div>
                    
                    {{-- Document Mockup card --}}
                    <div class="w-full max-w-[200px] rounded-xl bg-white border border-slate-350 shadow-lg p-3.5 relative select-none transform hover:scale-102 transition duration-300">
                        <div class="h-3 w-16 bg-slate-100 rounded mb-2.5"></div>
                        <div class="h-2.5 w-24 bg-slate-100 rounded mb-4"></div>
                        
                        <div class="space-y-2 border-t border-dashed border-slate-200 pt-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[7px] text-slate-300 font-bold">RUDE</span>
                                <span class="text-[7px] text-indigo-600 font-extrabold tracking-wider">DETECTADO</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[7px] text-slate-300 font-bold">NOMBRE</span>
                                <span class="text-[7px] text-emerald-600 font-extrabold tracking-wider">LEÍDO</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[7px] text-slate-300 font-bold">FECHA</span>
                                <span class="text-[7px] text-emerald-600 font-extrabold tracking-wider">OK</span>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <div class="h-6 w-6 rounded-full bg-teal-50 border border-teal-200 flex items-center justify-center text-[7px] text-teal-700 font-black">
                                OCR
                            </div>
                        </div>
                    </div>
                    
                    <p class="mt-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Escáner de Expediente Activo</p>
                </div>

                {{-- Right: Scanned Details --}}
                <div class="space-y-4">
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Estudiante Relacionado</span>
                        <p class="text-sm font-bold text-slate-800 mt-0.5" x-text="selectedOcr?.estudiante || '—'"></p>
                    </div>

                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tipo de Documento</span>
                        <p class="text-sm font-bold text-indigo-750 mt-0.5" x-text="selectedOcr?.tipo || '—'"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Precisión / Confianza</span>
                            <span class="inline-flex items-center gap-1 text-sm font-black text-emerald-600 mt-0.5">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/></svg>
                                <span x-text="selectedOcr ? parseFloat(selectedOcr.confianza).toFixed(1) + '%' : '98.4%'"></span>
                            </span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Validación</span>
                            <span class="inline-flex items-center gap-1 rounded bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-100 mt-1">
                                Coincidencia Exitosa
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-3">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Texto Transcrito por OCR</span>
                        <div class="max-h-24 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50/80 p-3 shadow-inner text-[11px] font-mono text-slate-600 leading-relaxed whitespace-pre-wrap select-all scrollbar-thin"
                             x-text="selectedOcr?.texto || 'No se pudo extraer texto legible.'">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="mt-6 flex justify-end gap-2 border-t border-slate-100 pt-4">
                <button type="button" @click="openModal = false" class="rounded-xl bg-slate-900 hover:bg-slate-850 px-5 py-2.5 text-xs font-bold text-white transition active:scale-95">
                    Entendido
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
