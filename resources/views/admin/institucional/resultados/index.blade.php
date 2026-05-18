@extends('layouts.dashboard')

@section('title', 'Resultados y ranking | Admin institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Resultados</span>
@endsection

@section('content')
    @php
        $pageSubtitle = $unidad
            ? trim($unidad->nombre_ued . ($unidad->codigo_ued ? ' (' . $unidad->codigo_ued . ')' : '')) . ' — ranking por oferta según evaluaciones y pesos de criterios.'
            : 'Resultados y asignación de cupos de su unidad educativa.';
    @endphp

    <x-institucional.page module="resultados" title="Resultados y asignación de cupos" :subtitle="$pageSubtitle">
        <x-slot:actions>
            <x-admin.export-report route="admin.institucional.resultados.export" />
        </x-slot:actions>

        <x-slot:kpis>
            <x-institucional.kpi-grid module="resultados" cols="sm:grid-cols-2 lg:grid-cols-5" :items="[
                ['label' => 'Postulaciones', 'value' => $resumen['postulaciones']],
                ['label' => 'Con evaluación', 'value' => $resumen['con_evaluacion']],
                ['label' => 'En resultado', 'value' => $resumen['resultados_guardados']],
                ['label' => 'Cupos asignados', 'value' => $resumen['asignados']],
                ['label' => 'Lista de espera', 'value' => $resumen['lista_espera']],
            ]" />
        </x-slot:kpis>

        <x-institucional.panel module="resultados" title="Flujo de trabajo">
            <div class="p-5 text-sm text-indigo-900">
                <p class="font-semibold">Pasos recomendados</p>
                <ol class="mt-2 list-inside list-decimal space-y-1 text-indigo-800/90">
                    <li>Puntúe postulantes en <strong>Postulaciones</strong>.</li>
                    <li>Use <strong>Guardar ranking</strong> para fijar puntajes y orden por oferta.</li>
                    <li>Ejecute <strong>Asignación de cupos</strong> para ocupar vacantes o generar lista de espera.</li>
                </ol>
            </div>
        </x-institucional.panel>

        <x-institucional.panel module="resultados" title="Acciones">
            <div class="flex flex-wrap gap-3 p-5">
                <form method="POST" action="{{ route('admin.institucional.resultados.sincronizar') }}"
                      onsubmit="return confirm('¿Guardar el ranking calculado en los resultados?')">
                    @csrf
                    <button type="submit" class="rounded-xl border border-indigo-200 bg-white px-5 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">
                        Guardar ranking
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.institucional.asignacion.store') }}"
                      onsubmit="return confirm('¿Ejecutar asignación de cupos por oferta? Esto puede cambiar estados a aprobada y los cupos disponibles.')">
                    @csrf
                    <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-indigo-700">
                        Ejecutar asignación de cupos
                    </button>
                </form>
                <a href="{{ route('admin.institucional.criterios.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                    Criterios de evaluación
                </a>
            </div>
        </x-institucional.panel>

        <x-institucional.panel module="resultados" title="Filtros">
            <form method="GET" class="flex flex-wrap items-end gap-3 p-5">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-500">Gestión</label>
                    <select name="id_ges_oac" class="min-w-[120px] rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                        <option value="">Todas</option>
                        @foreach($gestiones as $g)
                            <option value="{{ $g->id_ges }}" @selected(request('id_ges_oac') == $g->id_ges)>{{ $g->nombre_ges }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-500">Curso</label>
                    <select name="id_cur_oac" class="min-w-[120px] rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                        <option value="">Todos</option>
                        @foreach($cursos as $c)
                            <option value="{{ $c->id_cur }}" @selected(request('id_cur_oac') == $c->id_cur)>{{ $c->nombre_cur }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-500">Oferta</label>
                    <select name="id_oac_pos" class="min-w-[180px] rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                        <option value="">Todas</option>
                        @foreach($ofertasUnidad as $o)
                            <option value="{{ $o->id_oac }}" @selected(request('id_oac_pos') == $o->id_oac)>
                                {{ $o->gestion->nombre_ges ?? '' }} · {{ $o->curso->nombre_cur ?? '' }} {{ $o->paralelo->nombre_par ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-xl bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-900">Filtrar</button>
                <a href="{{ route('admin.institucional.resultados.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Limpiar</a>
            </form>
        </x-institucional.panel>

        <x-institucional.panel module="resultados" title="Ranking">
            <div class="overflow-x-auto">
                <table data-inst-table class="min-w-full text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-left">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Orden</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Postulante</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">RUDE</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Oferta</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Puntaje</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Guardado</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Estado</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Cupos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ranking as $fila)
                            @php
                                $p = $fila->postulacion;
                                $per = $p->estudiante?->persona;
                                $nombre = trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? '').' '.($per->ap_materno_per ?? ''));
                                $oac = $p->ofertaAcademica;
                                $res = $p->resultado;
                                $asignado = $p->asignaciones->where('estado_asi', 'asignado')->isNotEmpty();
                                $enEspera = $p->listasEspera->isNotEmpty();
                            @endphp
                            <tr class="border-b border-slate-50 hover:bg-indigo-50/30 last:border-0">
                                <td class="px-4 py-3">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">
                                        {{ $fila->orden_oferta }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-slate-900">{{ $nombre ?: '—' }}</p>
                                    <a href="{{ route('admin.institucional.postulaciones.show', $p) }}" class="text-xs text-indigo-600 hover:underline">Ver postulación</a>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-emerald-800">{{ $p->estudiante->rude_est ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-700">
                                    <p class="text-xs text-slate-500">{{ $oac->gestion->nombre_ges ?? '' }}</p>
                                    <p>{{ $oac->curso->nombre_cur ?? '—' }} · {{ $oac->paralelo->nombre_par ?? '' }}</p>
                                </td>
                                <td class="px-4 py-3 font-semibold text-indigo-600">{{ number_format($fila->puntaje, 2) }}</td>
                                <td class="px-4 py-3 text-slate-600">
                                    @if($res)
                                        {{ number_format((float) $res->puntaje_total_res, 2) }}
                                        <span class="text-xs text-slate-400">(#{{ $res->clasificacion_res }})</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $p->estadoPostulacion->nombre_ept ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if($asignado)
                                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Asignado</span>
                                    @elseif($enEspera)
                                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">
                                            Lista espera #{{ $p->listasEspera->first()->orden_les }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs">Sin asignar</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-slate-500">
                                    No hay postulaciones para mostrar. Registre ofertas y postulaciones primero.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($ranking->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">{{ $ranking->links() }}</div>
            @endif
        </x-institucional.panel>
    </x-institucional.page>
@endsection
