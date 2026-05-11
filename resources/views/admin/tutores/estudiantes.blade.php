@extends('layouts.dashboard')

@section('title', 'Estudiantes del tutor | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.usuarios.index') }}" class="hover:text-indigo-600">Usuarios</a>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Estudiantes del tutor</span>
@endsection

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs text-slate-400">Panel / Usuarios / Tutor</p>
            <h1 class="text-2xl font-bold text-slate-900">
                Estudiantes vinculados
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Tutor: <span class="font-semibold text-slate-700">
                    {{ $tutor->persona->nombres_per }} {{ $tutor->persona->ap_paterno_per }} {{ $tutor->persona->ap_materno_per }}
                </span>
            </p>
        </div>
        <a href="{{ route('admin.usuarios.index') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Volver a usuarios
        </a>
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

    {{-- Tabla de estudiantes vinculados --}}
    <div class="mb-8 overflow-hidden rounded-2xl bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-semibold text-slate-800">Estudiantes vinculados</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Nombre completo</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">CI</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Código</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-400">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vinculados as $estudiante)
                        <tr class="border-b border-slate-50 transition hover:bg-indigo-50/30 last:border-0">
                            <td class="px-6 py-4 font-medium text-slate-900">
                                {{ $estudiante->persona->nombres_per }}
                                {{ $estudiante->persona->ap_paterno_per }}
                                {{ $estudiante->persona->ap_materno_per }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $estudiante->persona->ci_per ?? '—' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $estudiante->codigo_est ?? '—' }}</td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST"
                                      action="{{ route('admin.tutores.estudiantes.detach', [$tutor, $estudiante]) }}"
                                      class="inline"
                                      onsubmit="return confirm('¿Desvincular a este estudiante del tutor?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-100">
                                        Desvincular
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-indigo-500">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-1a4 4 0 00-5-3.87M9 20H4v-1a4 4 0 015-3.87m0-6.13a4 4 0 110-8 4 4 0 010 8zm8 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </div>
                                <p class="mt-4 font-semibold text-slate-700">Sin estudiantes vinculados</p>
                                <p class="mt-2 text-sm text-slate-400">Usa el formulario de abajo para vincular estudiantes a este tutor.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Formulario de vinculación --}}
    <div class="max-w-xl rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-base font-semibold text-slate-800">Vincular estudiante</h2>

        @if($disponibles->isEmpty())
            <p class="text-sm text-slate-500">Todos los estudiantes registrados ya están vinculados a este tutor.</p>
        @else
            <form method="POST" action="{{ route('admin.tutores.estudiantes.attach', $tutor) }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                @csrf
                <div class="flex-1">
                    <label for="id_est" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Estudiante</label>
                    <select name="id_est" id="id_est"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        @foreach($disponibles as $est)
                            <option value="{{ $est->id_est }}">
                                {{ $est->persona->ci_per ? $est->persona->ci_per.' — ' : '' }}{{ $est->persona->nombres_per }} {{ $est->persona->ap_paterno_per }} {{ $est->persona->ap_materno_per }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_est')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:from-indigo-700 hover:to-purple-700">
                    Vincular
                </button>
            </form>
        @endif
    </div>
@endsection
