@extends('layouts.dashboard')

@section('title', 'Editar gestión | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span>Gestiones</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Editar</span>
@endsection

@section('content')
    <div class="mb-8">
        <p class="text-xs text-slate-400">Panel / Gestiones</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900">Editar gestión</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $gestion->nombre_ges }}</p>
    </div>

    <form method="POST" action="{{ route('admin.gestiones.update', $gestion) }}" class="max-w-2xl rounded-2xl bg-white p-6 shadow-sm md:p-8">
        @csrf
        @method('PUT')

        <div class="space-y-5">
            <div>
                <label for="nombre_ges" class="mb-1.5 block text-xs font-semibold text-slate-600 uppercase tracking-wide">Nombre</label>
                <input type="text" name="nombre_ges" id="nombre_ges" value="{{ old('nombre_ges', $gestion->nombre_ges) }}" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                @error('nombre_ges')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="fecha_ini_ges" class="mb-1.5 block text-xs font-semibold text-slate-600 uppercase tracking-wide">Fecha inicio</label>
                    <input type="date" name="fecha_ini_ges" id="fecha_ini_ges" value="{{ old('fecha_ini_ges', optional($gestion->fecha_ini_ges)->format('Y-m-d')) }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    @error('fecha_ini_ges')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="fecha_fin_ges" class="mb-1.5 block text-xs font-semibold text-slate-600 uppercase tracking-wide">Fecha fin</label>
                    <input type="date" name="fecha_fin_ges" id="fecha_fin_ges" value="{{ old('fecha_fin_ges', optional($gestion->fecha_fin_ges)->format('Y-m-d')) }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    @error('fecha_fin_ges')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                <label class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Marcar como activa</p>
                        <p class="mt-0.5 text-xs text-slate-500">Si activas esta gestión, las demás se desactivarán automáticamente.</p>
                    </div>
                    <input type="checkbox" name="activa_ges" id="activa_ges" value="1" {{ old('activa_ges', $gestion->activa_ges) ? 'checked' : '' }}
                           class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-300">
                </label>
                @error('activa_ges')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:from-indigo-700 hover:to-purple-700">Actualizar</button>
            <a href="{{ route('admin.gestiones.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancelar</a>
        </div>
    </form>
@endsection
