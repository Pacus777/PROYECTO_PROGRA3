@extends('layouts.dashboard')

@section('title', 'Documentos | Admin Institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Documentos</span>
@endsection

@section('content')
    <x-institucional.page
        module="documentos"
        title="Revisión de documentos"
        subtitle="Verifica, observa o rechaza los documentos enviados por los tutores."
    >
        <x-institucional.panel module="documentos" title="Filtrar por estado">
            <div class="flex flex-wrap items-center gap-2 p-5">
                @foreach(['todos' => 'Todos', 'pendiente' => 'Pendientes', 'verificado' => 'Verificados', 'observado' => 'Observados', 'rechazado' => 'Rechazados'] as $val => $label)
                    @php $active = ($val === 'todos' && !$estado) || $val === $estado; @endphp
                    <a href="{{ route('admin.institucional.documentos.index', $val !== 'todos' ? ['estado' => $val] : []) }}"
                       @if($active) data-inst-filter-active @endif
                       class="rounded-full px-4 py-1.5 text-xs font-semibold transition
                           {{ $active ? 'bg-indigo-600 text-white' : 'border border-slate-200 bg-white text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                        {{ $label }}
                    </a>
                @endforeach

                <x-admin.export-report route="admin.institucional.documentos.export" class="ml-auto" />
            </div>
        </x-institucional.panel>

        @php
            $estadoClasses = [
                'pendiente'  => 'bg-amber-50 text-amber-700',
                'verificado' => 'bg-emerald-50 text-emerald-700',
                'observado'  => 'bg-blue-50 text-blue-700',
                'rechazado'  => 'bg-rose-50 text-rose-700',
            ];
        @endphp

        <x-institucional.panel module="documentos" title="Documentos de postulación">
            @if($documentos->isEmpty())
                <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-indigo-500">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 3v5h5"/>
                        </svg>
                    </div>
                    <p class="mt-4 font-semibold text-slate-700">Sin documentos</p>
                    <p class="mt-2 text-sm text-slate-400">No hay documentos con el filtro seleccionado.</p>
                </div>
            @else
                <div class="overflow-x-auto" data-inst-table>
                    <table class="w-full text-sm">
                        <thead class="border-b border-slate-100 bg-slate-50">
                            <tr>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Estudiante</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Postulación</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Tipo</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Estado doc.</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Observación</th>
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
                                    $ocrClass = $estadoClasses[$doc->procesamientoOcr?->estado_poc] ?? 'bg-slate-100 text-slate-600';
                                @endphp

                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-5 py-3 font-medium text-slate-900">{{ $nom }}</td>

                                    <td class="px-5 py-3">
                                        @if($pos)
                                            <a href="{{ route('admin.institucional.postulaciones.show', $pos) }}"
                                               class="font-semibold text-indigo-600 hover:underline">
                                                #{{ $pos->id_pos }}
                                            </a>
                                            <p class="mt-1 text-xs text-slate-400">
                                                {{ $pos->ofertaAcademica->curso->nombre_cur ?? '—' }}
                                                {{ $pos->ofertaAcademica->paralelo->nombre_par ?? '' }}
                                            </p>
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <td class="px-5 py-3 text-slate-600">
                                        {{ $doc->tipoDocumento->nombre_tdo ?? '—' }}
                                    </td>

                                    <td class="px-5 py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $estadoClass }}">
                                            {{ $doc->estado_doc ?? 'pendiente' }}
                                        </span>

                                        @if($doc->fecha_revision_doc)
                                            <p class="mt-1 text-[11px] text-slate-400">
                                                Revisado: {{ $doc->fecha_revision_doc->format('d/m/Y H:i') }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="px-5 py-3 text-slate-600">
                                        @if($doc->observacion_doc)
                                            <p class="max-w-xs whitespace-pre-wrap text-xs">{{ $doc->observacion_doc }}</p>
                                        @else
                                            <span class="text-xs text-slate-400">Sin observación</span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $ocrClass }}">
                                            {{ $doc->procesamientoOcr?->estado_poc ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-3 text-right">
                                        <div class="flex flex-col items-end gap-2">
                                            <a href="{{ route('admin.institucional.documentos.download', $doc) }}"
                                               class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-200">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                Ver
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('admin.institucional.documentos.estado', $doc) }}"
                                                  class="w-72 rounded-xl border border-slate-100 bg-slate-50 p-3 text-left">
                                                @csrf
                                                @method('PATCH')

                                                <label class="mb-1 block text-[11px] font-semibold uppercase text-slate-400">
                                                    Estado
                                                </label>

                                                <select name="estado_doc"
                                                        class="mb-2 w-full rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                                    @foreach(['pendiente' => 'Pendiente', 'verificado' => 'Verificado', 'observado' => 'Observado', 'rechazado' => 'Rechazado'] as $val => $label)
                                                        <option value="{{ $val }}" {{ $doc->estado_doc === $val ? 'selected' : '' }}>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <label class="mb-1 block text-[11px] font-semibold uppercase text-slate-400">
                                                    Observación
                                                </label>

                                                <textarea name="observacion_doc"
                                                          rows="2"
                                                          maxlength="2000"
                                                          placeholder="Escriba una observación si corresponde..."
                                                          class="mb-2 w-full rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">{{ old('observacion_doc', $doc->observacion_doc) }}</textarea>

                                                <button type="submit"
                                                        class="w-full rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700">
                                                    Guardar revisión
                                                </button>
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
        </x-institucional.panel>
    </x-institucional.page>
@endsection
