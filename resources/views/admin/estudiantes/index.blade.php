@extends('layouts.dashboard')

@section('title', 'Estudiantes | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Estudiantes</span>
@endsection

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs text-slate-400">Panel / Estudiantes</p>
            <h1 class="text-2xl font-bold text-slate-900">Estudiantes</h1>
            <p class="mt-1 text-sm text-slate-500">El código de estudiante es el que el tutor usa para vincularse.</p>
        </div>
        <a href="{{ route('admin.estudiantes.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:from-indigo-700 hover:to-purple-700">
            Nuevo estudiante
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{{ session('error') }}</div>
    @endif

    {{-- Búsqueda --}}
    <form method="GET" action="{{ route('admin.estudiantes.index') }}" class="mb-6 flex gap-3">
        <input type="text" name="q" value="{{ $search }}"
               placeholder="Buscar por nombre, CI o código…"
               class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
        <button type="submit"
                class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
            Buscar
        </button>
        @if($search)
            <a href="{{ route('admin.estudiantes.index') }}"
               class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                Limpiar
            </a>
        @endif
    </form>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Nombre</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">CI</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Código
                            <span class="ml-1 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold text-amber-700">tutor usa esto</span>
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-400">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($estudiantes as $est)
                        @php
                            $p = $est->persona;
                            $nombre = trim(($p->nombres_per ?? '').' '.($p->ap_paterno_per ?? '').' '.($p->ap_materno_per ?? ''));
                        @endphp
                        <tr class="border-b border-slate-50 transition hover:bg-indigo-50/30 last:border-0">
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $nombre ?: '—' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $p->ci_per ?? '—' }}</td>
                            <td class="px-6 py-4">
                                @if($est->codigo_est)
                                    <code class="rounded bg-indigo-50 px-2 py-1 text-xs font-bold text-indigo-700">{{ $est->codigo_est }}</code>
                                @else
                                    <span class="text-xs text-rose-500 font-medium">Sin código</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.estudiantes.edit', $est) }}"
                                   class="mr-1 inline-flex rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-indigo-100 hover:text-indigo-700">
                                    Editar
                                </a>
                                <form method="POST" action="{{ route('admin.estudiantes.destroy', $est) }}"
                                      class="inline" onsubmit="return confirm('¿Eliminar este estudiante?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex rounded-lg bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-100">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-indigo-500">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6"/></svg>
                                </div>
                                <p class="mt-4 font-semibold text-slate-700">
                                    {{ $search ? 'Sin resultados para "'.$search.'"' : 'Sin estudiantes registrados' }}
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($estudiantes->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">{{ $estudiantes->links() }}</div>
        @endif
    </div>
@endsection
