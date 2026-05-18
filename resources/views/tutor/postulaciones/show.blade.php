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

    @php
        $etapaActual = $postulacion->etapaTutor();

        $pasosSeguimiento = [
            'registrada' => [
                'titulo' => 'Postulación registrada',
                'descripcion' => 'La solicitud fue creada correctamente.',
            ],
            'documentos_revision' => [
                'titulo' => 'Documentos en revisión',
                'descripcion' => 'Existen documentos cargados y pendientes de revisión.',
            ],
            'documentos_completos' => [
                'titulo' => 'Documentos completos',
                'descripcion' => 'Los documentos requeridos fueron cargados.',
            ],
            'resultado' => [
                'titulo' => 'Resultado generado',
                'descripcion' => 'La postulación ya cuenta con evaluación o ranking.',
            ],
            'asignado' => [
                'titulo' => 'Cupo asignado',
                'descripcion' => 'El estudiante tiene una asignación registrada.',
            ],
            'lista_espera' => [
                'titulo' => 'Lista de espera',
                'descripcion' => 'El estudiante quedó en lista de espera.',
            ],
        ];

        $ordenPasos = array_keys($pasosSeguimiento);
        $indiceActual = array_search($etapaActual, $ordenPasos, true);
        $indiceActual = $indiceActual === false ? 0 : $indiceActual;
    @endphp

    <section class="mb-6 rounded-2xl bg-white p-6 shadow-sm">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">Seguimiento de la postulación</h2>
                <p class="text-sm text-slate-500">
                    Avance documental: {{ $postulacion->porcentajeDocumental() }}%
                </p>
            </div>

            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                {{ $pasosSeguimiento[$etapaActual]['titulo'] ?? 'En proceso' }}
            </span>
        </div>

        <div class="grid gap-3 md:grid-cols-3 lg:grid-cols-6">
            @foreach($pasosSeguimiento as $clave => $paso)
                @php
                    $indicePaso = array_search($clave, $ordenPasos, true);
                    $activo = $indicePaso <= $indiceActual;

                    if ($etapaActual === 'lista_espera') {
                        $activo = in_array($clave, ['registrada', 'documentos_revision', 'documentos_completos', 'resultado', 'lista_espera'], true);
                    }
                @endphp

                <div class="rounded-xl border p-3 {{ $activo ? 'border-indigo-200 bg-indigo-50' : 'border-slate-100 bg-slate-50' }}">
                    <div class="mb-2 flex h-7 w-7 items-center justify-center rounded-full {{ $activo ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                        {{ $loop->iteration }}
                    </div>
                    <p class="text-sm font-semibold {{ $activo ? 'text-indigo-800' : 'text-slate-500' }}">
                        {{ $paso['titulo'] }}
                    </p>
                    <p class="mt-1 text-xs {{ $activo ? 'text-indigo-700' : 'text-slate-400' }}">
                        {{ $paso['descripcion'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-800">Información general</h2>
            <dl class="space-y-2 text-sm text-slate-700">
                <div><dt class="font-semibold text-slate-500">Estudiante</dt><dd>{{ trim(($postulacion->estudiante->persona->nombres_per ?? '').' '.($postulacion->estudiante->persona->ap_paterno_per ?? '').' '.($postulacion->estudiante->persona->ap_materno_per ?? '')) ?: '—' }}</dd></div>
                <div><dt class="font-semibold text-slate-500">Unidad educativa</dt><dd>{{ $postulacion->ofertaAcademica->unidadEducativa->nombre_ued ?? '—' }}</dd></div>
                <div><dt class="font-semibold text-slate-500">Gestión / Nivel</dt><dd>{{ ($postulacion->ofertaAcademica->gestion->nombre_ges ?? '—').' · '.($postulacion->ofertaAcademica->nivel->nombre_niv ?? '—') }}</dd></div>
                <div><dt class="font-semibold text-slate-500">Curso / Paralelo</dt><dd>{{ ($postulacion->ofertaAcademica->curso->nombre_cur ?? '—').' '.($postulacion->ofertaAcademica->paralelo->nombre_par ?? '') }}</dd></div>
                <div>
                    <dt class="font-semibold text-slate-500">Prioridad</dt>
                    <dd>
                        <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                            Prioridad {{ $postulacion->prioridad_pos }}
                        </span>
                    </dd>
                </div>
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
                        @if($asi->fecha_limite_respuesta_asi)
                            <p class="text-xs text-slate-500">
                                Responder hasta: {{ $asi->fecha_limite_respuesta_asi->format('d/m/Y H:i') }}
                            </p>
                        @endif
                        @if($asi->cupo)
                            <p class="text-xs text-slate-500">Cupo: {{ $asi->cupo->disponibles_cup ?? '—' }} / {{ $asi->cupo->total_cup ?? '—' }} disponibles</p>
                        @endif
                    </div>
                @endforeach
                @if($postulacion->asignaciones->isEmpty())
                    <p class="text-slate-500">Sin registros de asignación.</p>
                @endif
            </dl>

            @if($postulacion->puedeResponderCupo())
                <div class="mt-5 rounded-2xl border border-indigo-100 bg-indigo-50 p-4">
                    <h3 class="text-sm font-bold text-indigo-900">Confirmación de cupo</h3>
                    <p class="mt-1 text-sm text-indigo-800">
                        El estudiante tiene un cupo asignado. Debes aceptar o rechazar la asignación.
                    </p>

                    @php
                        $asignacionActiva = $postulacion->asignacionActiva();
                    @endphp

                    @if($asignacionActiva?->fecha_limite_respuesta_asi)
                        <p class="mt-2 rounded-xl bg-white/70 px-3 py-2 text-xs font-semibold text-indigo-900">
                            Plazo máximo de respuesta:
                            {{ $asignacionActiva->fecha_limite_respuesta_asi->format('d/m/Y H:i') }}
                        </p>
                    @endif

                    <form method="POST"
                          action="{{ route('tutor.postulaciones.responder-cupo', $postulacion) }}"
                          class="mt-4 flex flex-wrap gap-3">
                        @csrf

                        <button type="submit"
                                name="accion"
                                value="aceptar"
                                class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                            Aceptar cupo
                        </button>

                        <button type="submit"
                                name="accion"
                                value="rechazar"
                                onclick="return confirm('¿Seguro que deseas rechazar este cupo? Esta acción liberará la vacante.');"
                                class="rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-700">
                            Rechazar cupo
                        </button>
                    </form>
                </div>
            @elseif($postulacion->cupoAceptado())
                <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900">
                    <p class="font-semibold">Cupo aceptado</p>
                    <p class="mt-1 text-sm">
                        Fecha de aceptación:
                        {{ optional($postulacion->fecha_aceptacion_cupo)->format('d/m/Y H:i') ?? '—' }}
                    </p>
                </div>
            @elseif($postulacion->cupoVencido())
                <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
                    <p class="font-semibold">Plazo de respuesta vencido</p>
                    <p class="mt-1 text-sm">
                        El cupo fue liberado automáticamente porque no se aceptó ni rechazó dentro del plazo establecido.
                    </p>
                    <p class="mt-1 text-sm">
                        Fecha de vencimiento:
                        {{ optional($postulacion->fecha_aceptacion_cupo)->format('d/m/Y H:i') ?? '—' }}
                    </p>
                </div>
            @elseif($postulacion->cupoRechazado())
                <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-900">
                    <p class="font-semibold">Cupo rechazado</p>
                    <p class="mt-1 text-sm">
                        Fecha de rechazo:
                        {{ optional($postulacion->fecha_aceptacion_cupo)->format('d/m/Y H:i') ?? '—' }}
                    </p>
                </div>
            @endif
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
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-800">Documentos</h2>

                @if(! $postulacion->documentosCompletos())
                    <a href="{{ route('tutor.documentos.create', $postulacion) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-teal-50 px-4 py-2 text-sm font-semibold text-teal-700 transition hover:bg-teal-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Subir documento
                    </a>
                @else
                    <span class="inline-flex rounded-xl bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">
                        Documentación completa
                    </span>
                @endif
            </div>

            @php
                $estadoClasses = [
                    'pendiente'  => 'bg-amber-50 text-amber-700',
                    'verificado' => 'bg-emerald-50 text-emerald-700',
                    'observado'  => 'bg-blue-50 text-blue-700',
                    'rechazado'  => 'bg-rose-50 text-rose-700',
                ];
                $documentosRequeridos = $postulacion->ofertaAcademica->tiposDocumentoRequeridos ?? collect();
                $documentosPorTipo = $postulacion->documentos->groupBy('id_tdo_doc');
            @endphp

            <div class="mb-5 rounded-2xl border border-slate-100 bg-slate-50 p-4">
                <h3 class="mb-3 text-sm font-bold uppercase text-slate-500">Documentos requeridos</h3>

                @if($documentosRequeridos->isEmpty())
                    <p class="text-sm text-slate-500">Esta oferta no tiene documentos requeridos configurados.</p>
                @else
                    <div class="grid gap-2 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($documentosRequeridos as $tipo)
                            @php
                                $ultimoDocumento = collect($documentosPorTipo->get($tipo->id_tdo))
                                    ->sortByDesc('id_doc')
                                    ->first();

                                $estadoDocumento = $ultimoDocumento->estado_doc ?? null;

                                $clasesDocumento = [
                                    'pendiente' => 'bg-amber-50 text-amber-700',
                                    'verificado' => 'bg-emerald-50 text-emerald-700',
                                    'rechazado' => 'bg-rose-50 text-rose-700',
                                ];

                                $textoDocumento = [
                                    'pendiente' => 'En revisión',
                                    'verificado' => 'Verificado',
                                    'rechazado' => 'Rechazado',
                                ];
                            @endphp

                            <div class="rounded-xl bg-white p-3 text-sm shadow-sm">
                                <p class="font-semibold text-slate-800">{{ $tipo->nombre_tdo }}</p>

                                @if($estadoDocumento)
                                    <span class="mt-2 inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $clasesDocumento[$estadoDocumento] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $textoDocumento[$estadoDocumento] ?? $estadoDocumento }}
                                    </span>
                                @else
                                    <span class="mt-2 inline-flex rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-semibold text-rose-700">
                                        Faltante
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($postulacion->documentos->isEmpty())
                <p class="text-sm text-slate-500">No hay documentos adjuntos. Usa el botón para subir uno.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-slate-100 bg-slate-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-slate-400">Tipo</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-slate-400">Estado</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-slate-400">Observación</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold uppercase text-slate-400">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($postulacion->documentos as $doc)
                                @php $cls = $estadoClasses[$doc->estado_doc] ?? 'bg-slate-100 text-slate-600'; @endphp
                                <tr>
                                    <td class="px-4 py-2 font-medium text-slate-800">{{ $doc->tipoDocumento->nombre_tdo ?? '—' }}</td>
                                    <td class="px-4 py-2">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $cls }}">
                                            {{ $doc->estado_doc ?? 'pendiente' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-slate-600">
                                        @if($doc->observacion_doc)
                                            <p class="max-w-md whitespace-pre-wrap text-xs">{{ $doc->observacion_doc }}</p>
                                            @if($doc->fecha_revision_doc)
                                                <p class="mt-1 text-[11px] text-slate-400">
                                                    Revisado: {{ $doc->fecha_revision_doc->format('d/m/Y H:i') }}
                                                </p>
                                            @endif
                                        @else
                                            <span class="text-xs text-slate-400">Sin observación</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('tutor.documentos.download', $doc) }}"
                                               class="rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-200">
                                                Descargar
                                            </a>
                                            @if(in_array($doc->estado_doc, ['pendiente', 'observado', 'rechazado'], true))
                                                <form method="POST"
                                                      action="{{ route('tutor.documentos.destroy', $doc) }}"
                                                      class="inline"
                                                      onsubmit="return confirm('¿Eliminar este documento?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="rounded-lg bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-100">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
