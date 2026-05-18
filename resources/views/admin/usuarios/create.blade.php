@extends('layouts.dashboard')

@section('title', 'Nuevo usuario | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span>Usuarios</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Crear</span>
@endsection

@section('content')
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <p class="text-xs text-slate-400">Panel / Usuarios</p>
            <h1 class="text-2xl font-bold text-slate-900">Crear nuevo usuario</h1>
            <p class="mt-1 text-sm text-slate-500">Completa los pasos: datos personales, cuenta y confirmación.</p>
        </div>
        <a href="{{ route('admin.usuarios.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
            Volver al listado
        </a>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm md:p-8">
        @include('admin.usuarios._wizard-form', [
            'usuario' => null,
            'roles' => $roles,
            'unidades' => $unidades,
            'action' => route('admin.usuarios.store'),
            'submitLabel' => 'Crear usuario',
            'cancelUrl' => route('admin.usuarios.index'),
        ])
    </div>
@endsection
