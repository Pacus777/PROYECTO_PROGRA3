@extends('layouts.dashboard')

@section('title', 'Editar estudiante | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.estudiantes.index') }}" class="hover:text-indigo-600">Estudiantes</a>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Editar</span>
@endsection

@section('content')
    @php $p = $estudiante->persona; @endphp
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">
            Editar estudiante
        </h1>
        <p class="mt-1 text-sm text-slate-500">
            {{ trim(($p->nombres_per ?? '').' '.($p->ap_paterno_per ?? '')) }}
        </p>
        <p class="mt-2 text-xs text-slate-500">Registro académico; el postulante no tiene cuenta de acceso.</p>
    </div>

    <x-alert-sin-cuenta-estudiante />

    @if(session('success'))
        <div class="mb-6 max-w-2xl rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-6 max-w-2xl rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.estudiantes.update', $estudiante) }}"
          class="max-w-2xl rounded-2xl bg-white p-6 shadow-sm md:p-8">
        @csrf
        @method('PUT')

        @include('admin.estudiantes._identificadores', ['estudiante' => $estudiante])
        @if(!$estudiante->rude_est && !$estudiante->codigo_est)
            <p class="-mt-4 mb-6 text-xs font-medium text-amber-700">Sin RUDE ni código: el tutor no podrá vincular a este estudiante.</p>
        @endif

        @include('admin.estudiantes._matricula_ue', ['estudiante' => $estudiante, 'unidades' => $unidades])

        @include('admin.estudiantes._form-personal', ['estudiante' => $estudiante])

        @include('admin.estudiantes._domicilio', ['estudiante' => $estudiante])

        <div class="mt-8 flex flex-wrap gap-3">
            <button type="submit"
                    class="rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:from-indigo-700 hover:to-purple-700">
                Guardar cambios
            </button>
            <a href="{{ route('admin.estudiantes.index') }}"
               class="rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Cancelar
            </a>
        </div>
    </form>

    @include('admin.estudiantes._tutores', [
        'estudiante' => $estudiante,
        'tutoresVinculados' => $tutoresVinculados,
        'tutoresDisponibles' => $tutoresDisponibles,
    ])

    @include('admin.estudiantes._trayectoria_ue', [
        'estudiante' => $estudiante,
        'trayectoriaPostulaciones' => $trayectoriaPostulaciones,
    ])
@endsection
