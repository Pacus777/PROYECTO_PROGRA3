@extends('layouts.dashboard')

@section('title', 'Detalle de postulación | Admin institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.institucional.postulaciones.index') }}" class="hover:text-indigo-600">Postulaciones</a>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Detalle</span>
@endsection

@section('content')
    @php
        $est = $postulacion->estudiante;
        $per = $est?->persona;
        $oac = $postulacion->ofertaAcademica;
        $nombre = trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? '').' '.($per->ap_materno_per ?? ''));
        $tituloPostulante = $nombre ?: 'Postulación';
    @endphp

    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs text-slate-400">Panel / Postulaciones / Detalle</p>
            <h1 class="text-2xl font-bold text-slate-900">{{ $tituloPostulante }}</h1>
            <div class="mt-2">
                @include('admin.institucional.postulaciones._estado-badge', ['estado' => $postulacion->estadoPostulacion->nombre_ept ?? null])
            </div>
        </div>
        <a href="{{ route('admin.institucional.documentos.index') }}"
           class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
            Ver documentos
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-400">Postulante (estudiante)</h2>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-slate-500">Nombres completos</dt><dd class="font-semibold text-slate-900">{{ $nombre ?: '—' }}</dd></div>
                <div><dt class="text-slate-500">RUDE (rude_est)</dt><dd class="font-mono font-semibold text-emerald-800">{{ $est->rude_est ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Código (codigo_est)</dt><dd class="font-mono">{{ $est->codigo_est ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">CI (ci_per)</dt><dd>{{ $per->ci_per ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">UE matrícula actual</dt><dd>{{ $est->unidadMatriculaActual->nombre_ued ?? 'Sin registrar' }}</dd></div>
            </dl>
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-400">Oferta académica (id_oac_pos)</h2>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-slate-500">Unidad educativa</dt><dd class="font-semibold">{{ $oac->unidadEducativa->nombre_ued ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Gestión (id_ges_oac)</dt><dd>{{ $oac->gestion->nombre_ges ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Nivel / curso / paralelo</dt>
                    <dd>{{ $oac->nivel->nombre_niv ?? '—' }} · {{ $oac->curso->nombre_cur ?? '—' }} {{ $oac->paralelo->nombre_par ?? '' }}</dd>
                </div>
                <div><dt class="text-slate-500">fecha_pos</dt><dd>{{ $postulacion->fecha_pos?->format('d/m/Y H:i') ?? '—' }}</dd></div>
            </dl>
        </section>
    </div>

    <section class="mt-6 rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-400">Estado y observaciones</h2>
        <form method="POST" action="{{ route('admin.institucional.postulaciones.update', $postulacion) }}" class="grid gap-4 md:grid-cols-2">
            @csrf
            @method('PATCH')
            <div>
                <label for="id_ept_pos" class="mb-1 block text-xs font-semibold text-slate-500">Estado (id_ept_pos → estado_postulacion)</label>
                <select id="id_ept_pos" name="id_ept_pos" required
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    @foreach($estados as $estado)
                        <option value="{{ $estado->id_ept }}" @selected($postulacion->id_ept_pos == $estado->id_ept)>
                            {{ $estado->nombre_ept }}
                            @if($estado->descripcion_ept)
                                — {{ $estado->descripcion_ept }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="observaciones_pos" class="mb-1 block text-xs font-semibold text-slate-500">Observaciones (observaciones_pos)</label>
                <textarea id="observaciones_pos" name="observaciones_pos" rows="3"
                          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">{{ old('observaciones_pos', $postulacion->observaciones_pos) }}</textarea>
            </div>
            <div>
                <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Guardar cambios
                </button>
            </div>
        </form>
    </section>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-400">Resultado (tabla resultado)</h2>
            @if($postulacion->resultado)
                <dl class="space-y-2 text-sm">
                    <div><dt class="text-slate-500">puntaje_total_res</dt><dd class="text-lg font-bold text-indigo-600">{{ number_format((float) $postulacion->resultado->puntaje_total_res, 2) }}</dd></div>
                    <div><dt class="text-slate-500">clasificacion_res (orden)</dt><dd class="font-semibold">{{ $postulacion->resultado->clasificacion_res ?? '—' }}</dd></div>
                </dl>
            @else
                <p class="text-sm text-slate-500">Sin registro en <code class="text-xs">resultado</code>. Se genera al ejecutar la asignación desde Resultados.</p>
            @endif

            @if($postulacion->asignaciones->isNotEmpty())
                <h3 class="mt-4 text-xs font-bold uppercase text-slate-400">Asignación (tabla asignacion)</h3>
                <ul class="mt-2 space-y-2 text-sm">
                    @foreach($postulacion->asignaciones as $asi)
                        <li class="rounded-lg border border-slate-100 px-3 py-2">
                            <span class="font-semibold">{{ $asi->estado_asi }}</span>
                            · cupo #{{ $asi->id_cup_asi ?? '—' }}
                            @if($asi->fecha_asi)
                                · {{ $asi->fecha_asi->format('d/m/Y') }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif

            @if($postulacion->listasEspera->isNotEmpty())
                <h3 class="mt-4 text-xs font-bold uppercase text-slate-400">Lista de espera (lista_espera)</h3>
                <ul class="mt-2 space-y-1 text-sm text-slate-700">
                    @foreach($postulacion->listasEspera as $les)
                        <li>Orden {{ $les->orden_les }} · oferta #{{ $les->id_oac_les }}</li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-400">Documentos (tabla documento)</h2>
            @if($postulacion->documentos->isNotEmpty())
                <ul class="space-y-2 text-sm">
                    @foreach($postulacion->documentos as $doc)
                        <li class="flex items-center justify-between rounded-lg border border-slate-100 px-3 py-2">
                            <span>{{ $doc->tipoDocumento->nombre_tdo ?? 'Documento' }}</span>
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">{{ $doc->estado_doc ?? 'pendiente' }}</span>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('admin.institucional.documentos.index') }}" class="mt-3 inline-block text-xs font-semibold text-indigo-600 hover:underline">
                    Gestionar en Documentos / OCR →
                </a>
            @else
                <p class="text-sm text-slate-500">Sin documentos cargados para esta postulación.</p>
            @endif
        </section>
    </div>

    <section class="mt-6 rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-400">Evaluaciones (tabla evaluacion)</h2>

        <form method="POST" action="{{ route('admin.institucional.evaluaciones.store', $postulacion) }}"
              class="mb-6 grid gap-3 rounded-xl border border-slate-100 bg-slate-50/50 p-4 md:grid-cols-4">
            @csrf
            <select name="id_cri_eva" required class="rounded-lg border border-slate-200 bg-white px-2 py-2 text-sm md:col-span-2">
                <option value="">Criterio (id_cri_eva)</option>
                @foreach($criterios as $criterio)
                    <option value="{{ $criterio->id_cri }}">{{ $criterio->nombre_cri }} @if($criterio->peso_cri)(peso {{ $criterio->peso_cri }})@endif</option>
                @endforeach
            </select>
            <input type="number" step="0.01" min="0" max="100" name="puntaje_eva" placeholder="puntaje_eva"
                   class="rounded-lg border border-slate-200 bg-white px-2 py-2 text-sm">
            <button class="rounded-lg bg-indigo-600 text-sm font-semibold text-white hover:bg-indigo-700">Agregar</button>
            <textarea name="observaciones_eva" placeholder="observaciones_eva" rows="2"
                      class="md:col-span-4 rounded-lg border border-slate-200 bg-white px-2 py-2 text-sm"></textarea>
        </form>

        @forelse($postulacion->evaluaciones as $eva)
            <div class="mb-4 rounded-lg border border-slate-100 p-4">
                <form method="POST" action="{{ route('admin.institucional.evaluaciones.update', $eva) }}" class="space-y-2">
                    @csrf @method('PUT')
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="font-semibold text-slate-800">{{ $eva->criterio->nombre_cri ?? 'Criterio #'.$eva->id_cri_eva }}</p>
                        <input type="number" step="0.01" min="0" max="100" name="puntaje_eva" value="{{ $eva->puntaje_eva }}"
                               class="w-28 rounded border border-slate-200 px-2 py-1 text-sm">
                    </div>
                    <textarea name="observaciones_eva" rows="2" class="w-full rounded border border-slate-200 px-2 py-1 text-sm">{{ $eva->observaciones_eva }}</textarea>
                    <button class="rounded bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Actualizar</button>
                </form>
                <form method="POST" action="{{ route('admin.institucional.evaluaciones.destroy', $eva) }}" class="mt-2" onsubmit="return confirm('¿Eliminar evaluación?')">
                    @csrf @method('DELETE')
                    <button class="rounded bg-red-50 px-3 py-1 text-xs font-semibold text-rose-600">Eliminar</button>
                </form>
            </div>
        @empty
            <p class="text-sm text-slate-500">Sin evaluaciones registradas.</p>
        @endforelse
    </section>
@endsection
