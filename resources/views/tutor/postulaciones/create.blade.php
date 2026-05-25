@extends('layouts.dashboard')

@section('title', 'Tutor | Nueva postulación')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('tutor.postulaciones.index') }}" class="hover:text-indigo-650 transition">Postulaciones</a>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Nueva</span>
@endsection

@section('content')
    <div class="mb-6 animate-fadeInUp">
        <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Tutor / Postulaciones</p>
        <h1 class="text-3xl font-black text-slate-900 mt-1">Nueva Solicitud de Admisión</h1>
        <p class="mt-1.5 text-xs text-slate-450 font-light">Completa los campos a continuación para postular a uno de tus estudiantes vinculados en una oferta académica activa.</p>
    </div>

    @if(session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50/50 p-5 text-rose-800 flex items-start gap-3 shadow-sm animate-fadeInUp">
            <svg class="h-5 w-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p class="font-bold text-sm">No es posible continuar</p>
                <p class="text-xs font-light mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    @elseif($estudiantes->isEmpty())
        <div class="rounded-2xl border border-amber-250 bg-amber-50/40 p-6 text-amber-900 flex items-start gap-4.5 shadow-sm max-w-3xl animate-fadeInUp">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <p class="font-bold text-sm">Sin estudiantes vinculados</p>
                <p class="text-xs font-light mt-1 leading-relaxed">
                    Aún no cuentas con estudiantes enlazados a tu cuenta de tutor. Para poder postular a un cupo académico, debes registrar y vincular un alumno primero en el Portal de Estudiantes.
                </p>
                <a href="{{ route('tutor.estudiantes.index') }}" class="inline-flex items-center gap-1.5 mt-3.5 rounded-xl bg-amber-600 hover:bg-amber-700 px-4 py-2 text-xs font-bold text-white transition-all shadow-sm">
                    Ir al Portal de Estudiantes
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    @elseif($ofertas->isEmpty())
        <div class="rounded-2xl border border-amber-255 bg-amber-50/40 p-6 text-amber-900 flex items-start gap-4.5 shadow-sm max-w-3xl animate-fadeInUp">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <p class="font-bold text-sm">Periodo de convocatoria cerrado</p>
                <p class="text-xs font-light mt-1 leading-relaxed">
                    Actualmente no existen ofertas académicas con convocatorias abiertas o vigentes para postular en este momento.
                </p>
            </div>
        </div>
    @else
        <div class="max-w-3xl rounded-3xl bg-gradient-to-b from-white to-[#FAFBFD] border border-slate-200/80 p-8 shadow-[0_12px_36px_rgba(15,23,42,0.035),0_1px_3px_rgba(0,0,0,0.015)] animate-fadeInUp delay-75">
            <form method="POST" action="{{ route('tutor.postulaciones.store') }}" class="space-y-6">
                @csrf
                
                {{-- Selector de Estudiante --}}
                <div>
                    <label for="id_est_pos" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Estudiante a postular</label>
                    <div class="relative rounded-xl border border-slate-200 bg-[#F8FAFC] shadow-inner focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-100 transition-all duration-300">
                        <select id="id_est_pos" name="id_est_pos" required class="w-full bg-transparent px-4 py-3 text-sm text-slate-800 focus:outline-none cursor-pointer">
                            <option value="">Seleccione un alumno vinculado…</option>
                            @foreach($estudiantes as $est)
                                @php $nom = trim(($est->persona->nombres_per ?? '').' '.($est->persona->ap_paterno_per ?? '').' '.($est->persona->ap_materno_per ?? '')); @endphp
                                <option value="{{ $est->id_est }}" @selected(old('id_est_pos') == $est->id_est)>{{ $nom ?: 'Estudiante #'.$est->id_est }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('id_est_pos')
                        <p class="mt-1.5 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Selector de Oferta Académica --}}
                <div>
                    <label for="id_oac_pos" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Unidad Educativa y Oferta</label>
                    <div class="relative rounded-xl border border-slate-200 bg-[#F8FAFC] shadow-inner focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-100 transition-all duration-300">
                        <select id="id_oac_pos" name="id_oac_pos" required class="w-full bg-transparent px-4 py-3 text-sm text-slate-800 focus:outline-none cursor-pointer">
                            <option value="">Seleccione la oferta escolar…</option>
                            @foreach($ofertas as $oac)
                                @php
                                    $label = trim(implode(' · ', array_filter([
                                        $oac->unidadEducativa->nombre_ued ?? null,
                                        $oac->nivel->nombre_niv ?? null,
                                        $oac->curso->nombre_cur ?? null,
                                        $oac->paralelo->nombre_par ?? null,
                                    ])));
                                @endphp
                                <option value="{{ $oac->id_oac }}" @selected(old('id_oac_pos') == $oac->id_oac)>
                                    {{ $label ?: 'Oferta #'.$oac->id_oac }} ({{ $oac->gestion->nombre_ges ?? 'Convocatoria' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <p class="mt-1.5 text-[10px] text-slate-400 font-light leading-relaxed">
                        Solo se listan las ofertas de unidades educativas con periodos de postulación válidos y abiertos globalmente.
                    </p>
                    @error('id_oac_pos')
                        <p class="mt-1.5 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Prioridad y Observaciones en Grid --}}
                <div class="grid gap-5 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <label for="prioridad_pos" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Prioridad</label>
                        <input type="number"
                               id="prioridad_pos"
                               name="prioridad_pos"
                               min="1"
                               max="20"
                               value="{{ old('prioridad_pos', 1) }}"
                               required
                               class="w-full rounded-xl border border-slate-200 bg-[#F8FAFC] px-4 py-3 text-sm text-slate-800 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100 shadow-inner">
                        @error('prioridad_pos')
                            <p class="mt-1.5 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2 flex items-center">
                        <div class="rounded-xl bg-indigo-50/40 p-4 border border-indigo-100/35 text-[11px] text-indigo-850 flex items-start gap-2.5">
                            <svg class="h-4.5 w-4.5 text-indigo-550 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="leading-relaxed">
                                <strong>Regla de prioridad:</strong> Usa <strong class="text-indigo-700">1</strong> para tu primera opción (colegio de máxima preferencia), <strong class="text-indigo-700">2</strong> para tu segunda opción, y así sucesivamente.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Observaciones adicionales --}}
                <div>
                    <label for="observaciones_pos" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Observaciones adicionales (opcional)</label>
                    <textarea id="observaciones_pos" 
                              name="observaciones_pos" 
                              rows="3" 
                              placeholder="Escribe alguna aclaración relevante sobre la postulación de tu estudiante..."
                              class="w-full rounded-xl border border-slate-200 bg-[#F8FAFC] px-4 py-3 text-sm text-slate-800 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100 shadow-inner">{{ old('observaciones_pos') }}</textarea>
                    @error('observaciones_pos')
                        <p class="mt-1.5 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botones de Acción --}}
                <div class="flex flex-wrap gap-3 pt-3 border-t border-slate-100">
                    <button type="submit" 
                            class="rounded-xl bg-indigo-600 hover:bg-indigo-700 px-6 py-3 text-sm font-bold text-white shadow-md shadow-indigo-100 hover:shadow-lg hover:shadow-indigo-200 transition-all duration-300 active:scale-95">
                        Registrar solicitud
                    </button>
                    <a href="{{ route('tutor.postulaciones.index') }}" 
                       class="rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-6 py-3 text-sm font-bold text-slate-600 transition shadow-sm active:scale-95">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    @endif
@endsection
