@extends('layouts.dashboard')

@section('title', 'Reportes | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Reportes</span>
@endsection

@section('content')
    <div class="mb-8">
        <p class="text-xs text-slate-400">Panel / Reportes</p>
        <h1 class="text-2xl font-bold text-slate-900">Descargar reportes</h1>
        <p class="mt-1 text-sm text-slate-500">
            Visualiza indicadores nacionales en las gráficas y exporta los datos en <strong>Excel</strong> o <strong>PDF</strong>.
        </p>
    </div>

    @if(!empty($adminDashboard))
        @include('admin.dashboard.charts', ['adminDashboard' => $adminDashboard])
    @endif

    <section class="mt-10 border-t border-slate-200 pt-10">
        <h2 class="text-lg font-semibold text-slate-900">Exportar datos</h2>
        <p class="mt-1 text-sm text-slate-500">
            Cada fila es un reporte distinto. Si ves dos filas (por ejemplo listado y resumen de colegios), cada una tiene su Excel y PDF.
        </p>

        <div class="mt-6 mx-auto grid w-full max-w-3xl gap-4">
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-semibold text-slate-900">Postulaciones (nacional)</h3>
                <div class="mt-4"><x-admin.export-report route="admin.reportes.export.postulaciones" /></div>
                <a href="{{ route('admin.postulaciones.index') }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:underline">Ir a Postulaciones →</a>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-semibold text-slate-900">Postulantes</h3>
                <div class="mt-4"><x-admin.export-report route="admin.reportes.export.postulantes" /></div>
                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                    @foreach(['sin_tutor' => 'Sin tutor', 'sin_rude' => 'Sin RUDE', 'sin_matricula' => 'Sin matrícula', 'rude_duplicado' => 'RUDE duplicado'] as $key => $label)
                        <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-slate-50 px-2 py-1">
                            <span class="font-medium text-slate-600">{{ $label }}:</span>
                            <a href="{{ route('admin.reportes.export.postulantes', ['incidencia' => $key, 'format' => 'xlsx']) }}" class="text-emerald-700 hover:underline">Excel</a>
                            <span class="text-slate-300">|</span>
                            <a href="{{ route('admin.reportes.export.postulantes', ['incidencia' => $key, 'format' => 'pdf']) }}" class="text-rose-700 hover:underline">PDF</a>
                        </span>
                    @endforeach
                </div>
                <a href="{{ route('admin.estudiantes.index') }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:underline">Ir a Postulantes →</a>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-semibold text-slate-900">Unidades educativas</h3>
                <p class="mt-1 text-xs text-slate-500">Listado detallado y resumen con métricas.</p>
                <div class="mt-4">
                    <x-admin.export-actions
                        :items="[
                            ['route' => 'admin.reportes.export.unidades', 'label' => 'Listado de colegios', 'hint' => 'Detalle por unidad educativa.'],
                            ['route' => 'admin.reportes.export.resumen-unidades', 'label' => 'Resumen estadístico', 'hint' => 'Métricas agregadas por colegio.'],
                        ]"
                    />
                </div>
                <a href="{{ route('admin.unidades.index') }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:underline">Ir a Unidades →</a>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-semibold text-slate-900">Tutores y cuentas de acceso</h3>
                <div class="mt-4">
                    <x-admin.export-actions
                        :items="[
                            ['route' => 'admin.reportes.export.tutores', 'label' => 'Tutores y apoderados', 'hint' => 'Perfiles tutor vinculados a estudiantes.'],
                            ['route' => 'admin.reportes.export.usuarios', 'label' => 'Usuarios del sistema', 'hint' => 'Cuentas con rol Ministerio, UE o tutor.'],
                        ]"
                    />
                </div>
            </div>
        </div>
    </section>
@endsection
