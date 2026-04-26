@extends('layouts.dashboard')

@section('title', 'Tutor | Documentos')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Documentos</span>
@endsection

@section('content')
    <div class="mb-6">
        <p class="text-xs text-slate-400">Tutor / Documentos</p>
        <h1 class="text-2xl font-bold text-slate-900">Documentos</h1>
        <p class="mt-1 text-sm text-slate-500">Listado de documentos ligados a postulaciones de tus estudiantes (solo consulta).</p>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        @if($documentos->count() === 0)
            <p class="p-8 text-center text-sm text-slate-500">No hay documentos registrados.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">#</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Postulación</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Estudiante</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Tipo</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">OCR</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Archivo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($documentos as $doc)
                            @php
                                $pos = $doc->postulacion;
                                $nom = $pos && $pos->estudiante
                                    ? trim(($pos->estudiante->persona->nombres_per ?? '').' '.($pos->estudiante->persona->ap_paterno_per ?? ''))
                                    : '';
                            @endphp
                            <tr>
                                <td class="px-6 py-4 text-slate-500">{{ $doc->id_doc }}</td>
                                <td class="px-6 py-4 font-medium text-indigo-600">
                                    @if($pos)
                                        <a href="{{ route('tutor.postulaciones.show', $pos) }}" class="hover:underline">#{{ $pos->id_pos }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4">{{ $nom ?: '—' }}</td>
                                <td class="px-6 py-4">{{ $doc->tipoDocumento->nombre_tdo ?? '—' }}</td>
                                <td class="px-6 py-4">{{ $doc->estado_doc ?? '—' }}</td>
                                <td class="px-6 py-4 text-xs text-slate-600">{{ $doc->procesamientoOcr->estado_poc ?? '—' }}</td>
                                <td class="max-w-xs px-6 py-4 font-mono text-xs text-slate-500 break-all">{{ $doc->ruta_archivo_doc ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $documentos->links() }}
            </div>
        @endif
    </div>
@endsection
