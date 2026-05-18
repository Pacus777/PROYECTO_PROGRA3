@php
    $kpis = $adminDashboard['kpis'] ?? [];
    $charts = $adminDashboard['charts'] ?? [];
    $hasChartData = collect($charts)->contains(fn (array $chart): bool => array_sum($chart['data'] ?? []) > 0);
@endphp

@if(!empty($kpis['sin_gestion_activa']))
<div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">
    <p class="font-semibold">No hay gestión académica activa</p>
    <p class="mt-1 text-amber-800">Activa un período en <a href="{{ route('admin.gestiones.index') }}" class="font-semibold underline hover:text-amber-950">Gestiones</a> para que las unidades puedan operar con el ciclo correcto.</p>
</div>
@endif

<div class="mb-6 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-6">
    @foreach([
        ['key' => 'usuarios', 'label' => 'Usuarios', 'sub' => ($kpis['usuarios_activos'] ?? 0).' activos', 'from' => 'from-indigo-500', 'to' => 'to-indigo-700'],
        ['key' => 'estudiantes', 'label' => 'Estudiantes', 'sub' => 'Registrados', 'from' => 'from-violet-500', 'to' => 'to-purple-700'],
        ['key' => 'tutores', 'label' => 'Tutores', 'sub' => 'Perfiles', 'from' => 'from-fuchsia-500', 'to' => 'to-pink-600'],
        ['key' => 'postulaciones', 'label' => 'Postulaciones', 'sub' => 'Totales', 'from' => 'from-cyan-500', 'to' => 'to-blue-600'],
        ['key' => 'unidades', 'label' => 'Unidades', 'sub' => 'Educativas', 'from' => 'from-emerald-500', 'to' => 'to-teal-600'],
        ['key' => 'documentos', 'label' => 'Documentos', 'sub' => 'Cargados', 'from' => 'from-amber-500', 'to' => 'to-orange-600'],
    ] as $card)
        <article class="rounded-xl bg-gradient-to-br {{ $card['from'] }} {{ $card['to'] }} p-3 text-white shadow-md">
            <p class="text-[10px] font-semibold uppercase tracking-wide text-white/80">{{ $card['label'] }}</p>
            <p class="mt-1 text-xl font-black sm:text-2xl">{{ $kpis[$card['key']] ?? 0 }}</p>
            <p class="mt-0.5 text-[10px] text-white/75">{{ $card['sub'] }}</p>
        </article>
    @endforeach
</div>

<article class="mb-6 rounded-xl border border-indigo-100 bg-indigo-50/60 px-4 py-3">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Gestión vigente</p>
            <p class="text-lg font-bold text-indigo-950">{{ $kpis['gestion_activa'] ?? 'Ninguna' }}</p>
        </div>
        <p class="text-sm text-indigo-800">{{ $kpis['gestiones'] ?? 0 }} períodos registrados · {{ $kpis['ofertas'] ?? 0 }} ofertas académicas</p>
    </div>
</article>

@if(!$hasChartData)
<div class="mb-8 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-12 text-center">
    <p class="font-semibold text-slate-700">Aún no hay datos para las gráficas</p>
    <p class="mt-2 text-sm text-slate-500">Cuando existan usuarios, postulaciones o documentos, aquí verás la visualización del sistema.</p>
</div>
@else
<div class="mb-6">
    <h2 class="text-base font-semibold text-slate-900">Análisis del sistema</h2>
    <p class="mt-0.5 text-xs text-slate-500">Métricas globales en tiempo real según la base de datos.</p>
</div>

<div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
    @foreach([
        ['id' => 'chartUsuariosRol', 'key' => 'usuarios_por_rol', 'title' => 'Cuentas por tipo', 'subtitle' => 'Ministerio, unidad educativa o tutor', 'type' => 'doughnut'],
        ['id' => 'chartUsuariosEstado', 'key' => 'usuarios_estado', 'title' => 'Estado de cuentas', 'subtitle' => 'Usuarios activos e inactivos', 'type' => 'doughnut'],
        ['id' => 'chartPostulacionesEstado', 'key' => 'postulaciones_estado', 'title' => 'Postulaciones por estado', 'subtitle' => 'Flujo del proceso de admisión', 'type' => 'bar'],
        ['id' => 'chartPostulacionesUnidad', 'key' => 'postulaciones_unidad', 'title' => 'Postulaciones por unidad', 'subtitle' => 'Top unidades educativas (máx. 8)', 'type' => 'bar', 'indexAxis' => 'y'],
        ['id' => 'chartPostulacionesMes', 'key' => 'postulaciones_mes', 'title' => 'Postulaciones por mes', 'subtitle' => 'Últimos 6 meses', 'type' => 'line'],
        ['id' => 'chartEstudiantesTutor', 'key' => 'estudiantes_tutor', 'title' => 'Postulantes registrados', 'subtitle' => 'Con o sin tutor vinculado (sin cuenta de acceso)', 'type' => 'pie'],
        ['id' => 'chartEstudiantesCalidad', 'key' => 'estudiantes_calidad', 'title' => 'Calidad de datos (postulantes)', 'subtitle' => 'Incidencias detectadas en el padrón', 'type' => 'bar'],
        ['id' => 'chartDocumentosEstado', 'key' => 'documentos_estado', 'title' => 'Documentos por estado', 'subtitle' => 'Revisión documental', 'type' => 'doughnut'],
    ] as $chartMeta)
        @php
            $chartPayload = $charts[$chartMeta['key']] ?? ['labels' => [], 'data' => []];
        @endphp
        <article class="rounded-xl bg-white p-3 shadow-sm ring-1 ring-slate-100">
            <h3 class="text-sm font-semibold text-slate-900 leading-tight">{{ $chartMeta['title'] }}</h3>
            <p class="mt-0.5 text-[11px] text-slate-500 line-clamp-2">{{ $chartMeta['subtitle'] }}</p>
            <div class="relative mt-2 h-36 sm:h-40">
                <canvas id="{{ $chartMeta['id'] }}"
                        data-chart-type="{{ $chartMeta['type'] }}"
                        @if(!empty($chartMeta['indexAxis'])) data-index-axis="{{ $chartMeta['indexAxis'] }}" @endif
                        data-labels='@json($chartPayload['labels'])'
                        data-values='@json($chartPayload['data'])'></canvas>
            </div>
        </article>
    @endforeach
</div>
@endif

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const palette = [
                '#6366f1', '#8b5cf6', '#a855f7', '#ec4899', '#06b6d4',
                '#10b981', '#f59e0b', '#ef4444', '#3b82f6', '#14b8a6',
            ];

            const doughnutOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8, font: { size: 10 } } },
                },
            };

            document.querySelectorAll('canvas[data-chart-type]').forEach((canvas) => {
                const labels = JSON.parse(canvas.dataset.labels || '[]');
                const values = JSON.parse(canvas.dataset.values || '[]');
                const type = canvas.dataset.chartType;
                const indexAxis = canvas.dataset.indexAxis || 'x';

                if (!labels.length || values.every((v) => Number(v) === 0)) {
                    const parent = canvas.parentElement;
                    canvas.remove();
                    const empty = document.createElement('p');
                    empty.className = 'flex h-full items-center justify-center text-sm text-slate-400';
                    empty.textContent = 'Sin datos para mostrar';
                    parent.appendChild(empty);
                    return;
                }

                const colors = labels.map((_, i) => palette[i % palette.length]);

                let options = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: type === 'line' || type === 'bar',
                            position: 'bottom',
                            labels: { boxWidth: 10, padding: 6, font: { size: 10 } },
                        },
                    },
                    scales: {},
                };

                if (type === 'bar' || type === 'line') {
                    options.scales = {
                        x: { grid: { display: type === 'line' }, ticks: { maxRotation: 45, minRotation: 0, font: { size: 10 } } },
                        y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } } },
                    };
                }

                if (type === 'bar') {
                    options.indexAxis = indexAxis;
                }

                if (type === 'doughnut' || type === 'pie') {
                    options = doughnutOptions;
                }

                const dataset = {
                    label: 'Total',
                    data: values,
                    backgroundColor: type === 'line'
                        ? 'rgba(99, 102, 241, 0.15)'
                        : colors.map((c) => c + (type === 'bar' ? 'cc' : 'dd')),
                    borderColor: type === 'line' ? '#6366f1' : colors,
                    borderWidth: type === 'line' ? 2 : 1,
                    fill: type === 'line',
                    tension: 0.35,
                    borderRadius: type === 'bar' ? 6 : 0,
                };

                new Chart(canvas, {
                    type,
                    data: { labels, datasets: [dataset] },
                    options,
                });
            });
        });
    </script>
@endpush
