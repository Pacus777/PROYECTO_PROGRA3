@extends('layouts.dashboard')

@section('title', 'Unidades educativas | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Unidades educativas</span>
@endsection

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs text-slate-400">Panel / Unidades educativas</p>
            <h1 class="text-2xl font-bold text-slate-900">Unidades educativas</h1>
            <p class="mt-1 text-sm text-slate-500">Instituciones registradas en el sistema.</p>
        </div>
        <a href="{{ route('admin.unidades.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:from-indigo-700 hover:to-purple-700">
            Nueva unidad
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Nombre</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Código</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Dirección</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-400">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($unidades as $unidad)
                        <tr class="border-b border-slate-50 transition hover:bg-indigo-50/30 last:border-0">
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $unidad->nombre_ued }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $unidad->codigo_ued ?? '—' }}</td>
                            <td class="max-w-xs truncate px-6 py-4 text-slate-600">{{ $unidad->direccion_ued ?? '—' }}</td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.unidades.show', $unidad) }}" class="mr-1 inline-flex rounded-lg bg-slate-100 p-2 text-slate-500 transition hover:bg-indigo-100 hover:text-indigo-600">Ver</a>
                                <a href="{{ route('admin.unidades.edit', $unidad) }}" class="mr-1 inline-flex rounded-lg bg-slate-100 p-2 text-slate-500 transition hover:bg-indigo-100 hover:text-indigo-600">Editar</a>
                                <form method="POST" action="{{ route('admin.unidades.destroy', $unidad) }}" class="inline" onsubmit="return confirm('¿Eliminar esta unidad? Se eliminarán ofertas vinculadas si aplica.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex rounded-lg bg-red-50 px-2.5 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-indigo-500">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 21h16M6 21V7l6-4 6 4v14"/></svg>
                                </div>
                                <p class="mt-4 font-semibold text-slate-700">Sin datos aún</p>
                                <p class="mt-2 text-sm text-slate-400">No hay unidades registradas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($unidades->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">{{ $unidades->links() }}</div>
        @endif
    </div>
@endsection
