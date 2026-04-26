@extends('layouts.dashboard')

@section('title', 'Tutor | Detalle postulación')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Detalle</span>
@endsection

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-xs text-slate-400">Tutor / Postulaciones / #{{ $postulacion->id_pos }}</p>
            <h1 class="text-2xl font-bold text-slate-900">Detalle de postulación</h1>
        </div>
        <a href="{{ route('tutor.postulaciones.index') }}" class="text-sm font-semibold text-indigo-600 hover:underline">← Volver al listado</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-800">Información general</h2>
            <dl class="space-y-2 text-sm text-slate-700">
                <div><dt class="font-semibold text-slate-500">Estudiante</dt><dd>{{ trim(($postulacion->estudiante->persona->nombres_per ?? '').' '.($postulacion->estudiante->persona->ap_paterno_per ?? '').' '.($postulacion->estudiante->persona->ap_materno_per ?? '')) ?: '—' }}</dd></div>
                <div><dt class="font-semibold text-slate-500">Unidad educativa</dt><dd>{{ $postulacion->ofertaAcademica->unidadEducativa->nombre_ued ?? '—' }}</dd></div>
                <div><dt class="font-semibold text-slate-500">Gestión / Nivel</dt><dd>{{ ($postulacion->ofertaAcademica->gestion->nombre_ges ?? '—').' · '.($postulacion->ofertaAcademica->nivel->nombre_niv ?? '—') }}</dd></div>
                <div><dt class="font-semibold text-slate-500">Curso / Paralelo</dt><dd>{{ ($postulacion->ofertaAcademica->curso->nombre_cur ?? '—').' '.($postulacion->ofertaAcademica->paralelo->nombre_par ?? '') }}</dd></div>
                <div><dt class="font-semibold text-slate-500">Estado</dt><dd><span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold">{{ $postulacion->estadoPostulacion->nombre_ept ?? '—' }}</span></dd></div>
                <div><dt class="font-semibold text-slate-500">Fecha</dt><dd>{{ optional($postulacion->fecha_pos)->format('d/m/Y H:i') ?? '—' }}</dd></div>
                <div><dt class="font-semibold text-slate-500">Observaciones</dt><dd class="whitespace-pre-wrap">{{ $postulacion->observaciones_pos ?: '—' }}</dd></div>
            </dl>
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-800">Resultado y asignación</h2>
            <dl class="space-y-2 text-sm text-slate-700">
                <div><dt class="font-semibold text-slate-500">Puntaje total</dt><dd>{{ $postulacion->resultado->puntaje_total_res ?? '—' }}</dd></div>
                <div><dt class="font-semibold text-slate-500">Clasificación</dt><dd>{{ $postulacion->resultado->clasificacion_res ?? '—' }}</dd></div>
                @foreach($postulacion->asignaciones as $asi)
                    <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-3">
                        <p class="text-xs font-semibold uppercase text-slate-400">Asignación</p>
                        <p class="mt-1">Estado: <strong>{{ $asi->estado_asi ?? '—' }}</strong></p>
                        <p class="text-xs text-slate-500">Fecha: {{ optional($asi->fecha_asi)->format('d/m/Y H:i') ?? '—' }}</p>
                        @if($asi->cupo)
                            <p class="text-xs text-slate-500">Cupo: {{ $asi->cupo->disponibles_cup ?? '—' }} / {{ $asi->cupo->total_cup ?? '—' }} disponibles</p>
                        @endif
                    </div>
                @endforeach
                @if($postulacion->asignaciones->isEmpty())
                    <p class="text-slate-500">Sin registros de asignación.</p>
                @endif
            </dl>
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="mb-4 text-lg font-semibold text-slate-800">Lista de espera</h2>
            @if($postulacion->listasEspera->isEmpty())
                <p class="text-sm text-slate-500">No figura en lista de espera.</p>
            @else
                <ul class="divide-y divide-slate-100 text-sm">
                    @foreach($postulacion->listasEspera as $les)
                        <li class="flex flex-wrap justify-between gap-2 py-2">
                            <span>Oferta #{{ $les->id_oac_les }} {{ $les->ofertaAcademica->curso->nombre_cur ?? '' }}</span>
                            <span class="font-semibold text-amber-700">Orden {{ $les->orden_les }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="mb-4 text-lg font-semibold text-slate-800">Evaluaciones (solo lectura)</h2>
            <div class="space-y-2">
                @forelse($postulacion->evaluaciones as $eva)
                    <div class="rounded-xl border border-slate-100 p-3 text-sm">
                        <p class="font-semibold text-slate-800">{{ $eva->criterio->nombre_cri ?? 'Criterio' }}</p>
                        <p class="text-slate-600">Puntaje: {{ $eva->puntaje_eva ?? '—' }}</p>
                        @if($eva->observaciones_eva)
                            <p class="mt-1 text-xs text-slate-500">{{ $eva->observaciones_eva }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Sin evaluaciones registradas.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="mb-4 text-lg font-semibold text-slate-800">Documentos</h2>
            @if($postulacion->documentos->isEmpty())
                <p class="text-sm text-slate-500">No hay documentos asociados.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-slate-100 bg-slate-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-slate-400">Tipo</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-slate-400">Estado</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-slate-400">Ruta</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($postulacion->documentos as $doc)
                                <tr>
                                    <td class="px-4 py-2">{{ $doc->tipoDocumento->nombre_tdo ?? '—' }}</td>
                                    <td class="px-4 py-2">{{ $doc->estado_doc ?? '—' }}</td>
                                    <td class="px-4 py-2 font-mono text-xs text-slate-600 break-all">{{ $doc->ruta_archivo_doc ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
