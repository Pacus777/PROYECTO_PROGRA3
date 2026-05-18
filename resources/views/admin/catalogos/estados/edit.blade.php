@extends('layouts.dashboard')

@section('title', 'Editar estado | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span>Estados</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Editar</span>
@endsection

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Editar estado</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $estado->nombre_ept }}</p>
    </div>

    <form method="POST" action="{{ route('admin.estados-postulacion.update', $estado) }}" class="max-w-2xl rounded-2xl bg-white p-6 shadow-sm md:p-8">
        @csrf @method('PUT')
        <div class="space-y-5">
            <div>
                <label for="nombre_ept" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Nombre</label>
                <input type="text" name="nombre_ept" id="nombre_ept" value="{{ old('nombre_ept', $estado->nombre_ept) }}" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                @error('nombre_ept')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="descripcion_ept" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Descripción</label>
                <textarea name="descripcion_ept" id="descripcion_ept" rows="3"
                          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">{{ old('descripcion_ept', $estado->descripcion_ept) }}</textarea>
                @error('descripcion_ept')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="mt-8 flex flex-wrap gap-3">
            <button type="submit" class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-700">Actualizar</button>
            <a href="{{ route('admin.estados-postulacion.index') }}" class="rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancelar</a>
        </div>
    </form>
@endsection
