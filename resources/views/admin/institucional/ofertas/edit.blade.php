@extends('layouts.dashboard')

@section('title', 'Editar oferta | Admin institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.institucional.ofertas.index') }}" class="hover:text-indigo-600">Ofertas</a>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Editar</span>
@endsection

@section('content')
    @php
        $oac = $oferta_academica;
        $cupo = $oac->cupos->first();
        $titulo = trim(($oac->gestion->nombre_ges ?? '').' · '.($oac->curso->nombre_cur ?? '').' '.($oac->paralelo->nombre_par ?? ''));
    @endphp

    @if(session('error'))
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
    @endif

    <div class="mb-6">
        <p class="text-xs text-slate-400">Panel / Ofertas / Editar</p>
        <h1 class="text-2xl font-bold text-slate-900">{{ $titulo ?: 'Editar oferta' }}</h1>
        @if($oac->postulaciones_count > 0)
            <p class="mt-2 text-sm text-amber-700">
                Esta oferta tiene <strong>{{ $oac->postulaciones_count }}</strong> postulación(es). Cambios de nivel/curso/paralelo deben mantener coherencia con la base de datos.
            </p>
        @endif
    </div>

    @php
        $documentosSeleccionados = old(
            'documentos_requeridos',
            $oac->tiposDocumentoRequeridos->pluck('id_tdo')->map(fn ($id) => (string) $id)->all()
        );
    @endphp

    <form method="POST" action="{{ route('admin.institucional.ofertas.update', $oac) }}"
          class="mb-8 grid max-w-4xl gap-4 rounded-2xl bg-white p-5 shadow-sm md:grid-cols-2"
          x-data="ofertaForm({
            cursos: @js($cursosParaJs),
            paralelos: @js($paralelosParaJs),
            nivelId: '{{ old('id_niv_oac', $oac->id_niv_oac) }}',
            cursoId: '{{ old('id_cur_oac', $oac->id_cur_oac) }}',
            paraleloId: '{{ old('id_par_oac', $oac->id_par_oac) }}',
            selectedDocs: @js(array_map('strval', $documentosSeleccionados))
          })">
        @csrf @method('PUT')

        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-500">Gestión</label>
            <select name="id_ges_oac" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                @foreach($gestiones as $g)
                    <option value="{{ $g->id_ges }}" @selected(old('id_ges_oac', $oac->id_ges_oac) == $g->id_ges)>{{ $g->nombre_ges }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-500">Nivel</label>
            <select name="id_niv_oac" x-model="nivelId" @change="onNivelChange" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                @foreach($niveles as $n)
                    <option value="{{ $n->id_niv }}">{{ $n->nombre_niv }}</option>
                @endforeach
            </select>
            @error('id_niv_oac')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-500">Curso</label>
            <select name="id_cur_oac" x-model="cursoId" @change="onCursoChange" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                <template x-for="c in cursosFiltrados" :key="c.id">
                    <option :value="c.id" x-text="c.nombre"></option>
                </template>
            </select>
            @error('id_cur_oac')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-500">Paralelo</label>
            <select name="id_par_oac" x-model="paraleloId" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                <template x-for="p in paralelosFiltrados" :key="p.id">
                    <option :value="p.id" x-text="p.nombre"></option>
                </template>
            </select>
            @error('id_par_oac')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div class="md:col-span-2">
            <label class="mb-1 block text-xs font-semibold text-slate-500">Descripción</label>
            <input name="descripcion_oac" value="{{ old('descripcion_oac', $oac->descripcion_oac) }}" maxlength="255"
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
        </div>

        <div class="md:col-span-2 rounded-xl bg-indigo-50/40 p-4 border border-indigo-100/35 text-xs text-indigo-850 flex items-start gap-2.5">
            <svg class="h-5 w-5 text-indigo-550 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="font-bold">Cronograma de Convocatoria Global</p>
                <p class="mt-0.5 leading-relaxed text-slate-550">Las fechas de inicio y cierre de postulación están sincronizadas globalmente por la gestión académica seleccionada.
                    @if($oac->gestion && $oac->gestion->fecha_inicio_postulacion_ges && $oac->gestion->fecha_fin_postulacion_ges)
                        Actualmente: <strong class="text-indigo-700">{{ $oac->gestion->fecha_inicio_postulacion_ges->format('d/m/Y H:i') }}</strong> hasta <strong class="text-indigo-700">{{ $oac->gestion->fecha_fin_postulacion_ges->format('d/m/Y H:i') }}</strong>.
                    @else
                        <strong class="text-rose-600">No se ha configurado un cronograma global para esta gestión aún.</strong>
                    @endif
                </p>
            </div>
        </div>

        <div class="md:col-span-2">
            <div class="mb-3">
                <label class="block text-xs font-semibold text-slate-500">Documentos requeridos</label>
                <p class="text-[11px] text-slate-400 font-light mt-0.5">Marque la documentación obligatoria que los tutores deben presentar para calificar al proceso de admisión.</p>
            </div>

            @if($tiposDocumento->isEmpty())
                <p class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    No existen tipos de documento registrados.
                </p>
            @else
                <div class="grid gap-4 md:grid-cols-2">
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

            @error('documentos_requeridos')
                <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2 flex flex-wrap gap-3">
            <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Guardar oferta</button>
            <a href="{{ route('admin.institucional.ofertas.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancelar</a>
        </div>
    </form>

    @if($cupo)
        <section class="max-w-md rounded-2xl bg-white p-5 shadow-sm">
            <h2 class="mb-3 text-sm font-bold uppercase text-slate-400">Cupo actual</h2>
            <p class="text-sm text-slate-600">Total: <strong>{{ $cupo->total_cup }}</strong> · Disponibles: <strong>{{ $cupo->disponibles_cup }}</strong></p>
            <p class="mt-2 text-xs text-slate-500">Edite cupos desde el listado de ofertas.</p>
        </section>
    @endif

    @include('admin.institucional.ofertas._alpine-oferta-form')
@endsection
