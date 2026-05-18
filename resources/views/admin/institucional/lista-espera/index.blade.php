@extends('layouts.dashboard')

@section('title', 'Lista de espera | Admin institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Lista de espera</span>
@endsection

@section('content')
    @php
        $pageSubtitle = $unidad
            ? trim($unidad->nombre_ued . ($unidad->codigo_ued ? ' (' . $unidad->codigo_ued . ')' : '')) . ' — postulantes en cola por oferta cuando no hay cupos disponibles.'
            : 'Postulantes en cola por oferta cuando no hay cupos disponibles.';
    @endphp

    <x-institucional.page module="lista-espera" title="Lista de espera" :subtitle="$pageSubtitle">
        <x-slot:actions>
            <x-admin.export-report route="admin.institucional.lista-espera.export" />
            <a href="{{ route('admin.institucional.asignacion.index') }}"
               class="rounded-xl border border-white/40 bg-white/15 px-4 py-2 text-sm font-semibold text-white backdrop-blur-sm hover:bg-white/25">
                Ver asignaciones
            </a>
        </x-slot:actions>

        <x-slot:kpis>
            <x-institucional.kpi-grid module="lista-espera" :items="[
                ['label' => 'En espera', 'value' => $resumen['total']],
                ['label' => 'Ofertas con cola', 'value' => $resumen['ofertas_con_espera']],
                ['label' => 'Primeros en cola', 'value' => $resumen['primeros_en_cola']],
                ['label' => 'Cupos libres (ofertas en cola)', 'value' => $resumen['cupos_libres_en_ofertas_con_espera']],
            ]" />
        </x-slot:kpis>

        @if($porOferta->isNotEmpty())
            <x-institucional.panel module="lista-espera" title="Colas por oferta">
                <div class="grid gap-3 p-5 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach($porOferta as $fila)
                        @php
                            $oac = $fila->oferta;
                            $primero = $fila->primero;
                            $per = $primero?->postulacion?->estudiante?->persona;
                            $nomPrimero = $primero ? trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? '')) : null;
                        @endphp
                        <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-3">
                            <p class="text-xs font-semibold text-slate-500">{{ $oac->gestion->nombre_ges ?? '' }}</p>
                            <p class="font-medium text-slate-900">{{ $oac->curso->nombre_cur ?? '—' }} · {{ $oac->paralelo->nombre_par ?? '' }}</p>
                            <p class="mt-2 text-xs text-slate-600">
                                <span class="font-semibold text-amber-700">{{ $fila->en_espera }}</span> en espera ·
                                <span class="font-semibold text-emerald-700">{{ $fila->cupos_disponibles }}</span> cupo(s) libre(s)
                            </p>
                            @if($nomPrimero)
                                <p class="mt-1 truncate text-xs text-indigo-700" title="{{ $nomPrimero }}">1° {{ $nomPrimero }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-institucional.panel>
        @endif

        <x-institucional.panel module="lista-espera" title="Filtros">
            <form method="GET" class="space-y-3 p-5">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="min-w-[180px] flex-1">
                        <label class="mb-1 block text-xs font-semibold text-slate-500">Buscar</label>
                        <input type="text" name="buscar" value="{{ request('buscar') }}"
                               placeholder="Nombre o RUDE"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
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
                    <a href="{{ route('admin.institucional.lista-espera.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Limpiar</a>
                </div>
            </form>
        </x-institucional.panel>

        <x-institucional.panel module="lista-espera" :title="'Cola de espera ('.$registros->total().')'">
            <div class="overflow-x-auto" data-inst-table>
                <table class="min-w-full text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-left">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Orden</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Postulante</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Oferta</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Puntaje</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Estado</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registros as $les)
                            @php
                                $p = $les->postulacion;
                                $per = $p?->estudiante?->persona;
                                $nombre = trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? '').' '.($per->ap_materno_per ?? ''));
                                $oac = $les->ofertaAcademica;
                                $esPrimero = $listaEsperaService->esPrimeroEnOferta($les);
                                $puedeAsignar = $listaEsperaService->puedeAsignarCupo($les);
                                $cupoDisp = (int) ($oac->cupos->first()->disponibles_cup ?? 0);
                            @endphp
                            <tr @if($loop->first) data-inst-queue-row @endif class="border-b border-slate-50 hover:bg-amber-50/20 last:border-0">
                                <td class="px-4 py-3">
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full {{ $esPrimero ? 'bg-amber-200 text-amber-900' : 'bg-slate-100 text-slate-600' }} text-xs font-bold">
                                        {{ $les->orden_les }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-slate-900">{{ $nombre ?: '—' }}</p>
                                    <p class="font-mono text-xs text-emerald-800">{{ $p->estudiante->rude_est ?? '—' }}</p>
                                    @if($p)
                                        <a href="{{ route('admin.institucional.postulaciones.show', $p) }}" class="text-xs text-indigo-600 hover:underline">Ver postulación</a>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-700">
                                    <p class="text-xs text-slate-500">{{ $oac->gestion->nombre_ges ?? '' }}</p>
                                    <p>{{ $oac->curso->nombre_cur ?? '—' }} · {{ $oac->paralelo->nombre_par ?? '' }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $cupoDisp }} cupo(s) libre(s)</p>
                                </td>
                                <td class="px-4 py-3 font-semibold text-indigo-600">
                                    {{ $p?->resultado ? number_format((float) $p->resultado->puntaje_total_res, 2) : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($esPrimero)
                                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">Primero en cola</span>
                                    @else
                                        <span class="text-xs text-slate-400">En cola</span>
                                    @endif
                                    <p class="mt-1 text-xs text-slate-500">{{ $p->estadoPostulacion->nombre_ept ?? '—' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if($puedeAsignar)
                                        <form method="POST" action="{{ route('admin.institucional.lista-espera.asignar-cupo', $les) }}"
                                              onsubmit="return confirm('¿Asignar cupo a este postulante?')">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                                                Asignar cupo
                                            </button>
                                        </form>
                                    @elseif($esPrimero && $cupoDisp === 0)
                                        <span class="text-xs text-slate-400">Sin cupos libres</span>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-14 text-center">
                                    <p class="font-medium text-slate-600">No hay postulantes en lista de espera</p>
                                    <p class="mt-2 text-sm text-slate-400">
                                        Se genera al ejecutar la asignación desde
                                        <a href="{{ route('admin.institucional.asignacion.index') }}" class="font-semibold text-indigo-600 hover:underline">Asignación</a>
                                        cuando los cupos están llenos.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($registros->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">{{ $registros->links() }}</div>
            @endif
        </x-institucional.panel>
    </x-institucional.page>
@endsection
