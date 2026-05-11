@extends('layouts.dashboard')

@section('title', 'Gestiones | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Gestiones</span>
@endsection

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs text-slate-400">Panel / Gestiones</p>
            <h1 class="text-2xl font-bold text-slate-900">Gestiones</h1>
            <p class="mt-1 text-sm text-slate-500">Períodos globales del sistema (solo una puede estar activa).</p>
        </div>
        <a href="{{ route('admin.gestiones.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:from-indigo-700 hover:to-purple-700">
            Nueva gestión
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Nombre</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Inicio</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Fin</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Estado</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-400">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gestiones as $gestion)
                        <tr class="border-b border-slate-50 transition hover:bg-indigo-50/30 last:border-0">
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $gestion->nombre_ges }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $gestion->fecha_ini_ges?->format('Y-m-d') ?? '—' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $gestion->fecha_fin_ges?->format('Y-m-d') ?? '—' }}</td>
                            <td class="px-6 py-4">
                                @if($gestion->activa_ges)
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Activa</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Inactiva</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.gestiones.edit', $gestion) }}" class="mr-1 inline-flex rounded-lg bg-slate-100 p-2 text-slate-500 transition hover:bg-indigo-100 hover:text-indigo-600">Editar</a>
                                <form method="POST" action="{{ route('admin.gestiones.destroy', $gestion) }}" class="inline" onsubmit="return confirm('¿Eliminar esta gestión?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex rounded-lg bg-red-50 px-2.5 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-indigo-500">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m4-4H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="mt-4 font-semibold text-slate-700">Sin datos aún</p>
                                <p class="mt-2 text-sm text-slate-400">No hay gestiones registradas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($gestiones->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">{{ $gestiones->links() }}</div>
        @endif
    </div>
@endsection
