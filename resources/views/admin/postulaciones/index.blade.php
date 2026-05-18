@extends('layouts.dashboard')

@section('title', 'Postulaciones | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Postulaciones</span>
@endsection

@section('content')
    <div class="mb-8">
        <p class="text-xs text-slate-400">Panel / Postulaciones</p>
        <h1 class="text-2xl font-bold text-slate-900">Postulaciones</h1>
        <p class="mt-1 text-sm text-slate-500">Vista nacional con filtros por departamento, provincia, municipio, distrito educativo o unidad.</p>
    </div>

    <form method="GET" class="mb-6 space-y-4 rounded-2xl bg-white p-4 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Ámbito territorial</p>
        <x-admin.filtro-territorio :departamentos="$departamentos" mode="filter" />

        <p class="text-xs font-bold uppercase tracking-wide text-slate-400 pt-2">Otros filtros</p>
        <div class="flex flex-wrap items-end gap-3">
            <div class="min-w-[180px] flex-1">
                <label class="mb-1 block text-xs font-semibold text-slate-500">Buscar</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="RUDE, nombre, CI…"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-500">Gestión</label>
                <select name="id_ges" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm min-w-[140px]">
                    <option value="">Todas</option>
                    @foreach($gestiones as $g)
                        <option value="{{ $g->id_ges }}" @selected(request('id_ges') == $g->id_ges)>{{ $g->nombre_ges }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-500">Estado</label>
                <select name="id_ept_pos" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm min-w-[140px]">
                    <option value="">Todos</option>
                    @foreach($estados as $e)
                        <option value="{{ $e->id_ept }}" @selected(request('id_ept_pos') == $e->id_ept)>{{ $e->nombre_ept }}</option>
                    @endforeach
                </select>
            </div>
            <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" title="Desde"
                   class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
            <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" title="Hasta"
                   class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
            <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Filtrar</button>
            <a href="{{ route('admin.postulaciones.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Limpiar</a>
            <x-admin.export-report route="admin.postulaciones.export" class="ml-auto" />
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Postulante</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">RUDE</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Departamento</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Municipio</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Unidad</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Curso</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Estado</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Fecha</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-400"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($postulaciones as $p)
                        @php
                            $per = $p->estudiante->persona;
                            $nom = trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? ''));
                            $ue = $p->ofertaAcademica->unidadEducativa;
                            $mun = $ue?->municipio;
                            $dep = $mun?->provincia?->departamento;
                        @endphp
                        <tr class="border-b border-slate-50 hover:bg-indigo-50/30 last:border-0">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $nom ?: '—' }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-emerald-800">{{ $p->estudiante->rude_est ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $dep->nombre_dep ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $mun->nombre_mun ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $ue->nombre_ued ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $p->ofertaAcademica->curso->nombre_cur ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $p->estadoPostulacion->nombre_ept ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $p->fecha_pos?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.postulaciones.show', $p) }}" class="text-xs font-semibold text-indigo-600 hover:underline">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-12 text-center text-slate-500">Sin postulaciones con estos filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($postulaciones->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $postulaciones->links() }}</div>
        @endif
    </div>
@endsection
