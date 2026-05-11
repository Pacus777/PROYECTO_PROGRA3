@extends('layouts.dashboard')

@section('title', 'Tutor | Inicio')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Tutor</span>
@endsection

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs text-slate-400">Tutor / Inicio</p>
            <h1 class="text-2xl font-bold text-slate-900">Panel del tutor</h1>
            <p class="mt-1 text-sm text-slate-500">Resumen de postulaciones de tus estudiantes vinculados.</p>
        </div>
        <a href="{{ route('tutor.postulaciones.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:from-teal-700 hover:to-emerald-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nueva postulación
        </a>
    </div>

    @if($tutor === null)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-amber-900">
            <p class="font-semibold">No hay perfil de tutor asociado.</p>
            <p class="mt-2 text-sm text-amber-800">Contacta a un administrador para vincular tu usuario con la tabla de tutores.</p>
        </div>
    @elseif($tutor->estudiantes->isEmpty())
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-amber-900">
            <p class="font-semibold">Aún no tienes estudiantes vinculados.</p>
            <p class="mt-2 text-sm text-amber-800">Un administrador debe asignarte estudiantes antes de poder registrar postulaciones. Mientras tanto puedes revisar las ofertas disponibles.</p>
            <a href="{{ route('tutor.postulaciones.index') }}"
               class="mt-4 inline-flex items-center gap-2 rounded-xl bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-900 transition hover:bg-amber-200">
                Ver mis postulaciones →
            </a>
        </div>
    @else
        <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <article class="rounded-2xl bg-gradient-to-br from-teal-500 to-teal-700 p-6 text-white shadow-lg">
                <p class="text-xs font-semibold uppercase tracking-wide text-teal-100">Postulaciones</p>
                <p class="mt-2 text-4xl font-black">{{ $totalPostulaciones }}</p>
                <p class="mt-1 text-sm text-teal-100">Total de tus estudiantes</p>
                <a href="{{ route('tutor.postulaciones.index') }}" class="mt-3 inline-block text-xs font-semibold text-teal-200 hover:text-white">Ver todas →</a>
            </article>
            <a href="{{ route('tutor.estudiantes.index') }}"
               class="group rounded-2xl border border-slate-100 bg-white p-6 shadow-sm transition hover:border-teal-200 hover:bg-teal-50">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Estudiantes vinculados</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $tutor->estudiantes->count() }}</p>
                <span class="mt-3 inline-block text-sm font-semibold text-teal-600 group-hover:underline">Ver listado →</span>
            </a>
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Acciones rápidas</p>
                <div class="mt-4 flex flex-col gap-3">
                    <a href="{{ route('tutor.postulaciones.create') }}"
                       class="flex items-center gap-3 rounded-xl bg-teal-50 px-4 py-2.5 text-sm font-semibold text-teal-700 transition hover:bg-teal-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Registrar postulación
                    </a>
                    <a href="{{ route('tutor.seguimiento.index') }}"
                       class="flex items-center gap-3 rounded-xl bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Ver seguimiento
                    </a>
                    <a href="{{ route('tutor.resultados.index') }}"
                       class="flex items-center gap-3 rounded-xl bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8M12 17v4M7 4h10v5a5 5 0 01-10 0V4z"/></svg>
                        Mis resultados
                    </a>
                </div>
            </div>
        </div>

        <section class="overflow-hidden rounded-2xl bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-800">Últimas postulaciones</h2>
            </div>
            @if($postulacionesRecientes->isEmpty())
                <p class="p-8 text-center text-sm text-slate-500">No hay postulaciones registradas aún.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-slate-100 bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Estudiante</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Oferta</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-400"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($postulacionesRecientes as $pos)
                                @php
                                    $nom = trim(($pos->estudiante->persona->nombres_per ?? '').' '.($pos->estudiante->persona->ap_paterno_per ?? ''));
                                    $oferta = $pos->ofertaAcademica;
                                    $txt = $oferta ? trim(implode(' · ', array_filter([$oferta->gestion->nombre_ges ?? null, $oferta->curso->nombre_cur ?? null, $oferta->paralelo->nombre_par ?? null]))) : '';
                                @endphp
                                <tr class="text-slate-700">
                                    <td class="px-6 py-3 font-medium text-slate-900">{{ $nom ?: '—' }}</td>
                                    <td class="px-6 py-3">{{ $txt ?: '—' }}</td>
                                    <td class="px-6 py-3">
                                        <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">{{ $pos->estadoPostulacion->nombre_ept ?? '—' }}</span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('tutor.postulaciones.show', $pos) }}" class="font-semibold text-indigo-600 hover:underline">Ver</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endif
@endsection
