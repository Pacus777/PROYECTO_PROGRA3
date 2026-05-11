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
    </div>

    <form method="POST" action="{{ route('admin.estudiantes.store') }}"
          class="max-w-2xl rounded-2xl bg-white p-6 shadow-sm md:p-8">
        @csrf

        {{-- Código (destacado) --}}
        <div class="mb-6 rounded-xl border-2 border-indigo-200 bg-indigo-50 p-4">
            <label for="codigo_est" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-indigo-700">
                Código del estudiante
                <span class="ml-1 font-normal text-indigo-500">(el tutor usará este código para vincularse)</span>
            </label>
            <input type="text" name="codigo_est" id="codigo_est" value="{{ old('codigo_est') }}"
                   placeholder="Ej: EST-2025-001"
                   class="w-full rounded-xl border border-indigo-200 bg-white px-4 py-3 text-sm font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-400">
            @error('codigo_est')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Nombres *</label>
                    <input type="text" name="nombres_per" value="{{ old('nombres_per') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    @error('nombres_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Ap. Paterno *</label>
                    <input type="text" name="ap_paterno_per" value="{{ old('ap_paterno_per') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    @error('ap_paterno_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Ap. Materno</label>
                    <input type="text" name="ap_materno_per" value="{{ old('ap_materno_per') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">CI</label>
                    <input type="text" name="ci_per" value="{{ old('ci_per') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nac_per" value="{{ old('fecha_nac_per') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Género</label>
                    <select name="genero_per"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <option value="">—</option>
                        <option value="M" {{ old('genero_per') === 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('genero_per') === 'F' ? 'selected' : '' }}>Femenino</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Correo</label>
                    <input type="email" name="correo_per" value="{{ old('correo_per') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Teléfono</label>
                    <input type="text" name="telefono_per" value="{{ old('telefono_per') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
            <button type="submit"
                    class="rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:from-indigo-700 hover:to-purple-700">
                Guardar
            </button>
            <a href="{{ route('admin.estudiantes.index') }}"
               class="rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Cancelar
            </a>
        </div>
    </form>
@endsection
