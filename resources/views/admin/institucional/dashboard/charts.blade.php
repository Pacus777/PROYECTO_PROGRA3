@php
    $kpis = $institucionalDashboard['kpis'] ?? [];
    $charts = $institucionalDashboard['charts'] ?? [];
    $hasChartData = collect($charts)->contains(fn (array $chart): bool => array_sum($chart['data'] ?? []) > 0);
@endphp

<section class="mb-8">
    <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Estadísticas de admisión</h2>
            <p class="text-sm text-slate-500">Indicadores y gráficos de su unidad educativa en tiempo real.</p>
        </div>
        <a href="{{ route('admin.institucional.reportes.index') }}"
           class="inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 hover:underline">
            Ver reportes completos →
        </a>
    </div>

    <div class="mb-6 grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-7">
        @foreach([
            ['key' => 'postulaciones', 'label' => 'Postulaciones', 'sub' => 'Totales', 'from' => 'from-indigo-500', 'to' => 'to-indigo-700'],
            ['key' => 'ofertas', 'label' => 'Ofertas', 'sub' => 'Activas', 'from' => 'from-cyan-500', 'to' => 'to-blue-600'],
            ['key' => 'cupos_asignados', 'label' => 'Asignados', 'sub' => 'Cupos ocupados', 'from' => 'from-emerald-500', 'to' => 'to-teal-600'],
            ['key' => 'cupos_disponibles', 'label' => 'Disponibles', 'sub' => 'de '.$kpis['cupos_total'], 'from' => 'from-violet-500', 'to' => 'to-purple-700'],
            ['key' => 'lista_espera', 'label' => 'Lista espera', 'sub' => 'En cola', 'from' => 'from-amber-500', 'to' => 'to-orange-600'],
            ['key' => 'con_evaluacion', 'label' => 'Evaluadas', 'sub' => 'Con puntaje', 'from' => 'from-fuchsia-500', 'to' => 'to-pink-600'],
            ['key' => 'documentos_pendientes', 'label' => 'Docs. pend.', 'sub' => 'Por revisar', 'from' => 'from-rose-500', 'to' => 'to-red-600'],
        ] as $card)
            <article class="rounded-xl bg-gradient-to-br {{ $card['from'] }} {{ $card['to'] }} p-3 text-white shadow-md">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-white/80">{{ $card['label'] }}</p>
                <p class="mt-1 text-xl font-black sm:text-2xl">{{ $kpis[$card['key']] ?? 0 }}</p>
                <p class="mt-0.5 text-[10px] text-white/75">{{ $card['sub'] }}</p>
            </article>
        @endforeach
    </div>

    @if(!$hasChartData)
        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-12 text-center">
            <p class="font-semibold text-slate-700">Aún no hay datos para las gráficas</p>
            <p class="mt-2 text-sm text-slate-500">Cuando existan postulaciones, documentos o cupos asignados, aquí verá el análisis visual.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @foreach([
                ['id' => 'instChartEstado', 'key' => 'postulaciones_estado', 'title' => 'Postulaciones por estado', 'subtitle' => 'Flujo del proceso en su unidad', 'type' => 'doughnut'],
                ['id' => 'instChartOferta', 'key' => 'postulaciones_oferta', 'title' => 'Postulaciones por oferta', 'subtitle' => 'Top 6 ofertas académicas', 'type' => 'bar', 'indexAxis' => 'y'],
                ['id' => 'instChartMes', 'key' => 'postulaciones_mes', 'title' => 'Ingresos por mes', 'subtitle' => 'Últimos 6 meses', 'type' => 'line'],
                ['id' => 'instChartCupos', 'key' => 'cupos_resumen', 'title' => 'Cupos y lista de espera', 'subtitle' => 'Asignados, libres y en cola', 'type' => 'pie'],
                ['id' => 'instChartDocs', 'key' => 'documentos_estado', 'title' => 'Documentos por estado', 'subtitle' => 'Revisión documental', 'type' => 'doughnut'],
                ['id' => 'instChartEval', 'key' => 'evaluacion_avance', 'title' => 'Avance de evaluación', 'subtitle' => 'Postulaciones con puntaje registrado', 'type' => 'doughnut'],
            ] as $chartMeta)
                @php
                    $chartPayload = $charts[$chartMeta['key']] ?? ['labels' => [], 'data' => []];
                @endphp
                <article class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
                    <h3 class="text-sm font-semibold text-slate-900 leading-tight">{{ $chartMeta['title'] }}</h3>
                    <p class="mt-0.5 text-[11px] text-slate-500 line-clamp-2">{{ $chartMeta['subtitle'] }}</p>
                    <div class="relative mt-3 h-40 sm:h-44">
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
</section>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const palette = [
                '#6366f1', '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b',
                '#ef4444', '#ec4899', '#3b82f6', '#14b8a6', '#a855f7',
            ];

            const doughnutOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8, font: { size: 10 } } },
                },
            };

            document.querySelectorAll('#instChartEstado, #instChartOferta, #instChartMes, #instChartCupos, #instChartDocs, #instChartEval').forEach((canvas) => {
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
