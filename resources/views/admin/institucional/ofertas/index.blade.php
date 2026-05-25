@extends('layouts.dashboard')

@section('title', 'Ofertas y cupos | Admin institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Ofertas académicas</span>
@endsection

@section('content')
    @php
        $pageSubtitle = $unidad
            ? trim($unidad->nombre_ued . ($unidad->codigo_ued ? ' (' . $unidad->codigo_ued . ')' : '')) . ' — gestione ofertas de admisión y cupos por gestión, nivel, curso y paralelo.'
            : 'Ofertas académicas y cupos de su unidad educativa.';
    @endphp

    <x-institucional.page module="ofertas" title="Ofertas y cupos" :subtitle="$pageSubtitle">
        <x-slot:actions>
            <x-admin.export-report route="admin.institucional.ofertas.export" />
        </x-slot:actions>

        <x-slot:kpis>
            <x-institucional.kpi-grid module="ofertas" :items="[
                ['label' => 'Ofertas (filtro)', 'value' => $resumen['total']],
                ['label' => 'Con cupo definido', 'value' => $resumen['con_cupo']],
                ['label' => 'Cupos disponibles', 'value' => $resumen['cupos_disponibles']],
                ['label' => 'Postulaciones recibidas', 'value' => $resumen['postulaciones']],
            ]" />
        </x-slot:kpis>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 animate-fadeInUp">
            <div class="lg:col-span-2">
                <x-institucional.panel module="ofertas" title="Información académica">
                    <div class="p-5 text-sm text-indigo-900 leading-relaxed font-light bg-indigo-50/20">
                        <p>La combinación <strong>gestión + nivel + curso + paralelo</strong> debe ser única en su unidad.
                            El nivel debe coincidir con el del curso. Defina cupos totales y disponibles al registrar o editar cada oferta.</p>
                        <a href="{{ route('admin.institucional.academic.index') }}" class="mt-2.5 inline-flex items-center gap-1 text-xs font-bold text-indigo-750 hover:underline">
                            Gestionar catálogo académico →
                        </a>
                    </div>
                </x-institucional.panel>
            </div>

            <div>
                <x-institucional.panel module="ofertas" title="Filtros de búsqueda">
                    <form method="GET" class="flex flex-wrap items-end gap-3 p-5">
                        <div class="flex-1 min-w-[100px]">
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Gestión</label>
                            <select name="id_ges_oac" class="w-full rounded-xl border border-slate-250 bg-[#F8FAFC] px-3 py-2 text-xs font-semibold text-slate-750 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                                <option value="">Todas</option>
                                @foreach($gestiones as $g)
                                    <option value="{{ $g->id_ges }}" @selected(request('id_ges_oac') == $g->id_ges)>{{ $g->nombre_ges }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-2 min-w-[130px]">
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Nivel</label>
                            <select name="id_niv_oac" class="w-full rounded-xl border border-slate-250 bg-[#F8FAFC] px-3 py-2 text-xs font-semibold text-slate-750 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                                <option value="">Todos</option>
                                @foreach($niveles as $n)
                                    <option value="{{ $n->id_niv }}" @selected(request('id_niv_oac') == $n->id_niv)>{{ $n->nombre_niv }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-md shadow-indigo-100 hover:bg-indigo-700 transition">Filtrar</button>
                            <a href="{{ route('admin.institucional.ofertas.index') }}" class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-655 hover:bg-slate-50 transition">Limpiar</a>
                        </div>
                    </form>
                </x-institucional.panel>
            </div>
        </div>

        {{-- Nueva oferta - Registro interactivo en pasos (Wizard) --}}
        <div class="mt-6 animate-fadeInUp">
            <x-institucional.panel module="ofertas" title="Nueva oferta académica (Registro paso a paso)">
                <section class="p-6"
                        x-data="ofertaForm({
                            cursos: @js($cursosParaJs),
                            paralelos: @js($paralelosParaJs),
                            nivelId: '{{ old('id_niv_oac') }}',
                            cursoId: '{{ old('id_cur_oac') }}',
                            paraleloId: '{{ old('id_par_oac') }}',
                            selectedDocs: @js(array_map('strval', old('documentos_requeridos', [])))
                        })">
                    
                    {{-- Indicador visual de pasos --}}
                    <div class="mb-8 flex items-center justify-between relative max-w-xl mx-auto px-4">
                        <div class="absolute left-10 right-10 top-1/2 -translate-y-1/2 h-0.5 bg-slate-200 -z-0">
                            <div class="h-full bg-gradient-to-r from-indigo-500 to-indigo-650 transition-all duration-350" :style="'width: ' + ((step - 1) / (maxStep - 1) * 100) + '%'"></div>
                        </div>
                        
                        <button type="button" @click="goToStep(1)" class="relative z-10 flex flex-col items-center gap-1.5 focus:outline-none group">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full border-2 text-xs font-black transition-all duration-350"
                                  :class="step >= 1 ? 'bg-indigo-600 border-indigo-600 text-white shadow-md shadow-indigo-100' : 'bg-white border-slate-200 text-slate-400'">
                                1
                            </span>
                            <span class="text-[9px] font-bold uppercase tracking-wider transition-colors" :class="step === 1 ? 'text-indigo-700' : 'text-slate-450'">Estructura</span>
                        </button>
                        
                        <button type="button" @click="goToStep(2)" class="relative z-10 flex flex-col items-center gap-1.5 focus:outline-none group">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full border-2 text-xs font-black transition-all duration-350"
                                  :class="step >= 2 ? 'bg-indigo-600 border-indigo-600 text-white shadow-md shadow-indigo-100' : 'bg-white border-slate-200 text-slate-400'">
                                2
                            </span>
                            <span class="text-[9px] font-bold uppercase tracking-wider transition-colors" :class="step === 2 ? 'text-indigo-700' : 'text-slate-450'">Documentos</span>
                        </button>
                        
                        <button type="button" @click="goToStep(3)" class="relative z-10 flex flex-col items-center gap-1.5 focus:outline-none group">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full border-2 text-xs font-black transition-all duration-350"
                                  :class="step >= 3 ? 'bg-indigo-600 border-indigo-600 text-white shadow-md shadow-indigo-100' : 'bg-white border-slate-200 text-slate-400'">
                                3
                            </span>
                            <span class="text-[9px] font-bold uppercase tracking-wider transition-colors" :class="step === 3 ? 'text-indigo-700' : 'text-slate-450'">Cupos</span>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.institucional.ofertas.store') }}">
                        @csrf
                        
                        {{-- PASO 1: Estructura Académica --}}
                        <div x-show="step === 1" x-transition.opacity.duration.300ms class="space-y-6">
                            
                            {{-- Gestión Académica (Estilo Premium Card) --}}
                            <div class="rounded-2xl bg-indigo-50/30 p-4 border border-indigo-100/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-md shadow-indigo-200/35">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </span>
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wider text-indigo-650">Gestión de admisión</p>
                                        <p class="text-[11px] text-slate-450 font-light mt-0.5">Establece el período académico correspondiente a la convocatoria.</p>
                                    </div>
                                </div>
                                <div class="w-full sm:w-64">
                                    <select name="id_ges_oac" required class="w-full rounded-xl border border-indigo-100 bg-white px-3.5 py-2.5 text-xs font-bold text-indigo-950 focus:outline-none focus:ring-2 focus:ring-indigo-100 shadow-sm transition">
                                        @foreach($gestiones as $g)
                                            <option value="{{ $g->id_ges }}" @selected(old('id_ges_oac', $gestionActiva?->id_ges) == $g->id_ges)>{{ $g->nombre_ges }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_ges_oac')<p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-6">
                                {{-- Nivel Académico --}}
                                <div>
                                    <label class="mb-3.5 block text-xs font-bold uppercase tracking-wider text-slate-500">1. Selecciona el Nivel académico</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                        @foreach($niveles as $n)
                                            @php
                                                $icon = '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>';
                                                if (str_contains(strtolower($n->nombre_niv), 'secun')) {
                                                    $icon = '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>';
                                                } elseif (str_contains(strtolower($n->nombre_niv), 'ini') || str_contains(strtolower($n->nombre_niv), 'pre')) {
                                                    $icon = '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                                                }
                                            @endphp
                                            <button type="button"
                                                    @click="nivelId = '{{ $n->id_niv }}'; onNivelChange();"
                                                    class="group relative flex items-center gap-3.5 rounded-2xl bg-white p-4.5 border transition-all duration-300 text-left w-full"
                                                    :class="nivelId === '{{ $n->id_niv }}' ? 'border-indigo-650 bg-indigo-50/20 ring-2 ring-indigo-100 shadow-md' : 'border-slate-200/95 shadow-[0_4px_10px_rgba(15,23,42,0.01)] hover:-translate-y-0.5 hover:shadow-md hover:border-indigo-200'">
                                                <span class="flex h-10 w-10 items-center justify-center rounded-xl transition-all duration-300"
                                                      :class="nivelId === '{{ $n->id_niv }}' ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-50 group-hover:text-indigo-600'">
                                                    {!! $icon !!}
                                                </span>
                                                <div>
                                                    <p class="text-sm font-bold transition-colors" :class="nivelId === '{{ $n->id_niv }}' ? 'text-indigo-950' : 'text-slate-800'">{{ $n->nombre_niv }}</p>
                                                    <p class="text-[10px] text-slate-400 font-light mt-0.5">Nivel educativo</p>
                                                </div>
                                                <div class="absolute top-4 right-4 flex h-4.5 w-4.5 items-center justify-center rounded-full border transition-all"
                                                     :class="nivelId === '{{ $n->id_niv }}' ? 'border-indigo-600 bg-indigo-600 text-white scale-105' : 'border-slate-300 bg-white'">
                                                    <svg x-show="nivelId === '{{ $n->id_niv }}'" class="h-2.5 w-2.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                    <select name="id_niv_oac" x-model="nivelId" required class="hidden">
                                        <option value="">Seleccione…</option>
                                        @foreach($niveles as $n)
                                            <option value="{{ $n->id_niv }}">{{ $n->nombre_niv }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_niv_oac')<p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>

                                {{-- Curso Asociado --}}
                                <div x-show="nivelId" x-transition.opacity.duration.300ms class="pt-4 border-t border-slate-100">
                                    <label class="mb-3.5 block text-xs font-bold uppercase tracking-wider text-slate-500">2. Elige el Curso asociado</label>
                                    <div class="flex flex-wrap gap-2.5">
                                        <template x-for="c in CursosFiltrados" :key="c.id">
                                            <button type="button"
                                                    @click="cursoId = String(c.id); onCursoChange();"
                                                    class="inline-flex items-center gap-2 rounded-xl px-4 py-3 text-xs font-bold border transition-all duration-300"
                                                    :class="String(cursoId) === String(c.id) ? 'bg-indigo-600 border-indigo-600 text-white shadow-md shadow-indigo-100/50' : 'bg-white border-slate-200/90 text-slate-700 hover:border-slate-350 hover:bg-slate-50 shadow-sm'">
                                                <svg class="h-4 w-4" :class="String(cursoId) === String(c.id) ? 'text-indigo-100' : 'text-slate-400'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                                <span x-text="c.nombre"></span>
                                            </button>
                                        </template>
                                    </div>
                                    <select name="id_cur_oac" x-model="cursoId" required class="hidden">
                                        <option value="">Seleccione nivel primero</option>
                                        <template x-for="c in CursosFiltrados" :key="c.id">
                                            <option :value="c.id" x-text="c.nombre"></option>
                                        </template>
                                    </select>
                                    @error('id_cur_oac')<p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>

                                {{-- Paralelo --}}
                                <div x-show="cursoId" x-transition.opacity.duration.300ms class="pt-4 border-t border-slate-100">
                                    <label class="mb-3.5 block text-xs font-bold uppercase tracking-wider text-slate-500">3. Asigna el Paralelo</label>
                                    <div class="flex flex-wrap gap-3">
                                        <template x-for="p in paralelosFiltrados" :key="p.id">
                                            <button type="button"
                                                    @click="paraleloId = String(p.id)"
                                                    class="flex h-11 w-11 items-center justify-center rounded-full border text-sm font-black transition-all duration-300 shadow-sm"
                                                    :class="String(paraleloId) === String(p.id) ? 'bg-teal-600 border-teal-600 text-white shadow-md shadow-teal-100/50 scale-105' : 'bg-white border-slate-200/90 text-slate-700 hover:border-slate-350 hover:bg-slate-50'">
                                                <span x-text="p.nombre"></span>
                                            </button>
                                        </template>
                                    </div>
                                    <select name="id_par_oac" x-model="paraleloId" required class="hidden">
                                        <option value="">Seleccione curso</option>
                                        <template x-for="p in paralelosFiltrados" :key="p.id">
                                            <option :value="p.id" x-text="p.nombre"></option>
                                        </template>
                                    </select>
                                    @error('id_par_oac')<p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                           {{-- PASO 2: Documentación Requerida --}}
                        <div x-show="step === 2" x-transition.opacity.duration.300ms class="space-y-4">
                            <div>
                                <div class="mb-4">
                                    <h3 class="text-sm font-bold text-slate-800">Requisitos de postulación</h3>
                                    <p class="text-xs text-slate-400 font-light mt-0.5">Seleccione la documentación obligatoria que los tutores deben presentar para calificar al proceso de admisión.</p>
                                </div>

                                @if($tiposDocumento->isEmpty())
                                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3.5 text-sm text-amber-800 flex items-center gap-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        No existen tipos de documento en el catálogo general.
                                    </div>
                                @else
                                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                                        @foreach($tiposDocumento as $tipo)
                                            <label 
                                                :class="selectedDocs.includes('{{ $tipo->id_tdo }}') ? 'border-indigo-500 bg-indigo-50/40 ring-1 ring-indigo-200 shadow-md shadow-indigo-100/30' : 'border-slate-200 bg-white hover:border-slate-350 hover:bg-slate-50/30 shadow-[0_2px_4px_rgba(15,23,42,0.015)]'"
                                                class="relative flex flex-col justify-between rounded-2xl border p-4.5 transition-all duration-300 cursor-pointer group"
                                            >
                                                <div class="flex items-start justify-between gap-3">
                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100/80 text-slate-550 transition-colors duration-300 group-hover:bg-indigo-50 group-hover:text-indigo-650"
                                                         :class="selectedDocs.includes('{{ $tipo->id_tdo }}') ? '!bg-indigo-600 !text-white' : ''">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                    </div>
                                                    
                                                    <input type="checkbox"
                                                           name="documentos_requeridos[]"
                                                           value="{{ $tipo->id_tdo }}"
                                                           x-model="selectedDocs"
                                                           class="rounded-full h-5 w-5 border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-all duration-350">
                                                </div>

                                                <div class="mt-4">
                                                    <span class="block text-sm font-bold text-slate-800 transition-colors group-hover:text-indigo-950"
                                                          :class="selectedDocs.includes('{{ $tipo->id_tdo }}') ? '!text-indigo-955' : ''">
                                                        {{ $tipo->nombre_tdo }}
                                                    </span>
                                                    <span class="mt-1.5 block text-[10px] text-slate-400 font-light leading-relaxed">
                                                        Obligatorio para la verificación del expediente e inscripción formal.
                                                    </span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                                @error('documentos_requeridos')<p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- PASO 3: Cupos y Capacidad --}}
                        <div x-show="step === 3" x-transition.opacity.duration.300ms class="space-y-6">
                            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-555">Cupos totales</label>
                                    <input type="number" min="0" name="total_cup" value="{{ old('total_cup', 0) }}"
                                        class="w-full rounded-xl border border-slate-200 bg-[#F8FAFC] px-3.5 py-3 text-sm text-slate-800 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100 shadow-inner">
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-555">Cupos disponibles</label>
                                    <input type="number" min="0" name="disponibles_cup" value="{{ old('disponibles_cup', 0) }}"
                                        class="w-full rounded-xl border border-slate-200 bg-[#F8FAFC] px-3.5 py-3 text-sm text-slate-800 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100 shadow-inner">
                                    @error('disponibles_cup')<p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>
                                <div class="flex items-end">
                                    <p class="text-xs text-slate-400 font-light leading-relaxed">Los cupos iniciales determinarán el número de vacantes libres para la asignación inmediata.</p>
                                </div>
                            </div>

                            <div class="border-t border-slate-100 pt-5">
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-555">Descripción / Glosa de la Oferta</label>
                                <textarea name="descripcion_oac" rows="2" maxlength="255" placeholder="Opcional: detalles académicos o requisitos específicos de esta oferta..."
                                    class="w-full rounded-xl border border-slate-200 bg-[#F8FAFC] px-3.5 py-3 text-sm text-slate-800 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100 shadow-inner">{{ old('descripcion_oac') }}</textarea>
                            </div>
                        </div>

                        {{-- Wizard Navigation Footer --}}
                        <div class="mt-8 flex items-center justify-between border-t border-slate-200 pt-6">
                            <button type="button"
                                    @click="prevStep"
                                    x-show="step > 1"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-xs font-bold text-slate-600 transition-all hover:bg-slate-50 shadow-sm">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                Anterior
                            </button>
                            <div x-show="step === 1"></div>
                            
                            <button type="button"
                                    @click="nextStep"
                                    x-show="step < maxStep"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-6 py-2.5 text-xs font-bold text-white shadow-md shadow-indigo-100/35 transition-all hover:bg-indigo-700">
                                Siguiente
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            
                            <button type="submit"
                                    x-show="step === maxStep"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-650 px-6 py-2.5 text-xs font-bold text-white shadow-md shadow-emerald-100/35 transition-all hover:from-teal-700 hover:to-emerald-700">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Registrar oferta
                            </button>
                        </div>
                    </form>
                </section>
            </x-institucional.panel>
        </div>

        {{-- Ofertas Registradas con Inset 3D --}}
        <div class="mt-6 animate-fadeInUp">
            <x-institucional.panel module="ofertas" title="Ofertas académicas registradas">
                <div class="p-6">
                    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-slate-50/60 shadow-inner">
                        <table data-inst-table class="min-w-full text-sm text-left">
                            <thead class="border-b border-slate-200 bg-slate-100/80 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                <tr>
                                    <th class="px-5 py-4">Gestión</th>
                                    <th class="px-5 py-4">Nivel / curso / paralelo</th>
                                    <th class="px-5 py-4">Descripción</th>
                                    <th class="px-5 py-4">Convocatoria</th>
                                    <th class="px-5 py-4">Requisitos</th>
                                    <th class="px-5 py-4">Cupos</th>
                                    <th class="px-5 py-4">Postul.</th>
                                    <th class="px-5 py-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse($ofertas as $oferta)
                                    @php($cupo = $oferta->cupos->first())
                                    <tr class="text-slate-650 hover:bg-slate-50/40 transition-colors">
                                        <td class="px-5 py-4 font-bold text-slate-800">{{ $oferta->gestion->nombre_ges ?? '—' }}</td>
                                        <td class="px-5 py-4">
                                            <p class="font-bold text-slate-800 leading-snug">{{ $oferta->nivel->nombre_niv ?? '—' }}</p>
                                            <p class="text-[11px] text-slate-400 font-light mt-0.5">{{ $oferta->curso->nombre_cur ?? '—' }} · Paralelo {{ $oferta->paralelo->nombre_par ?? '—' }}</p>
                                        </td>
                                        <td class="px-5 py-4 text-slate-500 font-light truncate max-w-[150px]">{{ $oferta->descripcion_oac ?: '—' }}</td>
                                        <td class="px-5 py-4">
                                            @switch($oferta->estadoConvocatoria())
                                                @case('abierta')
                                                    <span class="inline-flex items-center gap-1 rounded-full border border-emerald-250 bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-800">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                        Abierta
                                                    </span>
                                                    @break
                                                @case('proxima')
                                                    <span class="inline-flex items-center gap-1 rounded-full border border-blue-250 bg-blue-50 px-2.5 py-0.5 text-[10px] font-bold text-blue-800">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                                        Próxima
                                                    </span>
                                                    @break
                                                @case('cerrada')
                                                    <span class="inline-flex items-center gap-1 rounded-full border border-rose-250 bg-rose-50 px-2.5 py-0.5 text-[10px] font-bold text-rose-800">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                                        Cerrada
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="inline-flex items-center gap-1 rounded-full border border-slate-250 bg-slate-50 px-2.5 py-0.5 text-[10px] font-bold text-slate-600">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                                        —
                                                    </span>
                                            @endswitch

                                            <p class="mt-1.5 text-[10px] text-slate-400 font-light">
                                                @if($oferta->gestion && $oferta->gestion->fecha_inicio_postulacion_ges && $oferta->gestion->fecha_fin_postulacion_ges)
                                                    {{ $oferta->gestion->fecha_inicio_postulacion_ges->format('d/m/Y H:i') }} al {{ $oferta->gestion->fecha_fin_postulacion_ges->format('d/m/Y H:i') }}
                                                @else
                                                    <span class="text-rose-600 font-semibold italic">Sin cronograma global</span>
                                                @endif
                                            </p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex max-w-[200px] flex-wrap gap-1">
                                                @forelse($oferta->tiposDocumentoRequeridos as $tipo)
                                                    <span class="rounded bg-slate-100 border border-slate-200/50 px-2 py-0.5 text-[10px] font-medium text-slate-600">
                                                        {{ $tipo->nombre_tdo }}
                                                    </span>
                                                @empty
                                                    <span class="text-[10px] text-slate-400 font-light">Sin documentos</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            @if($cupo)
                                                <form method="POST" action="{{ route('admin.institucional.cupos.update', $cupo) }}" class="flex flex-wrap items-center gap-1.5">
                                                    @csrf @method('PUT')
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-[9px] text-slate-400 font-medium">Tot:</span>
                                                        <input type="number" min="0" name="total_cup" value="{{ $cupo->total_cup }}" class="w-12 rounded-lg border border-slate-200 bg-white px-1.5 py-0.5 text-xs text-center font-mono">
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-[9px] text-slate-400 font-medium">Disp:</span>
                                                        <input type="number" min="0" name="disponibles_cup" value="{{ $cupo->disponibles_cup }}" class="w-12 rounded-lg border border-slate-200 bg-white px-1.5 py-0.5 text-xs text-center font-mono">
                                                    </div>
                                                    <button class="rounded-lg bg-indigo-50 border border-indigo-200/60 px-2 py-1 text-[10px] font-bold text-indigo-700 transition hover:bg-indigo-100">OK</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.institucional.cupos.store') }}" class="flex flex-wrap items-center gap-1.5">
                                                    @csrf
                                                    <input type="hidden" name="id_oac_cup" value="{{ $oferta->id_oac }}">
                                                    <input type="number" min="0" name="total_cup" placeholder="Total" class="w-12 rounded-lg border border-slate-200 bg-white px-1.5 py-0.5 text-xs text-center font-mono" required>
                                                    <input type="number" min="0" name="disponibles_cup" placeholder="Disp." class="w-12 rounded-lg border border-slate-200 bg-white px-1.5 py-0.5 text-xs text-center font-mono" required>
                                                    <button class="rounded-lg bg-emerald-50 border border-emerald-200/60 px-2 py-1 text-[10px] font-bold text-emerald-700 transition hover:bg-emerald-100">+ Cupo</button>
                                                </form>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4">
                                            @if($oferta->postulaciones_count > 0)
                                                <a href="{{ route('admin.institucional.postulaciones.index', ['id_cur_oac' => $oferta->id_cur_oac, 'id_ges_oac' => $oferta->id_ges_oac]) }}"
                                                   class="font-black text-indigo-600 hover:underline font-mono">{{ $oferta->postulaciones_count }}</a>
                                            @else
                                                <span class="text-slate-400 font-mono">0</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-right whitespace-nowrap">
                                            <a href="{{ route('admin.institucional.ofertas.edit', $oferta) }}" class="mr-3 text-xs font-bold text-indigo-650 hover:underline">Editar</a>
                                            <form method="POST" action="{{ route('admin.institucional.ofertas.destroy', $oferta) }}" class="inline" onsubmit="return confirm('¿Eliminar esta oferta?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs font-bold text-rose-600 hover:underline">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-5 py-14 text-center text-slate-400 font-light">
                                            No hay ofertas para su unidad. Registre la primera arriba o revise el
                                            <a href="{{ route('admin.institucional.academic.index') }}" class="font-semibold text-indigo-600 hover:underline">catálogo académico</a>.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($ofertas->hasPages())
                    <div class="border-t border-slate-200 px-5 py-4 bg-slate-50/50">{{ $ofertas->links() }}</div>
                @endif
            </x-institucional.panel>
        </div>

        @include('admin.institucional.ofertas._alpine-oferta-form')
    </x-institucional.page>
@endsection
