@extends('layouts.dashboard')

@section('title', 'Editar tipo de documento')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span>Tipos documento</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Editar</span>
@endsection

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Editar tipo de documento</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $tipo->nombre_tdo }}</p>
    </div>

    <form method="POST" action="{{ route('admin.tipos-documento.update', $tipo) }}" class="max-w-2xl rounded-2xl bg-white p-6 shadow-sm md:p-8">
        @csrf @method('PUT')
        <div>
            <label for="nombre_tdo" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Nombre</label>
            <input type="text" name="nombre_tdo" id="nombre_tdo" value="{{ old('nombre_tdo', $tipo->nombre_tdo) }}" required
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
            @error('nombre_tdo')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div class="mt-8 flex flex-wrap gap-3">
            <button type="submit" class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-700">Actualizar</button>
            <a href="{{ route('admin.tipos-documento.index') }}" class="rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancelar</a>
        </div>
    </form>
@endsection
