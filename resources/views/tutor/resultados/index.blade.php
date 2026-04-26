@extends('layouts.dashboard')

@section('title', 'Tutor | Resultados')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Resultados</span>
@endsection

@section('content')
    <div class="mb-6">
        <p class="text-xs text-slate-400">Tutor / Resultados</p>
        <h1 class="text-2xl font-bold text-slate-900">Resultados y cierre</h1>
        <p class="mt-1 text-sm text-slate-500">Puntajes, asignaciones de cupo y posición en lista de espera.</p>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        @if($postulaciones->count() === 0)
            <p class="p-8 text-center text-sm text-slate-500">No hay postulaciones para mostrar.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">#</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Estudiante</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Oferta</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Puntaje</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Asignación</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Lista espera</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-400"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($postulaciones as $pos)
                            @php
                                $nom = trim(($pos->estudiante->persona->nombres_per ?? '').' '.($pos->estudiante->persona->ap_paterno_per ?? ''));
                                $oac = $pos->ofertaAcademica;
                                $ofertaTxt = $oac ? trim(implode(' · ', array_filter([$oac->gestion->nombre_ges ?? null, $oac->curso->nombre_cur ?? null, $oac->paralelo->nombre_par ?? null]))) : '';
                                $asi = $pos->asignaciones->first();
                                $les = $pos->listasEspera->first();
                            @endphp
                            <tr class="text-slate-700">
                                <td class="px-6 py-4 text-slate-500">{{ $pos->id_pos }}</td>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $nom ?: '—' }}</td>
                                <td class="px-6 py-4">{{ $ofertaTxt ?: '—' }}</td>
                                <td class="px-6 py-4"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold">{{ $pos->estadoPostulacion->nombre_ept ?? '—' }}</span></td>
                                <td class="px-6 py-4">{{ $pos->resultado->puntaje_total_res ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    @if($asi)
                                        <span class="text-xs">{{ $asi->estado_asi ?? '—' }}</span>
                                        @if($asi->cupo && $asi->cupo->ofertaAcademica)
                                            <p class="text-xs text-slate-500">Cupo oferta #{{ $asi->cupo->id_oac_cup }}</p>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($les)
                                        <span class="text-xs font-semibold text-amber-700">Orden {{ $les->orden_les }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
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
