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
                <p class="mt-1 text-sm text-slate-500">Código: <span class="font-mono font-semibold text-slate-700">{{ $unidad->codigo_ued }}</span></p>
            @endif
        </div>
        <a href="{{ route('admin.unidades.edit', $unidad) }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 transition hover:bg-slate-50">Editar</a>
    </div>

    <div class="max-w-2xl rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-400">Dirección</h2>
        <p class="text-slate-800 whitespace-pre-line">{{ $unidad->direccion_ued ?: 'Sin dirección registrada.' }}</p>
        <p class="mt-6 text-sm text-slate-500">Usuarios asociados: <span class="font-semibold text-slate-800">{{ $unidad->usuarios_count ?? 0 }}</span></p>
    </div>

    <form method="POST" action="{{ route('admin.unidades.destroy', $unidad) }}" class="mt-8" onsubmit="return confirm('¿Eliminar esta unidad educativa?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-xl bg-red-50 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-red-100">Eliminar unidad</button>
    </form>
@endsection
