@extends('layouts.dashboard')

@section('title', 'Estados de postulación')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Estados postulación</span>
@endsection

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Estados de postulación</h1>
            <p class="mt-1 text-sm text-slate-500">Catálogo global del flujo de admisión.</p>
        </div>
        <a href="{{ route('admin.estados-postulacion.create') }}" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Nuevo estado</a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ session('error') }}</div>
    @endif

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b bg-slate-50 text-left">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Nombre</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Descripción</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-slate-400">En uso</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-400">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($estados as $estado)
                    <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $estado->nombre_ept }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $estado->descripcion_ept ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">{{ $estado->postulaciones_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.estados-postulacion.edit', $estado) }}" class="mr-2 text-xs font-semibold text-indigo-600">Editar</a>
                            <form method="POST" action="{{ route('admin.estados-postulacion.destroy', $estado) }}" class="inline" onsubmit="return confirm('¿Eliminar este estado?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-rose-600">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">Sin estados registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
