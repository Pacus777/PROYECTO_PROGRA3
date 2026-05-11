@extends('layouts.dashboard')

@section('title', 'Tutor | Mis estudiantes')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Mis estudiantes</span>
@endsection

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs text-slate-400">Tutor / Mis estudiantes</p>
            <h1 class="text-2xl font-bold text-slate-900">Mis estudiantes</h1>
            <p class="mt-1 text-sm text-slate-500">Ingresa el código del estudiante para vincularlo a tu cuenta.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    @if($tutor === null)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-amber-900">
            <p class="font-semibold">No hay perfil de tutor asociado.</p>
            <p class="mt-1 text-sm text-amber-800">Contacta a un administrador para activar tu perfil de tutor.</p>
        </div>
    @else

        {{-- Formulario de vinculación por código --}}
        <div class="mb-8 rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="mb-1 text-base font-semibold text-slate-800">Vincular estudiante por código</h2>
            <p class="mb-4 text-xs text-slate-500">El código es el identificador único del estudiante (<code class="rounded bg-slate-100 px-1">codigo_est</code>). Si no lo tienes, consulta con la institución.</p>

            <form method="POST" action="{{ route('tutor.estudiantes.store') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                @csrf
                <div class="flex-1">
                    <label for="codigo_est" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Código del estudiante</label>
                    <input type="text"
                           name="codigo_est"
                           id="codigo_est"
                           value="{{ old('codigo_est') }}"
                           placeholder="Ej: EST-2025-001"
                           autocomplete="off"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-300">
                    @error('codigo_est')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:from-teal-700 hover:to-emerald-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Vincular
                </button>
            </form>
        </div>

        {{-- Tabla de estudiantes vinculados --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-800">
                    Estudiantes vinculados
                    <span class="ml-2 rounded-full bg-teal-100 px-2.5 py-0.5 text-xs font-bold text-teal-700">
                        {{ $tutor->estudiantes->count() }}
                    </span>
                </h2>
            </div>

            @if($tutor->estudiantes->isEmpty())
                <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-1a4 4 0 00-5-3.87M9 20H4v-1a4 4 0 015-3.87m0-6.13a4 4 0 110-8 4 4 0 010 8zm8 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <p class="mt-4 font-semibold text-slate-700">Sin estudiantes vinculados</p>
                    <p class="mt-2 text-sm text-slate-400">Ingresa el código de un estudiante arriba para vincularlo.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-slate-100 bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Nombre</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Código</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">CI</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-400">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($tutor->estudiantes as $est)
                                @php
                                    $p = $est->persona;
                                    $nombre = trim(($p->nombres_per ?? '').' '.($p->ap_paterno_per ?? '').' '.($p->ap_materno_per ?? ''));
                                @endphp
                                <tr class="transition hover:bg-teal-50/30">
                                    <td class="px-6 py-4 font-medium text-slate-900">{{ $nombre ?: '—' }}</td>
                                    <td class="px-6 py-4">
                                        @if($est->codigo_est)
                                            <code class="rounded bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ $est->codigo_est }}</code>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">{{ $p->ci_per ?? '—' }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST"
                                              action="{{ route('tutor.estudiantes.destroy', $est) }}"
                                              class="inline"
                                              onsubmit="return confirm('¿Desvincular a {{ $nombre ?: 'este estudiante' }} de tu perfil?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-100">
                                                Desvincular
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
@endsection
