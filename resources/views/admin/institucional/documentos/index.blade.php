@extends('layouts.dashboard')

@section('title', 'Documentos | Admin Institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Documentos</span>
@endsection

@section('content')
    <div class="mb-8">
        <p class="text-xs text-slate-400">Panel / Documentos</p>
        <h1 class="text-2xl font-bold text-slate-900">Revisión de documentos</h1>
        <p class="mt-1 text-sm text-slate-500">Verifica y aprueba o rechaza los documentos enviados por los tutores.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
    @endif

    {{-- Filtro por estado --}}
    <div class="mb-6 flex flex-wrap gap-2">
        @foreach(['todos' => 'Todos', 'pendiente' => 'Pendientes', 'verificado' => 'Verificados', 'rechazado' => 'Rechazados'] as $val => $label)
            @php $active = ($val === 'todos' && !$estado) || $val === $estado; @endphp
            <a href="{{ route('admin.institucional.documentos.index', $val !== 'todos' ? ['estado' => $val] : []) }}"
               class="rounded-full px-4 py-1.5 text-xs font-semibold transition
                   {{ $active ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 hover:bg-indigo-50 hover:text-indigo-700 border border-slate-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @php
        $estadoClasses = [
            'pendiente'  => 'bg-amber-50 text-amber-700',
            'verificado' => 'bg-emerald-50 text-emerald-700',
            'rechazado'  => 'bg-rose-50 text-rose-700',
        ];
    @endphp

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        @if($documentos->isEmpty())
            <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-indigo-500">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 3v5h5"/></svg>
                </div>
                <p class="mt-4 font-semibold text-slate-700">Sin documentos</p>
                <p class="mt-2 text-sm text-slate-400">No hay documentos con el filtro seleccionado.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Estudiante</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Postulación</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Tipo</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Estado doc.</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">OCR</th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($documentos as $doc)
                            @php
                                $pos = $doc->postulacion;
                                $nom = $pos?->estudiante
                                    ? trim(($pos->estudiante->persona->nombres_per ?? '').' '.($pos->estudiante->persona->ap_paterno_per ?? ''))
                                    : '—';
                                $estadoClass = $estadoClasses[$doc->estado_doc] ?? 'bg-slate-100 text-slate-600';
                                $ocrClass    = $estadoClasses[$doc->procesamientoOcr?->estado_poc] ?? 'bg-slate-100 text-slate-600';
                            @endphp
                            <tr class="transition hover:bg-slate-50">
                                <td class="px-5 py-3 font-medium text-slate-900">{{ $nom }}</td>
                                <td class="px-5 py-3">
                                    @if($pos)
                                        <a href="{{ route('admin.institucional.postulaciones.show', $pos) }}"
                                           class="font-semibold text-indigo-600 hover:underline">#{{ $pos->id_pos }}</a>
                                    @else —
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-slate-600">{{ $doc->tipoDocumento->nombre_tdo ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $estadoClass }}">
                                        {{ $doc->estado_doc ?? 'pendiente' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $ocrClass }}">
                                        {{ $doc->procesamientoOcr?->estado_poc ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="inline-flex flex-wrap items-center justify-end gap-2">
                                        <a href="{{ route('admin.institucional.documentos.download', $doc) }}"
                                           class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-200">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            Ver
                                        </a>

                                        {{-- Cambio de estado --}}
                                        <form method="POST"
                                              action="{{ route('admin.institucional.documentos.estado', $doc) }}"
                                              class="inline-flex items-center gap-1">
                                            @csrf
                                            @method('PATCH')
                                            <select name="estado_doc"
                                                    onchange="this.form.submit()"
                                                    class="rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                                @foreach(['pendiente' => 'Pendiente', 'verificado' => 'Verificar', 'rechazado' => 'Rechazar'] as $val => $label)
                                                    <option value="{{ $val }}" {{ $doc->estado_doc === $val ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($documentos->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">{{ $documentos->links() }}</div>
            @endif
        @endif
    </div>
@endsection
