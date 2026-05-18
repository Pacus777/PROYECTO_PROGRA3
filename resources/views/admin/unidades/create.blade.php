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
        <p class="mt-1 text-sm text-slate-500">Registro del colegio en el catálogo central.</p>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm md:p-8">
        <x-ui.form-wizard
            :steps="['Datos del colegio', 'Ubicación']"
            :action="route('admin.unidades.store')"
            submit-label="Guardar unidad"
            :cancel-url="route('admin.unidades.index')"
        >
            <x-ui.form-wizard-step :index="0" title="Identificación">
                <div class="space-y-5">
                    <div>
                        <label for="nombre_ued" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Nombre</label>
                        <input type="text" name="nombre_ued" id="nombre_ued" value="{{ old('nombre_ued') }}" required
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        @error('nombre_ued')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="codigo_ued" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Código UE</label>
                        <p class="mb-1.5 text-xs text-slate-500">Identifica al colegio (no al RUDE del estudiante).</p>
                        <input type="text" name="codigo_ued" id="codigo_ued" value="{{ old('codigo_ued') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        @error('codigo_ued')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </x-ui.form-wizard-step>

            <x-ui.form-wizard-step :index="1" title="Territorio y dirección">
                <div class="space-y-5">
                    <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                        <p class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">Ubicación territorial</p>
                        <x-admin.filtro-territorio :departamentos="$departamentos" mode="form" :show-unidad="false" />
                        @error('id_mun_ued')<p class="mt-2 text-xs text-rose-600">{{ $message }}</p>@enderror
                        @error('id_dis_ued')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <x-admin.address-location-picker
                        :address="old('direccion_ued')"
                        :lat="old('lat_ued')"
                        :lng="old('lng_ued')"
                    />
                </div>
            </x-ui.form-wizard-step>
        </x-ui.form-wizard>
    </div>
@endsection
