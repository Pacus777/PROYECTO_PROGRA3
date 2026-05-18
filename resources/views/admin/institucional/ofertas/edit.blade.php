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

    <form method="POST" action="{{ route('admin.institucional.ofertas.update', $oac) }}"
          class="mb-8 grid max-w-4xl gap-4 rounded-2xl bg-white p-5 shadow-sm md:grid-cols-2"
          x-data="ofertaForm({
            cursos: @js($cursosParaJs),
            paralelos: @js($paralelosParaJs),
            nivelId: '{{ old('id_niv_oac', $oac->id_niv_oac) }}',
            cursoId: '{{ old('id_cur_oac', $oac->id_cur_oac) }}',
            paraleloId: '{{ old('id_par_oac', $oac->id_par_oac) }}'
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
