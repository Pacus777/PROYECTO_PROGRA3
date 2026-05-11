@extends('layouts.dashboard')

@section('title', 'Tutores | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Tutores</span>
@endsection

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs text-slate-400">Panel / Tutores</p>
            <h1 class="text-2xl font-bold text-slate-900">Tutores</h1>
            <p class="mt-1 text-sm text-slate-500">Gestiona los vínculos entre tutores y sus estudiantes.</p>
        </div>
        <a href="{{ route('admin.usuarios.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:from-indigo-700 hover:to-purple-700">
            Nuevo usuario tutor
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Nombre</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">CI</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wide text-slate-400">Estudiantes</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-400">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tutores as $tutor)
                        @php
                            $p = $tutor->persona;
                            $nombre = trim(($p->nombres_per ?? '').' '.($p->ap_paterno_per ?? '').' '.($p->ap_materno_per ?? ''));
                        @endphp
                        <tr class="border-b border-slate-50 transition hover:bg-indigo-50/30 last:border-0">
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $nombre ?: '—' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $p->ci_per ?? '—' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold
                                    {{ $tutor->estudiantes_count > 0 ? 'bg-teal-50 text-teal-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $tutor->estudiantes_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.tutores.estudiantes.index', $tutor) }}"
                                   class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">
                                    Gestionar estudiantes
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-indigo-500">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <p class="mt-4 font-semibold text-slate-700">Sin tutores registrados</p>
                                <p class="mt-2 text-sm text-slate-400">Crea un usuario con rol tutor para que aparezca aquí.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tutores->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">{{ $tutores->links() }}</div>
        @endif
    </div>
@endsection
