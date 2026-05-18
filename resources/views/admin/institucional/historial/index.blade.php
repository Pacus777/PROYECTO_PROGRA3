@extends('layouts.dashboard')

@section('title', 'Historial | Admin institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Historial</span>
@endsection

@section('content')
    @php
        $pageSubtitle = $unidad
            ? trim($unidad->nombre_ued . ($unidad->codigo_ued ? ' (' . $unidad->codigo_ued . ')' : '')) . ' — registro de acciones sobre postulaciones, documentos, cupos y evaluaciones.'
            : 'Registro de acciones sobre postulaciones, documentos, cupos y evaluaciones.';
    @endphp

    <x-institucional.page module="historial" title="Historial de actividad" :subtitle="$pageSubtitle">
        <x-slot:actions>
            <x-admin.export-report route="admin.institucional.historial.export" />
        </x-slot:actions>

        <x-slot:kpis>
            <x-institucional.kpi-grid module="historial" :items="[
                ['label' => 'Eventos (filtros)', 'value' => $resumen['total']],
                ['label' => 'Hoy', 'value' => $resumen['hoy']],
                ['label' => 'Últimos 7 días', 'value' => $resumen['semana']],
                ['label' => 'Módulos activos', 'value' => $resumen['por_modulo']->count()],
            ]" />
        </x-slot:kpis>

        @if($resumen['por_modulo']->isNotEmpty())
            <div class="mb-6 flex flex-wrap gap-2">
                @foreach($resumen['por_modulo'] as $m)
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                        {{ $m->label }}: {{ $m->total }}
                    </span>
                @endforeach
            </div>
        @endif

        <x-institucional.panel module="historial" title="Filtros">
            <form method="GET" class="space-y-3 p-5">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="min-w-[160px] flex-1">
                        <label class="mb-1 block text-xs font-semibold text-slate-500">Buscar</label>
                        <input type="text" name="buscar" value="{{ request('buscar') }}"
                               placeholder="Descripción o usuario"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-500">Módulo</label>
                        <select name="modulo" class="min-w-[140px] rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                            <option value="">Todos</option>
                            @foreach($modulos as $key => $label)
                                <option value="{{ $key }}" @selected(request('modulo') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-500">Acción</label>
                        <select name="accion" class="min-w-[140px] rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                            <option value="">Todas</option>
                            @foreach($acciones as $key => $label)
                                <option value="{{ $key }}" @selected(request('accion') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-500">Desde</label>
                        <input type="date" name="desde" value="{{ request('desde') }}"
                               class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-500">Hasta</label>
                        <input type="date" name="hasta" value="{{ request('hasta') }}"
                               class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                    </div>
                    <button type="submit" class="rounded-xl bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-900">Filtrar</button>
                    <a href="{{ route('admin.institucional.historial.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Limpiar</a>
                </div>
            </form>
        </x-institucional.panel>

        <x-institucional.panel module="historial" title="Línea de tiempo">
            <div class="divide-y divide-slate-100">
                @forelse($eventos as $evento)
                    <div data-inst-timeline-item class="relative flex gap-4 px-4 py-4 hover:bg-slate-50/80">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $evento->origen === 'auditoria' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-600">{{ $evento->modulo_label }}</span>
                                <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-semibold text-indigo-700">{{ $evento->accion_label }}</span>
                                @if($evento->origen === 'auditoria')
                                    <span class="rounded-full bg-violet-50 px-2 py-0.5 text-[10px] font-semibold text-violet-700">Auditoría</span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm font-medium text-slate-900">{{ $evento->descripcion }}</p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $evento->fecha->format('d/m/Y H:i') }}
                                @if($evento->usuario)
                                    · {{ $evento->usuario }}
                                @endif
                            </p>
                            @if($evento->url)
                                <a href="{{ $evento->url }}" class="mt-1 inline-block text-xs font-semibold text-indigo-600 hover:underline">Ver detalle →</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-16 text-center text-slate-500">
                        <p class="font-medium">Sin eventos para los filtros seleccionados</p>
                        <p class="mt-2 text-sm">La actividad aparecerá al registrar postulaciones, evaluaciones y asignaciones.</p>
                    </div>
                @endforelse
            </div>
            @if($eventos->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">{{ $eventos->links() }}</div>
            @endif
        </x-institucional.panel>
    </x-institucional.page>
@endsection
