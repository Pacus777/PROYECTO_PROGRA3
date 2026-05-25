@extends('layouts.dashboard')

@section('title', 'Tutor | Mis estudiantes')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Mis estudiantes</span>
@endsection

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between animate-fadeInUp">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Tutor / Portal Familiar</p>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight mt-1">Mis estudiantes</h1>
            <p class="mt-1.5 text-sm text-slate-550 font-light">Vincula postulantes con su RUDE o el código de la institución. Ellos no tienen usuario propio.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800 shadow-[0_4px_12px_rgba(16,185,129,0.03)] flex items-center gap-3 animate-fadeInUp">
            <svg class="h-5 w-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-800 shadow-[0_4px_12px_rgba(244,63,94,0.03)] flex items-center gap-3 animate-fadeInUp">
            <svg class="h-5 w-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    @if($tutor === null)
        <div class="rounded-2xl border border-amber-250 bg-amber-50/70 p-6 text-amber-900 shadow-[0_4px_15px_rgba(245,158,11,0.02)] flex gap-3.5 items-start animate-fadeInUp">
            <svg class="h-6 w-6 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p class="font-bold text-slate-800">No hay perfil de tutor asociado.</p>
                <p class="mt-1 text-sm text-slate-650 leading-relaxed font-light">Contacta a un administrador del sistema de admisiones para activar y vincular tu perfil de tutor.</p>
            </div>
        </div>
    @else

        {{-- Formulario de vinculación por código con Relieve --}}
        <div class="mb-8 rounded-2xl bg-gradient-to-b from-white to-[#F9FAFD] p-6 shadow-[0_15px_35px_rgba(99,102,241,0.05)] border border-indigo-100/30 animate-fadeInUp">
            <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3 mb-5">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100/50 shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </span>
                <h2 class="text-base font-bold text-slate-800">Vincular estudiante</h2>
            </div>
            <p class="mb-5 text-xs text-slate-500 font-light">Ingresa el <strong>RUDE</strong> del postulante (Registro Único de Estudiantes) o el código único institucional otorgado por la institución.</p>

            <form method="POST" action="{{ route('tutor.estudiantes.store') }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                @csrf
                <div class="flex-1">
                    <label for="codigo_est" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">RUDE o código de estudiante</label>
                    <input type="text"
                           name="codigo_est"
                           id="codigo_est"
                           value="{{ old('codigo_est') }}"
                           placeholder="Ej: 1234567890 (RUDE)"
                           autocomplete="off"
                           class="w-full rounded-xl border border-slate-200 bg-[#F8FAFC] px-4 py-3 text-sm text-slate-800 transition shadow-inner focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    @error('codigo_est')<p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-650 px-6 py-3.5 text-sm font-bold text-white shadow-md shadow-emerald-100/35 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-emerald-200/35">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Vincular estudiante
                </button>
            </form>
        </div>

        {{-- Tabla de estudiantes vinculados con Relieve --}}
        <div class="overflow-hidden rounded-2xl bg-gradient-to-b from-white to-[#F9FAFD] border border-slate-100/50 shadow-[0_15px_35px_rgba(148,163,184,0.06),0_1px_2px_rgba(0,0,0,0.005)] animate-fadeInUp">
            <div class="border-b border-slate-200 bg-slate-50/50 px-6 py-4.5">
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2.5">
                    Estudiantes vinculados
                    <span class="rounded-full bg-indigo-50 border border-indigo-200/60 px-2.5 py-0.5 text-xs font-black text-indigo-700 shadow-sm">
                        {{ $tutor->estudiantes->count() }}
                    </span>
                </h2>
            </div>

            @if($tutor->estudiantes->isEmpty())
                <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400 border border-slate-200/40 shadow-sm">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-1a4 4 0 00-5-3.87M9 20H4v-1a4 4 0 015-3.87m0-6.13a4 4 0 110-8 4 4 0 010 8zm8 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <p class="mt-4 font-bold text-slate-700">Sin estudiantes vinculados</p>
                    <p class="mt-2 text-sm text-slate-400 font-light">Ingresa el RUDE o código de un estudiante arriba para vincularlo a tu perfil.</p>
                </div>
            @else
                <div class="p-6">
                    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-slate-50/60 shadow-inner">
                        <table class="w-full text-sm text-left">
                            <thead class="border-b border-slate-200 bg-slate-100/80 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                <tr>
                                    <th class="px-6 py-4.5">Nombre del estudiante</th>
                                    <th class="px-6 py-4.5">Código RUDE</th>
                                    <th class="px-6 py-4.5">Código interno</th>
                                    <th class="px-6 py-4.5">Cédula Identidad (CI)</th>
                                    <th class="px-6 py-4.5 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @foreach($tutor->estudiantes as $est)
                                    @php
                                        $p = $est->persona;
                                        $nombre = trim(($p->nombres_per ?? '').' '.($p->ap_paterno_per ?? '').' '.($p->ap_materno_per ?? ''));
                                    @endphp
                                    <tr class="text-slate-650 transition-colors hover:bg-slate-50/40">
                                        <td class="px-6 py-4.5 font-bold text-slate-850">{{ $nombre ?: '—' }}</td>
                                        <td class="px-6 py-4.5">
                                            @if($est->rude_est)
                                                <code class="rounded-lg bg-emerald-50 border border-emerald-200/60 px-2.5 py-1 text-xs font-bold text-emerald-800 font-mono">{{ $est->rude_est }}</code>
                                            @else
                                                <span class="text-slate-400 font-light">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4.5">
                                            @if($est->codigo_est)
                                                <code class="rounded-lg bg-slate-50 border border-slate-200/60 px-2.5 py-1 text-xs font-semibold text-slate-700 font-mono">{{ $est->codigo_est }}</code>
                                            @else
                                                <span class="text-slate-400 font-light">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4.5 text-slate-500 font-light">{{ $p->ci_per ?? '—' }}</td>
                                        <td class="px-6 py-4.5 text-right">
                                            <form method="POST"
                                                  action="{{ route('tutor.estudiantes.destroy', $est) }}"
                                                  class="inline"
                                                  onsubmit="return confirm('¿Desvincular a {{ $nombre ?: 'este estudiante' }} de tu perfil?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 rounded-xl bg-rose-50 border border-rose-200 px-3.5 py-2 text-xs font-bold text-rose-600 transition-all duration-200 hover:bg-rose-100 hover:text-rose-700">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Desvincular
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @endif
@endsection
