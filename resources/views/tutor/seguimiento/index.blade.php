@extends('layouts.dashboard')

@section('title', 'Tutor | Seguimiento')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Seguimiento</span>
@endsection

@section('content')
    <div class="mb-6">
        <p class="text-xs text-slate-400">Tutor / Seguimiento</p>
        <h1 class="text-2xl font-bold text-slate-900">Seguimiento de postulaciones</h1>
        <p class="mt-1 text-sm text-slate-500">Línea de tiempo simple por fecha de postulación y estado actual.</p>
    </div>

    @if($postulaciones->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <p class="text-slate-600">No hay postulaciones para mostrar.</p>
        </div>
    @else
        <ol class="relative space-y-6 border-l-2 border-teal-100 pl-8 before:absolute before:left-[-5px] before:top-2 before:h-[calc(100%-1rem)] before:w-0.5 before:bg-gradient-to-b before:from-teal-400 before:to-cyan-300">
            @foreach($postulaciones as $pos)
                @php
                    $nom = trim(($pos->estudiante->persona->nombres_per ?? '').' '.($pos->estudiante->persona->ap_paterno_per ?? ''));
                    $curso = ($pos->ofertaAcademica->curso->nombre_cur ?? '—').' '.($pos->ofertaAcademica->paralelo->nombre_par ?? '');
                    $enEspera = $pos->listasEspera->isNotEmpty();
                @endphp
                <li class="relative rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                    <span class="absolute -left-[1.15rem] top-6 flex h-3 w-3 rounded-full border-2 border-white bg-teal-500 ring-2 ring-teal-100"></span>
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase text-slate-400">{{ optional($pos->fecha_pos)->format('d/m/Y H:i') ?? '—' }}</p>
                            <p class="mt-1 text-lg font-semibold text-slate-900">{{ $nom ?: 'Estudiante' }} · {{ $curso }}</p>
                            <p class="mt-1 text-sm text-slate-600">Postulación <strong>#{{ $pos->id_pos }}</strong></p>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $pos->estadoPostulacion->nombre_ept ?? '—' }}</span>
                            @if($pos->resultado)
                                <span class="text-xs text-emerald-700">Puntaje: {{ $pos->resultado->puntaje_total_res ?? '—' }}</span>
                            @endif
                            @if($enEspera)
                                <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">Lista de espera</span>
                            @endif
                            <a href="{{ route('tutor.postulaciones.show', $pos) }}" class="text-sm font-semibold text-indigo-600 hover:underline">Ver detalle</a>
                        </div>
                    </div>
                </li>
            @endforeach
        </ol>
    @endif
@endsection
