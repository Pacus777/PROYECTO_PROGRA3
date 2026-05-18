@extends('layouts.dashboard')

@section('title', 'Resultados y ranking | Admin institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Resultados</span>
@endsection

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs text-slate-400">Panel / Resultados</p>
            <h1 class="text-2xl font-bold text-slate-900">Resultados y ranking</h1>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-admin.export-report route="admin.institucional.resultados.export" />
            <form method="POST" action="{{ route('admin.institucional.asignacion.store') }}">
                @csrf
                <button class="rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:from-indigo-700 hover:to-purple-700">Ejecutar asignación</button>
            </form>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="overflow-x-auto rounded-2xl bg-white shadow-sm">
            <h2 class="border-b border-slate-100 px-4 py-3 font-semibold text-slate-900">Resultados guardados</h2>
            <table class="min-w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-slate-500 uppercase text-xs"><tr><th class="px-4 py-3 text-left">Postulación</th><th class="px-4 py-3 text-left">Puntaje</th><th class="px-4 py-3 text-left">Clasificación</th></tr></thead>
                <tbody>
                    @foreach($resultados as $resultado)
                        <tr class="border-b border-slate-50 transition hover:bg-indigo-50/30 last:border-0">
                            <td class="px-4 py-3">{{ trim(($resultado->postulacion->estudiante->persona->nombres_per ?? '').' '.($resultado->postulacion->estudiante->persona->ap_paterno_per ?? '')) ?: '—' }}</td>
                            <td class="px-4 py-3 font-semibold text-indigo-600">{{ $resultado->puntaje_total_res }}</td>
                            <td class="px-4 py-3"><span class="inline-flex rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700">{{ $resultado->clasificacion_res }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4">{{ $resultados->links() }}</div>
        </section>

        <section class="overflow-x-auto rounded-2xl bg-white shadow-sm">
            <h2 class="border-b border-slate-100 px-4 py-3 font-semibold text-slate-900">Ranking calculado (preview)</h2>
            <table class="min-w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-slate-500 uppercase text-xs"><tr><th class="px-4 py-3 text-left">Postulación</th><th class="px-4 py-3 text-left">Curso</th><th class="px-4 py-3 text-left">Puntaje</th></tr></thead>
                <tbody>
                    @foreach($rankingPreview as $item)
                        <tr class="border-b border-slate-50 transition hover:bg-indigo-50/30 last:border-0">
                            <td class="px-4 py-3">{{ trim(($item->estudiante->persona->nombres_per ?? '').' '.($item->estudiante->persona->ap_paterno_per ?? '')) ?: '—' }}</td>
                            <td class="px-4 py-3">{{ $item->ofertaAcademica->curso->nombre_cur ?? '—' }}</td>
                            <td class="px-4 py-3 font-semibold text-indigo-600">{{ number_format((float) $item->puntaje_calc, 4) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </div>
@endsection

