@extends('layouts.dashboard')

@section('title', 'Nueva gestión | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span>Gestiones</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Crear</span>
@endsection

@section('content')
    <div class="mb-8">
        <p class="text-xs text-slate-400">Panel / Gestiones</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900">Nueva gestión</h1>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm md:p-8">
        <x-ui.form-wizard
            :steps="['Datos del período', 'Activación']"
            :action="route('admin.gestiones.store')"
            submit-label="Guardar gestión"
            :cancel-url="route('admin.gestiones.index')"
        >
            <x-ui.form-wizard-step :index="0" title="Período académico">
                <div class="space-y-5">
                    <div>
                        <label for="nombre_ges" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Nombre</label>
                        <input type="text" name="nombre_ges" id="nombre_ges" value="{{ old('nombre_ges') }}" required
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        @error('nombre_ges')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label for="fecha_ini_ges" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Fecha inicio gestión</label>
                            <input type="date" name="fecha_ini_ges" id="fecha_ini_ges" value="{{ old('fecha_ini_ges') }}"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            @error('fecha_ini_ges')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="fecha_fin_ges" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Fecha fin gestión</label>
                            <input type="date" name="fecha_fin_ges" id="fecha_fin_ges" value="{{ old('fecha_fin_ges') }}"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            @error('fecha_fin_ges')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-5">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-indigo-650 mb-3">Cronograma Global de Postulaciones</h3>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label for="fecha_inicio_postulacion_ges" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Inicio de postulación (Nacional)</label>
                                <input type="datetime-local" name="fecha_inicio_postulacion_ges" id="fecha_inicio_postulacion_ges" value="{{ old('fecha_inicio_postulacion_ges') }}"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                @error('fecha_inicio_postulacion_ges')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="fecha_fin_postulacion_ges" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Cierre de postulación (Nacional)</label>
                                <input type="datetime-local" name="fecha_fin_postulacion_ges" id="fecha_fin_postulacion_ges" value="{{ old('fecha_fin_postulacion_ges') }}"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                @error('fecha_fin_postulacion_ges')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </x-ui.form-wizard-step>

            <x-ui.form-wizard-step :index="1" title="Estado de la gestión">
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <label class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Marcar como activa</p>
                            <p class="mt-0.5 text-xs text-slate-500">Si activas esta gestión, las demás se desactivarán automáticamente.</p>
                        </div>
                        <input type="checkbox" name="activa_ges" id="activa_ges" value="1" {{ old('activa_ges') ? 'checked' : '' }}
                               class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-300">
                    </label>
                    @error('activa_ges')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </x-ui.form-wizard-step>
        </x-ui.form-wizard>
    </div>
@endsection
