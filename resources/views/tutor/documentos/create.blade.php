@extends('layouts.dashboard')

@section('title', 'Subir documento | Tutor')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('tutor.postulaciones.index') }}" class="hover:text-teal-600">Postulaciones</a>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('tutor.postulaciones.show', $postulacion) }}" class="hover:text-teal-600">#{{ $postulacion->id_pos }}</a>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Subir documento</span>
@endsection

@section('content')
    <div class="mb-8">
        <p class="text-xs text-slate-400">Postulación #{{ $postulacion->id_pos }}</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900">Subir documento</h1>
        <p class="mt-1 text-sm text-slate-500">Formatos permitidos: PDF, JPG, PNG. Máximo 5 MB.</p>
    </div>

    <form method="POST"
          action="{{ route('tutor.documentos.store', $postulacion) }}"
          enctype="multipart/form-data"
          class="max-w-xl rounded-2xl bg-white p-6 shadow-sm md:p-8">
        @csrf

        <div class="space-y-5">
            <div>
                <label for="id_tdo_doc" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Tipo de documento</label>
                <select name="id_tdo_doc" id="id_tdo_doc"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">Selecciona un tipo…</option>
                    @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id_tdo }}" {{ old('id_tdo_doc') == $tipo->id_tdo ? 'selected' : '' }}>
                            {{ $tipo->nombre_tdo }}
                        </option>
                    @endforeach
                </select>
                @error('id_tdo_doc')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="archivo" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Archivo</label>
                <div class="relative">
                    <input type="file"
                           name="archivo"
                           id="archivo"
                           accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition file:mr-4 file:rounded-lg file:border-0 file:bg-teal-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-teal-700 hover:file:bg-teal-100 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                @error('archivo')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:from-teal-700 hover:to-emerald-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Subir documento
            </button>
            <a href="{{ route('tutor.postulaciones.show', $postulacion) }}"
               class="inline-flex items-center rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Cancelar
            </a>
        </div>
    </form>
@endsection
