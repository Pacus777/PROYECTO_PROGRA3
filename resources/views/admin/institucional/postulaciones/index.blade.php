@extends('layouts.dashboard')

@section('title', 'Postulaciones | Admin institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
    </svg>
    <span>Postulaciones</span>
@endsection

@section('content')
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <p class="text-xs text-slate-400">Panel / Postulaciones</p>
            <h1 class="text-2xl font-bold text-slate-900">Gestión de Postulaciones</h1>
            <p class="mt-1 text-sm text-slate-500">Gestiona y revisa todas las postulaciones</p>
        </div>
    </div>

    <form method="GET" class="mb-6 flex flex-wrap items-center gap-4 rounded-2xl bg-white p-4 shadow-sm">
        <div class="relative min-w-[220px] flex-1">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/>
            </svg>
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por estudiante..." class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>

        <select name="id_ept_pos" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <option value="">Todos los estados</option>
            @foreach($estados as $estado)<option value="{{ $estado->id_ept }}" @selected(request('id_ept_pos') == $estado->id_ept)>{{ $estado->nombre_ept }}</option>@endforeach
        </select>

        <select name="id_cur_oac" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <option value="">Todos los cursos</option>
            @foreach($cursos as $curso)<option value="{{ $curso->id_cur }}" @selected(request('id_cur_oac') == $curso->id_cur)>{{ $curso->nombre_cur }}</option>@endforeach
        </select>

        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-300">
        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-300">

        <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-indigo-700">Filtrar</button>
        <a href="{{ route('admin.institucional.postulaciones.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Limpiar</a>
        <x-admin.export-report route="admin.institucional.postulaciones.export" class="ml-auto" />
        <span class="w-full text-sm text-slate-500 sm:w-auto sm:ml-0">Mostrando {{ $postulaciones->count() }} en esta página</span>
    </form>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        @if($postulaciones->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">#</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Estudiante</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Nivel/Curso</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Fecha</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Estado</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Puntaje</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-400">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($postulaciones as $p)
                    @php
                        $nombre = trim(($p->estudiante->persona->nombres_per ?? '').' '.($p->estudiante->persona->ap_paterno_per ?? '')) ?: '—';
                        $correo = $p->estudiante->persona->correo_per ?? ($p->estudiante->codigo_est ? 'Cód. '.$p->estudiante->codigo_est : 'Sin contacto');
                        $inicial = strtoupper(mb_substr($nombre, 0, 1));
                        $estado = strtolower($p->estadoPostulacion->nombre_ept ?? 'pendiente');
                        $badge = match (true) {
                            str_contains($estado, 'aprob') => 'bg-emerald-100 text-emerald-700',
                            str_contains($estado, 'rechaz') => 'bg-red-100 text-red-600',
                            str_contains($estado, 'evalu') => 'bg-blue-100 text-blue-700',
                            default => 'bg-amber-100 text-amber-700',
                        };
                    @endphp
                    <tr class="border-b border-slate-50 transition-colors duration-150 hover:bg-indigo-50/30 last:border-0">
                        <td class="px-6 py-4 text-slate-500">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-indigo-400 to-purple-400 text-xs font-bold text-white">{{ $inicial }}</div>
                                <div>
                                    <p class="font-medium text-slate-800">{{ $nombre }}</p>
                                    <p class="text-xs text-slate-400">{{ $correo }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-700">{{ $p->ofertaAcademica->curso->nombre_cur ?? '—' }}</td>
                        <td class="px-6 py-4 text-xs text-slate-500">{{ optional($p->fecha_pos)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $badge }}">
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                {{ $p->estadoPostulacion->nombre_ept ?? 'Pendiente' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-indigo-600">{{ $p->resultado->puntaje_total_res ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.institucional.postulaciones.show', $p) }}" class="rounded-lg bg-slate-100 p-2 text-slate-500 transition hover:bg-indigo-100 hover:text-indigo-600" title="Ver detalle">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-indigo-500">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M5 7v11a2 2 0 002 2h10a2 2 0 002-2V7M9 11h6"/>
                    </svg>
                </div>
                <p class="mt-4 font-semibold text-slate-700">Sin datos aún</p>
                <p class="mt-2 text-sm text-slate-400">No se encontraron postulaciones para los filtros seleccionados.</p>
            </div>
        @endif

        <div class="flex items-center justify-between border-t border-slate-100 px-6 py-4">
            <span class="text-sm text-slate-500">Mostrando {{ $postulaciones->count() }} resultados</span>
            {{ $postulaciones->links() }}
        </div>
    </div>
@endsection

