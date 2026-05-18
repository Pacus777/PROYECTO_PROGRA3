@extends('layouts.dashboard')

@section('title', 'Tutor | Postulaciones')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Postulaciones</span>
@endsection

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <p class="text-xs text-slate-400">Tutor / Postulaciones</p>
            <h1 class="text-2xl font-bold text-slate-900">Postulaciones</h1>
            <p class="mt-1 text-sm text-slate-500">Solo postulaciones de estudiantes vinculados a tu perfil.</p>
        </div>
        <a href="{{ route('tutor.postulaciones.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-indigo-700">Nueva postulación</a>
    </div>

    <form method="GET" class="mb-6 flex flex-wrap items-center gap-3 rounded-2xl bg-white p-4 shadow-sm">
        <select name="id_ept_pos" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <option value="">Todos los estados</option>
            @foreach($estados as $estado)
                <option value="{{ $estado->id_ept }}" @selected(request('id_ept_pos') == $estado->id_ept)>{{ $estado->nombre_ept }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">Filtrar</button>
    </form>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        @if($postulaciones->total() === 0)
            <p class="p-8 text-center text-sm text-slate-500">No hay postulaciones para mostrar.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">#</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Prioridad</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Estudiante</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Curso</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Fecha</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Avance
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Puntaje</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($postulaciones as $pos)
                            @php
                                $nom = trim(($pos->estudiante->persona->nombres_per ?? '').' '.($pos->estudiante->persona->ap_paterno_per ?? ''));
                            @endphp
                            <tr class="text-slate-700">
                                <td class="px-6 py-4 text-slate-500">{{ $pos->id_pos }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                                        {{ $pos->prioridad_pos }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $nom ?: '—' }}</td>
                                <td class="px-6 py-4">{{ $pos->ofertaAcademica->curso->nombre_cur ?? '—' }} {{ $pos->ofertaAcademica->paralelo->nombre_par ?? '' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-600">{{ optional($pos->fecha_pos)->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">{{ $pos->estadoPostulacion->nombre_ept ?? '—' }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    @php
                                        $porcentaje = $pos->porcentajeDocumental();

                                        $etapaTexto = [
                                            'registrada' => 'Registrada',
                                            'documentos_revision' => 'Docs. en revisión',
                                            'documentos_completos' => 'Docs. completos',
                                            'resultado' => 'Resultado generado',
                                            'asignado' => 'Cupo asignado',
                                            'lista_espera' => 'Lista de espera',
                                        ];

                                        $etapa = $pos->etapaTutor();
                                    @endphp

                                    <div class="min-w-[150px]">
                                        <div class="mb-1 flex items-center justify-between text-xs">
                                            <span class="font-semibold text-slate-600">
                                                {{ $etapaTexto[$etapa] ?? 'En proceso' }}
                                            </span>
                                            <span class="text-slate-400">{{ $porcentaje }}%</span>
                                        </div>

                                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                            <div class="h-full rounded-full bg-indigo-500" style="width: {{ $porcentaje }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">{{ $pos->resultado->puntaje_total_res ?? '—' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('tutor.postulaciones.show', $pos) }}" class="font-semibold text-indigo-600 hover:underline">Ver</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $postulaciones->links() }}
            </div>
        @endif
    </div>
@endsection
