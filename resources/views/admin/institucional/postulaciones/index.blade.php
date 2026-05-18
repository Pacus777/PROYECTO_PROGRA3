@extends('layouts.dashboard')

@section('title', 'Postulaciones | Admin institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="font-medium text-slate-500">Postulaciones</span>
@endsection

@section('content')
    @php
        $pageSubtitle = $unidad
            ? trim($unidad->nombre_ued . ($unidad->codigo_ued ? ' (' . $unidad->codigo_ued . ')' : '')) . ' — postulaciones a ofertas de su colegio.'
            : 'Postulaciones vinculadas a ofertas académicas de su unidad educativa.';
        $kpiItems = [['label' => 'Total (filtros)', 'value' => $resumen['total']]];
        foreach ($resumen['por_estado']->take(3) as $item) {
            $kpiItems[] = ['label' => $item['nombre'], 'value' => $item['total']];
        }
    @endphp

    <x-institucional.page module="postulaciones" title="Postulaciones de la unidad" :subtitle="$pageSubtitle">
        <x-slot:kpis>
            <x-institucional.kpi-grid module="postulaciones" :items="$kpiItems" />
        </x-slot:kpis>

        <x-institucional.panel module="postulaciones" title="Filtros">
            <form method="GET" class="space-y-4 p-5">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="min-w-[200px] flex-1">
                        <label for="buscar" class="mb-1 block text-xs font-semibold text-slate-500">Buscar</label>
                        <input type="text" id="buscar" name="buscar" value="{{ request('buscar') }}"
                               placeholder="RUDE, CI, nombre, código…"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>

                    <div>
                        <label for="id_ges_oac" class="mb-1 block text-xs font-semibold text-slate-500">Gestión</label>
                        <select id="id_ges_oac" name="id_ges_oac" class="min-w-[140px] rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                            <option value="">Todas</option>
                            @foreach($gestiones as $g)
                                <option value="{{ $g->id_ges }}" @selected(request('id_ges_oac') == $g->id_ges)>{{ $g->nombre_ges }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="id_ept_pos" class="mb-1 block text-xs font-semibold text-slate-500">Estado</label>
                        <select id="id_ept_pos" name="id_ept_pos" class="min-w-[140px] rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                            <option value="">Todos</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->id_ept }}" @selected(request('id_ept_pos') == $estado->id_ept)>
                                    {{ $estado->nombre_ept }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="id_cur_oac" class="mb-1 block text-xs font-semibold text-slate-500">Curso (oferta)</label>
                        <select id="id_cur_oac" name="id_cur_oac" class="min-w-[140px] rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                            <option value="">Todos</option>
                            @foreach($cursos as $curso)
                                <option value="{{ $curso->id_cur }}" @selected(request('id_cur_oac') == $curso->id_cur)>
                                    {{ $curso->nombre_cur }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="fecha_desde" class="mb-1 block text-xs font-semibold text-slate-500">Desde</label>
                        <input type="date" id="fecha_desde" name="fecha_desde" value="{{ request('fecha_desde') }}"
                               class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                    </div>
                    <div>
                        <label for="fecha_hasta" class="mb-1 block text-xs font-semibold text-slate-500">Hasta</label>
                        <input type="date" id="fecha_hasta" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                               class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                    </div>

                    <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Filtrar</button>
                    <a href="{{ route('admin.institucional.postulaciones.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Limpiar</a>
                    <x-admin.export-report route="admin.institucional.postulaciones.export" class="sm:ml-auto" />
                </div>
            </form>
        </x-institucional.panel>

        <x-institucional.panel module="postulaciones" title="Listado de postulaciones">
            <div class="overflow-x-auto">
                <table data-inst-table class="w-full text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-left">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Postulante</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">RUDE</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Gestión / curso</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Fecha</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Estado</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Puntaje</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-400"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($postulaciones as $p)
                            @php
                                $per = $p->estudiante?->persona;
                                $nombre = trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? '').' '.($per->ap_materno_per ?? ''));
                                $oac = $p->ofertaAcademica;
                            @endphp
                            <tr class="border-b border-slate-50 hover:bg-indigo-50/30 last:border-0">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-slate-900">{{ $nombre ?: '—' }}</p>
                                    <p class="text-xs text-slate-400">CI: {{ $per->ci_per ?? '—' }}</p>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-emerald-800">{{ $p->estudiante->rude_est ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-700">
                                    <p>{{ $oac->gestion->nombre_ges ?? '—' }}</p>
                                    <p class="text-xs text-slate-500">
                                        {{ $oac->nivel->nombre_niv ?? '' }}
                                        {{ $oac->curso->nombre_cur ?? '—' }}
                                        {{ $oac->paralelo->nombre_par ?? '' }}
                                    </p>
                                </td>
                                <td class="px-4 py-3 text-slate-500">{{ $p->fecha_pos?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @include('admin.institucional.postulaciones._estado-badge', ['estado' => $p->estadoPostulacion->nombre_ept ?? null])
                                </td>
                                <td class="px-4 py-3 font-semibold text-indigo-600">
                                    {{ $p->resultado?->puntaje_total_res !== null ? number_format((float) $p->resultado->puntaje_total_res, 2) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.institucional.postulaciones.show', $p) }}"
                                       class="text-xs font-semibold text-indigo-600 hover:underline">Ver detalle</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                                    No hay postulaciones para su unidad con los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($postulaciones->hasPages())
                <div class="flex items-center justify-between border-t border-slate-100 px-4 py-3">
                    <span class="text-sm text-slate-500">
                        {{ $postulaciones->firstItem() }}–{{ $postulaciones->lastItem() }} de {{ $postulaciones->total() }}
                    </span>
                    {{ $postulaciones->links() }}
                </div>
            @endif
        </x-institucional.panel>
    </x-institucional.page>
@endsection
