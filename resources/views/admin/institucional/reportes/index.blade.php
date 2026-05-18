@extends('layouts.dashboard')

@section('title', 'Reportes | Admin institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Reportes</span>
@endsection

@section('content')
    @php
        $pageSubtitle = $unidad
            ? trim($unidad->nombre_ued . ($unidad->codigo_ued ? ' (' . $unidad->codigo_ued . ')' : '')) . ' — indicadores y descargas en Excel o PDF de su proceso de admisión.'
            : 'Indicadores y descargas en Excel o PDF de su proceso de admisión.';
    @endphp

    <x-institucional.page module="reportes" title="Reportes de la unidad" :subtitle="$pageSubtitle">
        <x-slot:kpis>
            <x-institucional.kpi-grid module="reportes" :items="[
                ['label' => 'Postulaciones', 'value' => $indicadores['postulaciones']],
                ['label' => 'Ofertas activas', 'value' => $indicadores['ofertas']],
                [
                    'label' => 'Cupos asignados',
                    'value' => $indicadores['cupos_asignados'],
                    'hint' => $indicadores['cupos_disponibles'].' disponibles de '.$indicadores['cupos_total'],
                ],
                ['label' => 'Lista de espera', 'value' => $indicadores['lista_espera']],
            ]" />
        </x-slot:kpis>

        @if($indicadores['por_estado']->isNotEmpty())
            <x-institucional.panel module="reportes" title="Postulaciones por estado">
                <div class="p-5">
                    <div class="flex flex-wrap gap-2">
                        @foreach($indicadores['por_estado'] as $est)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                {{ $est['nombre'] }}: {{ $est['total'] }}
                            </span>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs text-slate-500">
                        Con evaluación: <strong>{{ $indicadores['con_evaluacion'] }}</strong> ·
                        Documentos pendientes: <strong>{{ $indicadores['documentos_pendientes'] }}</strong>
                    </p>
                </div>
            </x-institucional.panel>
        @endif

        <x-institucional.panel module="reportes" title="Exportar datos">
            <p class="px-4 pt-4 text-sm text-slate-500">
                Cada tarjeta es un reporte distinto. Los archivos incluyen la información actual de su unidad educativa.
            </p>
            <div class="mx-auto grid w-full max-w-3xl gap-4 p-4">
                <div data-inst-export-card class="rounded-2xl border border-indigo-100 bg-indigo-50/40 p-6 shadow-sm">
                    <h3 class="font-semibold text-slate-900">Resumen de admisión por oferta</h3>
                    <p class="mt-1 text-xs text-slate-600">Postulaciones, cupos y lista de espera agrupados por oferta académica.</p>
                    <div class="mt-4">
                        <x-admin.export-report route="admin.institucional.reportes.export.resumen-admision" />
                    </div>
                </div>

                <div data-inst-export-card class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-semibold text-slate-900">Postulaciones</h3>
                    <p class="mt-1 text-xs text-slate-500">Listado de postulantes con estado, oferta y puntaje.</p>
                    <div class="mt-4"><x-admin.export-report route="admin.institucional.reportes.export.postulaciones" /></div>
                    <a href="{{ route('admin.institucional.postulaciones.index') }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:underline">Ir a Postulaciones →</a>
                </div>

                <div data-inst-export-card class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-semibold text-slate-900">Ofertas y cupos</h3>
                    <p class="mt-1 text-xs text-slate-500">Ofertas académicas con cupos totales y disponibles.</p>
                    <div class="mt-4"><x-admin.export-report route="admin.institucional.reportes.export.ofertas" /></div>
                    <a href="{{ route('admin.institucional.ofertas.index') }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:underline">Ir a Ofertas →</a>
                </div>

                <div data-inst-export-card class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-semibold text-slate-900">Resultados y ranking</h3>
                    <p class="mt-1 text-xs text-slate-500">Puntajes, orden por oferta y estado de asignación.</p>
                    <div class="mt-4"><x-admin.export-report route="admin.institucional.reportes.export.resultados" /></div>
                    <a href="{{ route('admin.institucional.resultados.index') }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:underline">Ir a Resultados →</a>
                </div>

                <div data-inst-export-card class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-semibold text-slate-900">Asignación de cupos</h3>
                    <p class="mt-1 text-xs text-slate-500">Postulantes con cupo asignado en su unidad.</p>
                    <div class="mt-4"><x-admin.export-report route="admin.institucional.reportes.export.asignaciones" /></div>
                    <a href="{{ route('admin.institucional.asignacion.index') }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:underline">Ir a Asignación →</a>
                </div>

                <div data-inst-export-card class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-semibold text-slate-900">Lista de espera</h3>
                    <p class="mt-1 text-xs text-slate-500">Orden de espera por oferta.</p>
                    <div class="mt-4"><x-admin.export-report route="admin.institucional.reportes.export.lista-espera" /></div>
                    <a href="{{ route('admin.institucional.lista-espera.index') }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:underline">Ir a Lista de espera →</a>
                </div>

                <div data-inst-export-card class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-semibold text-slate-900">Documentos</h3>
                    <p class="mt-1 text-xs text-slate-500">Estado de documentos de postulación.</p>
                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <x-admin.export-report route="admin.institucional.reportes.export.documentos" />
                        <span class="text-xs text-slate-500">Solo pendientes:</span>
                        <a href="{{ route('admin.institucional.reportes.export.documentos', ['estado' => 'pendiente', 'format' => 'xlsx']) }}"
                           class="text-xs font-semibold text-emerald-700 hover:underline">Excel</a>
                        <a href="{{ route('admin.institucional.reportes.export.documentos', ['estado' => 'pendiente', 'format' => 'pdf']) }}"
                           class="text-xs font-semibold text-rose-700 hover:underline">PDF</a>
                    </div>
                    <a href="{{ route('admin.institucional.documentos.index') }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:underline">Ir a Documentos →</a>
                </div>

                <div data-inst-export-card class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h3 class="font-semibold text-slate-900">Historial de actividad</h3>
                    <p class="mt-1 text-xs text-slate-500">Línea de tiempo de acciones en admisión.</p>
                    <div class="mt-4"><x-admin.export-report route="admin.institucional.reportes.export.historial" /></div>
                    <a href="{{ route('admin.institucional.historial.index') }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:underline">Ir a Historial →</a>
                </div>
            </div>
        </x-institucional.panel>
    </x-institucional.page>
@endsection
