@extends('layouts.dashboard')

@section('title', 'Tutor | Estudiantes')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Estudiantes</span>
@endsection

@section('content')
    <div class="mb-6">
        <p class="text-xs text-slate-400">Tutor / Estudiantes</p>
        <h1 class="text-2xl font-bold text-slate-900">Mis estudiantes</h1>
        <p class="mt-1 text-sm text-slate-500">Relación vía tabla <code class="rounded bg-slate-100 px-1 text-xs">estudiante_tutor</code> y datos de <code class="rounded bg-slate-100 px-1 text-xs">persona</code>.</p>
    </div>

    @if($tutor === null)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-amber-900">
            <p class="font-semibold">No hay perfil de tutor asociado.</p>
        </div>
    @elseif($tutor->estudiantes->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <p class="text-slate-600">Aún no tienes estudiantes vinculados.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">#</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Nombre</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Código</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">CI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($tutor->estudiantes as $est)
                            @php
                                $p = $est->persona;
                                $nombre = trim(($p->nombres_per ?? '').' '.($p->ap_paterno_per ?? '').' '.($p->ap_materno_per ?? ''));
                            @endphp
                            <tr>
                                <td class="px-6 py-4 text-slate-500">{{ $est->id_est }}</td>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $nombre ?: '—' }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $est->codigo_est ?? '—' }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $p->ci_per ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
