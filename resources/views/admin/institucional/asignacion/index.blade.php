@extends('layouts.dashboard')

@section('title', 'Asignación de cupos | Admin institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Asignación</span>
@endsection

@section('content')
    @php
        $pageSubtitle = $unidad
            ? trim($unidad->nombre_ued . ($unidad->codigo_ued ? ' (' . $unidad->codigo_ued . ')' : '')) . ' — ejecute la asignación automática según el ranking de su unidad.'
            : 'Cupos asignados tras el proceso de admisión.';

        $estadoClasses = [
            'asignado' => 'bg-indigo-50 text-indigo-700',
            'pendiente' => 'bg-amber-50 text-amber-700',
            'rechazado' => 'bg-rose-50 text-rose-700',
            'aceptado' => 'bg-emerald-50 text-emerald-700',
            'vencido' => 'bg-amber-50 text-amber-700',
        ];
    @endphp

    <x-institucional.page module="asignacion" title="Asignación de cupos" :subtitle="$pageSubtitle">
        <x-slot:actions>
            <a href="{{ route('admin.institucional.lista-espera.index') }}"
               class="rounded-xl border border-white/40 bg-white/15 px-4 py-2 text-sm font-semibold text-white backdrop-blur-sm hover:bg-white/25">
                Lista de espera ({{ $resumen['lista_espera'] }})
            </a>
            <a href="{{ route('admin.institucional.resultados.index') }}"
               class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-orange-700 shadow-sm hover:bg-orange-50">
                Ver ranking
            </a>
        </x-slot:actions>

        <x-slot:kpis>
            <x-institucional.kpi-grid module="asignacion" :items="[
                ['label' => 'Con evaluación', 'value' => $resumen['con_evaluacion']],
                ['label' => 'Cupos asignados', 'value' => $resumen['asignados']],
                ['label' => 'Lista de espera', 'value' => $resumen['lista_espera']],
                ['label' => 'Ofertas con cupo', 'value' => $resumen['ofertas_con_cupo']],
            ]" />
        </x-slot:kpis>

        <x-institucional.panel module="asignacion" title="Ejecutar proceso">
            <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-orange-900">
                    Asigna cupos según el ranking (evaluaciones × pesos). Puntúe en
                    <a href="{{ route('admin.institucional.postulaciones.index') }}" class="font-semibold underline">Postulaciones</a>
                    o revise el <a href="{{ route('admin.institucional.resultados.index') }}" class="font-semibold underline">ranking</a> antes de ejecutar.
                </p>
                <form method="POST" action="{{ route('admin.institucional.asignacion.store') }}"
                      onsubmit="return confirm('¿Ejecutar asignación de cupos? Se actualizarán asignaciones, lista de espera y cupos disponibles.')">
                    @csrf
                    <button type="submit" data-inst-primary-btn class="w-full rounded-xl px-6 py-3 text-sm font-bold text-white sm:w-auto">
                        Ejecutar asignación
                    </button>
                </form>
            </div>
        </x-institucional.panel>

        <x-institucional.panel module="asignacion" title="Filtros">
            <form method="GET" class="flex flex-wrap items-end gap-3 p-5">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-500">Oferta</label>
                    <select name="id_oac_pos" class="min-w-[200px] rounded-xl border border-orange-200 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-200">
                        <option value="">Todas</option>
                        @foreach($ofertasUnidad as $o)
                            <option value="{{ $o->id_oac }}" @selected(request('id_oac_pos') == $o->id_oac)>
                                {{ $o->gestion->nombre_ges ?? '' }} · {{ $o->curso->nombre_cur ?? '' }} {{ $o->paralelo->nombre_par ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-xl bg-orange-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-orange-700">Filtrar</button>
                <a href="{{ route('admin.institucional.asignacion.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Limpiar</a>
            </form>
        </x-institucional.panel>

        <x-institucional.panel module="asignacion" :title="'Cupos asignados ('.$asignaciones->total().')'">
            <div class="overflow-x-auto" data-inst-table>
                <table class="min-w-full text-sm">
                    <thead class="bg-orange-600 text-left text-white">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold uppercase">Postulante</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase">Oferta</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase">Puntaje</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase">Estado</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase">Fecha</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase">Fecha límite</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asignaciones as $asi)
                            @php
                                $p = $asi->postulacion;
                                $per = $p?->estudiante?->persona;
                                $nombre = trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? '').' '.($per->ap_materno_per ?? ''));
                                $oac = $asi->cupo?->ofertaAcademica;
                            @endphp
                            <tr class="border-b border-orange-50 hover:bg-orange-50/50 last:border-0">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-slate-900">{{ $nombre ?: '—' }}</p>
                                    @if($p)
                                        <a href="{{ route('admin.institucional.postulaciones.show', $p) }}" class="text-xs font-semibold text-orange-700 hover:underline">Ver postulación</a>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-700">
                                    {{ $oac->gestion->nombre_ges ?? '' }} · {{ $oac->curso->nombre_cur ?? '—' }} {{ $oac->paralelo->nombre_par ?? '' }}
                                </td>
                                <td class="px-4 py-3 font-bold text-orange-700">
                                    {{ $p?->resultado ? number_format((float) $p->resultado->puntaje_total_res, 2) : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $estadoClasses[$asi->estado_asi] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ $asi->estado_asi ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $asi->fecha_asi?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ $asi->fecha_limite_respuesta_asi?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                                    No hay cupos asignados aún. Ejecute la asignación cuando haya evaluaciones y cupos configurados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($asignaciones->hasPages())
                <div class="border-t border-orange-100 px-4 py-3">{{ $asignaciones->links() }}</div>
            @endif
        </x-institucional.panel>
    </x-institucional.page>
@endsection
