@extends('layouts.dashboard')

@section('title', 'Domicilio del estudiante | Tutor')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('tutor.estudiantes.index') }}" class="text-teal-600 hover:underline">Mis estudiantes</a>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Domicilio</span>
@endsection

@section('content')
    @php
        $p = $estudiante->persona;
        $nombre = trim(($p->nombres_per ?? '').' '.($p->ap_paterno_per ?? ''));
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Domicilio de {{ $nombre ?: 'estudiante' }}</h1>
        <p class="mt-1 text-sm text-slate-500">
            Este paso es obligatorio antes de postular. Indique dónde vive el postulante; esa ubicación se usa para evaluar la cercanía al colegio.
        </p>
    </div>

    @if(session('warning'))
        <div class="mb-6 max-w-3xl rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            {{ session('warning') }}
        </div>
    @endif

    <form method="POST" action="{{ route('tutor.estudiantes.domicilio.update', $estudiante) }}"
          class="max-w-3xl rounded-2xl bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')
        @if(!empty($returnUrl))
            <input type="hidden" name="return" value="{{ $returnUrl }}">
        @endif

        <x-admin.address-location-picker
            :address="old('direccion_est', $estudiante->direccion_est)"
            :lat="old('lat_est', $estudiante->lat_est)"
            :lng="old('lng_est', $estudiante->lng_est)"
            address-name="direccion_est"
            lat-name="lat_est"
            lng-name="lng_est"
        />

        <div class="mt-6 flex gap-3">
            <button type="submit"
                    class="rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-md hover:from-teal-700 hover:to-emerald-700">
                Guardar domicilio
            </button>
            <a href="{{ route('tutor.estudiantes.index') }}"
               class="rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Cancelar
            </a>
        </div>
    </form>
@endsection
