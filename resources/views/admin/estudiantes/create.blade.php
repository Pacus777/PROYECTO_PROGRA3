@extends('layouts.dashboard')

@section('title', 'Nuevo estudiante | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.estudiantes.index') }}" class="hover:text-indigo-600">Estudiantes</a>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Nuevo</span>
@endsection

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Nuevo estudiante</h1>
        <p class="mt-1 text-sm text-slate-500">Alta por pasos: identificación, datos personales y confirmación.</p>
    </div>

    <x-alert-sin-cuenta-estudiante />

    <div class="rounded-2xl bg-white p-6 shadow-sm md:p-8">
        <x-ui.form-wizard
            :steps="['Identificación', 'Datos personales', 'Confirmar']"
            :action="route('admin.estudiantes.store')"
            submit-label="Guardar estudiante"
            :cancel-url="route('admin.estudiantes.index')"
        >
            <x-ui.form-wizard-step :index="0" title="RUDE y matrícula">
                @include('admin.estudiantes._identificadores', ['estudiante' => null])
                @include('admin.estudiantes._matricula_ue', ['estudiante' => null, 'unidades' => $unidades])
            </x-ui.form-wizard-step>

            <x-ui.form-wizard-step :index="1" title="Datos del postulante">
                @include('admin.estudiantes._form-personal', ['estudiante' => null])
            </x-ui.form-wizard-step>

            <x-ui.form-wizard-step :index="2" title="Revisar">
                <p class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-sm text-slate-600">
                    El postulante <strong>no tendrá cuenta de acceso</strong>. Un tutor podrá vincularlo con el RUDE desde su panel.
                </p>
            </x-ui.form-wizard-step>
        </x-ui.form-wizard>
    </div>
@endsection
