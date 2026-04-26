@extends('layouts.dashboard')

@section('title', 'Nueva unidad educativa | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span>Unidades educativas</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Crear</span>
@endsection

@section('content')
    <div class="mb-8">
        <p class="text-xs text-slate-400">Panel / Unidades educativas</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900">Nueva unidad educativa</h1>
    </div>

    <form method="POST" action="{{ route('admin.unidades.store') }}" class="max-w-2xl rounded-2xl bg-white p-6 shadow-sm md:p-8">
        @csrf
        <div class="space-y-5">
            <div>
                <label for="nombre_ued" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Nombre</label>
                <input type="text" name="nombre_ued" id="nombre_ued" value="{{ old('nombre_ued') }}" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                @error('nombre_ued')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="codigo_ued" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Código (único, opcional)</label>
                <input type="text" name="codigo_ued" id="codigo_ued" value="{{ old('codigo_ued') }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                @error('codigo_ued')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="direccion_ued" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Dirección</label>
                <textarea name="direccion_ued" id="direccion_ued" rows="3"
                          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">{{ old('direccion_ued') }}</textarea>
                @error('direccion_ued')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="mt-8 flex flex-wrap gap-3">
            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:from-indigo-700 hover:to-purple-700">Guardar</button>
            <a href="{{ route('admin.unidades.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancelar</a>
        </div>
    </form>
@endsection
