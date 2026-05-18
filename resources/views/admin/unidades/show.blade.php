@extends('layouts.dashboard')

@section('title', $unidad->nombre_ued.' | Unidad')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span>Unidades educativas</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Detalle</span>
@endsection

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="text-xs text-slate-400">Panel / Unidades educativas</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">{{ $unidad->nombre_ued }}</h1>
            @if($unidad->codigo_ued)
                <p class="mt-1 text-sm text-slate-500">Código UE: <span class="font-mono font-semibold text-indigo-700">{{ $unidad->codigo_ued }}</span> <span class="text-slate-400">(identifica al colegio, no al estudiante)</span></p>
            @endif
        </div>
        <a href="{{ route('admin.unidades.edit', $unidad) }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 transition hover:bg-slate-50">Editar</a>
    </div>

    <div class="mb-6 grid max-w-3xl gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-4">
            <p class="text-xs font-semibold uppercase text-indigo-600">Estudiantes matriculados</p>
            <p class="mt-1 text-2xl font-bold text-indigo-900">{{ $unidad->estudiantes_matriculados_count ?? 0 }}</p>
            <p class="mt-1 text-xs text-indigo-700">Con matrícula actual en esta UE</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase text-slate-500">Ofertas académicas</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $unidad->ofertas_academicas_count ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase text-slate-500">Admins institucionales</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $unidad->usuarios_count ?? 0 }}</p>
        </div>
    </div>

    @include('admin.unidades._matriculados', ['matriculados' => $matriculados])

    <div class="mb-6 max-w-2xl rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-400">Ubicación territorial</h2>
        <dl class="space-y-2 text-sm">
            <div><dt class="text-slate-500">Departamento</dt><dd class="font-medium">{{ $unidad->municipio?->provincia?->departamento?->nombre_dep ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Provincia</dt><dd>{{ $unidad->municipio?->provincia?->nombre_prov ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Municipio</dt><dd>{{ $unidad->municipio?->nombre_mun ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Distrito educativo</dt><dd>{{ $unidad->distritoEducativo?->nombre_dis ?? '—' }}</dd></div>
        </dl>
    </div>

    <div class="max-w-2xl rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-xs font-bold uppercase tracking-wide text-slate-400">Dirección y mapa</h2>
        <x-admin.address-location-picker
            :address="$unidad->direccion_ued"
            :lat="$unidad->lat_ued"
            :lng="$unidad->lng_ued"
            :readonly="true"
        />
    </div>

    <form method="POST" action="{{ route('admin.unidades.destroy', $unidad) }}" class="mt-8" onsubmit="return confirm('¿Eliminar esta unidad educativa?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-xl bg-red-50 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-red-100">Eliminar unidad</button>
    </form>
@endsection
