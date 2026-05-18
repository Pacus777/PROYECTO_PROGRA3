@extends('layouts.dashboard')

@section('title', 'Postulación #'.$postulacion->id_pos)
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.postulaciones.index') }}" class="hover:text-indigo-600">Postulaciones</a>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">#{{ $postulacion->id_pos }}</span>
@endsection

@section('content')
    @php
        $est = $postulacion->estudiante;
        $per = $est->persona;
        $oac = $postulacion->ofertaAcademica;
        $ue = $oac->unidadEducativa;
        $nombre = trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? '').' '.($per->ap_materno_per ?? ''));
    @endphp

    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs text-slate-400">Vista nacional — solo lectura</p>
            <h1 class="text-2xl font-bold text-slate-900">Postulación #{{ $postulacion->id_pos }}</h1>
        </div>
        <a href="{{ route('admin.estudiantes.edit', $est) }}" class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">Ficha del postulante</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-400">Postulante</h2>
            <dl class="space-y-2 text-sm">
                <div><dt class="text-slate-500">Nombre</dt><dd class="font-semibold text-slate-900">{{ $nombre ?: '—' }}</dd></div>
                <div><dt class="text-slate-500">RUDE</dt><dd class="font-mono font-semibold text-emerald-800">{{ $est->rude_est ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">CI</dt><dd>{{ $per->ci_per ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Matrícula actual</dt><dd>{{ $est->unidadMatriculaActual->nombre_ued ?? 'Sin registrar' }}</dd></div>
            </dl>
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-400">Oferta y estado</h2>
            <dl class="space-y-2 text-sm">
                <div><dt class="text-slate-500">Unidad educativa</dt><dd class="font-semibold">{{ $ue->nombre_ued ?? '—' }} @if($ue?->codigo_ued)<span class="font-mono text-indigo-600">({{ $ue->codigo_ued }})</span>@endif</dd></div>
                <div><dt class="text-slate-500">Departamento</dt><dd>{{ $ue?->municipio?->provincia?->departamento?->nombre_dep ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Municipio</dt><dd>{{ $ue?->municipio?->nombre_mun ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Distrito educativo</dt><dd>{{ $ue?->distritoEducativo?->nombre_dis ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Gestión</dt><dd>{{ $oac->gestion->nombre_ges ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Curso / paralelo</dt><dd>{{ $oac->curso->nombre_cur ?? '—' }} {{ $oac->paralelo->nombre_par ?? '' }}</dd></div>
                <div><dt class="text-slate-500">Estado</dt><dd class="font-semibold">{{ $postulacion->estadoPostulacion->nombre_ept ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Fecha</dt><dd>{{ $postulacion->fecha_pos?->format('d/m/Y H:i') ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Puntaje</dt><dd>{{ $postulacion->resultado->puntaje_total_res ?? '—' }}</dd></div>
            </dl>
        </section>
    </div>

    @if($postulacion->evaluaciones->isNotEmpty())
        <section class="mt-6 rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="mb-3 text-sm font-bold uppercase text-slate-400">Evaluaciones</h2>
            <ul class="space-y-2 text-sm">
                @foreach($postulacion->evaluaciones as $eva)
                    <li class="flex justify-between rounded-lg border border-slate-100 px-3 py-2">
                        <span>{{ $eva->criterio->nombre_cri ?? 'Criterio' }}</span>
                        <span class="font-semibold">{{ $eva->puntaje_eva }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if($postulacion->documentos->isNotEmpty())
        <section class="mt-6 rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="mb-3 text-sm font-bold uppercase text-slate-400">Documentos</h2>
            <ul class="space-y-1 text-sm text-slate-700">
                @foreach($postulacion->documentos as $doc)
                    <li>{{ $doc->tipoDocumento->nombre_tdo ?? 'Documento' }} — {{ $doc->estado_doc ?? '—' }}</li>
                @endforeach
            </ul>
        </section>
    @endif
@endsection
