@extends('layouts.dashboard')

@section('title', 'Nuevo tipo de documento')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span>Tipos documento</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Crear</span>
@endsection

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Nuevo tipo de documento</h1>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm md:p-8">
        <x-ui.form-wizard
            :steps="['Datos', 'Confirmar']"
            :action="route('admin.tipos-documento.store')"
            submit-label="Guardar"
            :cancel-url="route('admin.tipos-documento.index')"
        >
            <x-ui.form-wizard-step :index="0" title="Tipo de documento">
                <div>
                    <label for="nombre_tdo" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Nombre</label>
                    <input type="text" name="nombre_tdo" id="nombre_tdo" value="{{ old('nombre_tdo') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    @error('nombre_tdo')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </x-ui.form-wizard-step>
            <x-ui.form-wizard-step :index="1" title="Confirmar">
                <p class="text-sm text-slate-600">Se usará en la carga documental de postulaciones.</p>
            </x-ui.form-wizard-step>
        </x-ui.form-wizard>
    </div>
@endsection
