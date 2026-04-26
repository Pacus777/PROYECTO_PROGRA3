@extends('layouts.dashboard')

@section('title', 'Detalle postulación | Admin institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span>Postulaciones</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Detalle</span>
@endsection

@section('content')
    <div class="mb-6">
        <p class="text-xs text-slate-400">Panel / Postulaciones</p>
        <h1 class="text-2xl font-bold text-slate-900">Detalle de postulante</h1>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-2xl bg-white p-5 shadow-sm">
            <h2 class="mb-3 text-lg font-semibold text-slate-800">Información general</h2>
            <p><strong>Estudiante:</strong> {{ trim(($postulacion->estudiante->persona->nombres_per ?? '').' '.($postulacion->estudiante->persona->ap_paterno_per ?? '')) }}</p>
            <p><strong>Curso:</strong> {{ $postulacion->ofertaAcademica->curso->nombre_cur ?? '—' }} {{ $postulacion->ofertaAcademica->paralelo->nombre_par ?? '' }}</p>
            <p><strong>Nivel:</strong> {{ $postulacion->ofertaAcademica->nivel->nombre_niv ?? '—' }}</p>
            <p><strong>Estado:</strong> {{ $postulacion->estadoPostulacion->nombre_ept ?? '—' }}</p>
            <p><strong>Puntaje total:</strong> {{ $postulacion->resultado->puntaje_total_res ?? '—' }}</p>
        </section>

        <section class="rounded-2xl bg-white p-5 shadow-sm">
            <h2 class="mb-3 text-lg font-semibold text-slate-800">Evaluaciones</h2>
            <form method="POST" action="{{ route('admin.institucional.evaluaciones.store', $postulacion) }}" class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-4">
                @csrf
                <select name="id_cri_eva" class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-2 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    @foreach(\App\Models\Criterio::orderBy('nombre_cri')->get() as $criterio)
                        <option value="{{ $criterio->id_cri }}">{{ $criterio->nombre_cri }}</option>
                    @endforeach
                </select>
                <input type="number" step="0.01" min="0" max="100" name="puntaje_eva" placeholder="Puntaje" class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-2 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <button class="rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 text-sm font-semibold text-white transition hover:from-indigo-700 hover:to-purple-700">Guardar</button>
                <textarea name="observaciones_eva" placeholder="Observaciones" class="md:col-span-3 rounded-lg border border-slate-200 bg-slate-50 px-2 py-2 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300"></textarea>
            </form>

            <div class="space-y-2">
                @foreach($postulacion->evaluaciones as $eva)
                    <form method="POST" action="{{ route('admin.institucional.evaluaciones.update', $eva) }}" class="rounded-lg border border-slate-100 p-3">
                        @csrf @method('PUT')
                        <div class="flex justify-between items-center gap-2">
                            <p class="font-semibold text-sm">{{ $eva->criterio->nombre_cri ?? 'Criterio' }}</p>
                            <input type="number" step="0.01" min="0" max="100" name="puntaje_eva" value="{{ $eva->puntaje_eva }}" class="w-24 rounded border border-slate-200 bg-slate-50 px-2 py-1 text-sm">
                        </div>
                        <textarea name="observaciones_eva" class="mt-2 w-full rounded border border-slate-200 bg-slate-50 px-2 py-1 text-sm">{{ $eva->observaciones_eva }}</textarea>
                        <div class="mt-2 flex gap-3">
                            <button class="rounded bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700">Actualizar</button>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('admin.institucional.evaluaciones.destroy', $eva) }}" onsubmit="return confirm('¿Eliminar evaluación?')">
                        @csrf @method('DELETE')
                        <button class="rounded bg-red-50 px-2 py-1 text-xs font-semibold text-rose-600">Eliminar</button>
                    </form>
                @endforeach
            </div>
        </section>
    </div>
@endsection

