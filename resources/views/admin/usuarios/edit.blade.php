@extends('layouts.dashboard')

@section('title', 'Editar usuario | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span>Usuarios</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Editar</span>
@endsection

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs text-slate-400">Panel / Usuarios</p>
            <h1 class="text-2xl font-bold text-slate-900">Editar usuario</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $usuario->correo_usu }}</p>
        </div>
        <a href="{{ route('admin.usuarios.show', $usuario) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Volver al detalle
        </a>
    </div>

    <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}" class="rounded-2xl bg-white p-6 md:p-8 shadow-sm">
        @csrf
        @method('PUT')
        @include('admin.usuarios._form', ['usuario' => $usuario, 'roles' => $roles, 'unidades' => $unidades])

        <div class="mt-8 flex flex-wrap gap-3">
            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:from-indigo-700 hover:to-purple-700">
                Actualizar
            </button>
            <a href="{{ route('admin.usuarios.show', $usuario) }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancelar</a>
        </div>
    </form>
@endsection
