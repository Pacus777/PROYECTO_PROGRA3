@extends('layouts.dashboard')

@section('title', 'Nuevo usuario | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
    </svg>
    <span>Usuarios</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="font-medium text-slate-500">Crear</span>
@endsection

@section('content')
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <p class="text-xs text-slate-400">Panel / Usuarios</p>
            <h1 class="text-2xl font-bold text-slate-900">Crear nuevo usuario</h1>
            <p class="mt-1 text-sm text-slate-500">Se crea la persona y la cuenta de acceso.</p>
        </div>
        <a href="{{ route('admin.usuarios.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-800">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver al listado
        </a>
    </div>

    <div class="overflow-hidden rounded-3xl bg-white shadow-sm">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
            <div class="flex items-center gap-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white/20 text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7a3 3 0 11-6 0 3 3 0 016 0zM6 21v-1a4 4 0 014-4h4a4 4 0 014 4v1M7 11h1m6 0h3"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-xl font-bold text-white">Crear nuevo usuario</h2>
                    <p class="mt-1 text-sm text-white/80">Completa los campos para registrar un nuevo usuario.</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.usuarios.store') }}" class="px-8 py-8" x-data="{ loading: false, showPass: false, strength: 0 }">
            @csrf

            @include('admin.usuarios._form', ['usuario' => null, 'roles' => $roles, 'unidades' => $unidades])

            {{-- Indicador visual de seguridad para contraseña --}}
            <div class="mt-4">
                <div class="flex gap-1">
                    <div class="h-1.5 flex-1 rounded-full transition" :class="strength >= 1 ? 'bg-red-400' : 'bg-slate-200'"></div>
                    <div class="h-1.5 flex-1 rounded-full transition" :class="strength >= 2 ? 'bg-amber-400' : 'bg-slate-200'"></div>
                    <div class="h-1.5 flex-1 rounded-full transition" :class="strength >= 3 ? 'bg-blue-400' : 'bg-slate-200'"></div>
                    <div class="h-1.5 flex-1 rounded-full transition" :class="strength >= 4 ? 'bg-emerald-400' : 'bg-slate-200'"></div>
                </div>
                <p class="mt-2 text-xs text-slate-500">
                    Seguridad:
                    <span class="font-semibold" x-text="['Muy débil','Débil','Regular','Buena','Fuerte'][strength]"></span>
                </p>
            </div>

            <div class="mt-8 border-t border-slate-100 pt-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ route('admin.usuarios.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 transition hover:text-slate-800">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        @click="loading = true"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition duration-200 hover:scale-105 hover:from-indigo-700 hover:to-purple-700 active:scale-95"
                    >
                        <svg x-show="loading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <svg x-show="!loading" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 7a3 3 0 11-6 0 3 3 0 016 0zM6 21v-1a4 4 0 014-4h4a4 4 0 014 4v1M7 11h1m6 0h3"/>
                        </svg>
                        Crear usuario
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            const input = document.getElementById('password_usu');
            if (!input) return;
            input.addEventListener('input', (e) => {
                const v = e.target.value || '';
                let score = 0;
                if (v.length >= 6) score++;
                if (/[A-Z]/.test(v) || /[a-z]/.test(v)) score++;
                if (/\d/.test(v)) score++;
                if (/[^A-Za-z0-9]/.test(v) || v.length >= 10) score++;
                const root = input.closest('[x-data]');
                if (root && root.__x) {
                    root.__x.$data.strength = Math.min(score, 4);
                }
            });
        });
    </script>
@endsection
