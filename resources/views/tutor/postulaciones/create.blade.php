@extends('layouts.dashboard')

@section('title', 'Tutor | Nueva postulación')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Nueva postulación</span>
@endsection

@section('content')
    <div class="mb-6">
        <p class="text-xs text-slate-400">Tutor / Postulaciones / Crear</p>
        <h1 class="text-2xl font-bold text-slate-900">Registrar postulación</h1>
        <p class="mt-1 text-sm text-slate-500">El estado inicial se asigna con la misma lógica que la API (<code class="rounded bg-slate-100 px-1 text-xs">borrador</code> por defecto vía servicio).</p>
    </div>

    @if($estudiantes->isEmpty())
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-amber-900">
            <p class="font-semibold">No puedes crear postulaciones sin estudiantes vinculados.</p>
        </div>
    @else
        <form method="POST" action="{{ route('tutor.postulaciones.store') }}" class="max-w-3xl space-y-6 rounded-2xl bg-white p-8 shadow-sm">
            @csrf
            <div>
                <label for="id_est_pos" class="block text-sm font-semibold text-slate-700">Estudiante</label>
                <select id="id_est_pos" name="id_est_pos" required class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="">Seleccione…</option>
                    @foreach($estudiantes as $est)
                        @php $nom = trim(($est->persona->nombres_per ?? '').' '.($est->persona->ap_paterno_per ?? '').' '.($est->persona->ap_materno_per ?? '')); @endphp
                        <option value="{{ $est->id_est }}" @selected(old('id_est_pos') == $est->id_est)>{{ $nom ?: 'Estudiante #'.$est->id_est }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="id_oac_pos" class="block text-sm font-semibold text-slate-700">Oferta académica</label>
                <select id="id_oac_pos" name="id_oac_pos" required class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="">Seleccione…</option>
                    @foreach($ofertas as $oac)
                        @php
                            $label = trim(implode(' · ', array_filter([
                                $oac->unidadEducativa->nombre_ued ?? null,
                                $oac->gestion->nombre_ges ?? null,
                                $oac->nivel->nombre_niv ?? null,
                                $oac->curso->nombre_cur ?? null,
                                $oac->paralelo->nombre_par ?? null,
                            ])));
                        @endphp
                        <option value="{{ $oac->id_oac }}" @selected(old('id_oac_pos') == $oac->id_oac)>{{ $label ?: 'Oferta #'.$oac->id_oac }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="observaciones_pos" class="block text-sm font-semibold text-slate-700">Observaciones (opcional)</label>
                <textarea id="observaciones_pos" name="observaciones_pos" rows="4" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">{{ old('observaciones_pos') }}</textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-indigo-700">Guardar</button>
                <a href="{{ route('tutor.postulaciones.index') }}" class="rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Cancelar</a>
            </div>
        </form>
    @endif
@endsection
