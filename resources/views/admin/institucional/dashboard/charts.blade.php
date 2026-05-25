@php
    $kpis = $institucionalDashboard['kpis'] ?? [];
    $charts = $institucionalDashboard['charts'] ?? [];
    $hasChartData = collect($charts)->contains(fn (array $chart): bool => array_sum($chart['data'] ?? []) > 0);
@endphp

<section class="mb-8">
    <div class="mb-5 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between animate-fadeInUp">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Estadísticas de admisión</h2>
            <p class="text-xs text-slate-400 font-light">Indicadores clave y análisis de tu unidad educativa en tiempo real.</p>
        </div>
        <a href="{{ route('admin.institucional.reportes.index') }}"
           class="inline-flex items-center gap-1 text-xs font-bold text-indigo-650 hover:underline">
            Ver reportes completos →
        </a>
    </div>

    <!-- KPIs Rápidos en tarjetas premium con relieve, degradado sutil y bordes definidos -->
    <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-7 animate-fadeInUp">
        @foreach([
            [
                'key' => 'postulaciones', 
                'label' => 'Postulaciones', 
                'sub' => 'Totales', 
                'color' => 'indigo',
                'icon' => '<svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>'
            ],
            [
                'key' => 'ofertas', 
                'label' => 'Ofertas', 
                'sub' => 'Activas', 
                'color' => 'cyan',
                'icon' => '<svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5"/></svg>'
            ],
            [
                'key' => 'cupos_asignados', 
                'label' => 'Asignados', 
                'sub' => 'Cupos ocupados', 
                'color' => 'emerald',
                'icon' => '<svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
            ],
            [
                'key' => 'cupos_disponibles', 
                'label' => 'Disponibles', 
                'sub' => 'de '.($kpis['cupos_total'] ?? 0), 
                'color' => 'violet',
                'icon' => '<svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m0 0a8.947 8.947 0 01-3.743-.479 3 3 0 014.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0012 21c2.17 0 4.207-.576 5.963-1.584A6.062 6.062 0 0018 18.722zm-5.263-2.852a4 4 0 11-1.474-7.446 4 4 0 011.474 7.446zm0 0a3.991 3.991 0 002.526-2.526 4 4 0 00-7.446-1.474 4 4 0 004.92 4.92z"/></svg>'
            ],
            [
                'key' => 'lista_espera', 
                'label' => 'Lista espera', 
                'sub' => 'En cola', 
                'color' => 'amber',
                'icon' => '<svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
            ],
            [
                'key' => 'con_evaluacion', 
                'label' => 'Evaluadas', 
                'sub' => 'Con puntaje', 
                'color' => 'fuchsia',
                'icon' => '<svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>'
            ],
            [
                'key' => 'documentos_pendientes', 
                'label' => 'Docs. pend.', 
                'sub' => 'Por revisar', 
                'color' => 'rose',
                'icon' => '<svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'
            ],
        ] as $card)
            @php
                $themeColors = [
                    'indigo' => ['bg' => 'bg-indigo-50/70', 'text' => 'text-indigo-600', 'border' => 'hover:shadow-indigo-200/50', 'glow' => 'bg-indigo-500/5', 'shadow' => 'shadow-[0_12px_30px_rgba(99,102,241,0.06)] border-indigo-100/30'],
                    'cyan' => ['bg' => 'bg-cyan-50/70', 'text' => 'text-cyan-600', 'border' => 'hover:shadow-cyan-200/50', 'glow' => 'bg-cyan-500/5', 'shadow' => 'shadow-[0_12px_30px_rgba(6,182,212,0.06)] border-cyan-100/30'],
                    'emerald' => ['bg' => 'bg-emerald-50/70', 'text' => 'text-emerald-600', 'border' => 'hover:shadow-emerald-200/50', 'glow' => 'bg-emerald-500/5', 'shadow' => 'shadow-[0_12px_30px_rgba(16,185,129,0.06)] border-emerald-100/30'],
                    'violet' => ['bg' => 'bg-violet-50/70', 'text' => 'text-violet-600', 'border' => 'hover:shadow-violet-200/50', 'glow' => 'bg-violet-500/5', 'shadow' => 'shadow-[0_12px_30px_rgba(139,92,246,0.06)] border-violet-100/30'],
                    'amber' => ['bg' => 'bg-amber-50/70', 'text' => 'text-amber-600', 'border' => 'hover:shadow-amber-200/50', 'glow' => 'bg-amber-500/5', 'shadow' => 'shadow-[0_12px_30px_rgba(245,158,11,0.06)] border-amber-100/30'],
                    'fuchsia' => ['bg' => 'bg-fuchsia-50/70', 'text' => 'text-fuchsia-600', 'border' => 'hover:shadow-fuchsia-200/50', 'glow' => 'bg-fuchsia-500/5', 'shadow' => 'shadow-[0_12px_30px_rgba(217,70,239,0.06)] border-fuchsia-100/30'],
                    'rose' => ['bg' => 'bg-rose-50/70', 'text' => 'text-rose-600', 'border' => 'hover:shadow-rose-200/50', 'glow' => 'bg-rose-500/5', 'shadow' => 'shadow-[0_12px_30px_rgba(244,63,94,0.06)] border-rose-100/30'],
                ];
                $tc = $themeColors[$card['color']];
            @endphp
            <article class="group relative overflow-hidden rounded-2xl bg-gradient-to-b from-white to-[#FAFAFD] border {{ $tc['shadow'] }} p-4 transition-all duration-350 hover:-translate-y-1 hover:shadow-xl hover:border-transparent">
                <div class="absolute -right-4 -bottom-4 h-14 w-14 rounded-full {{ $tc['glow'] }} opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                <div class="flex items-center justify-between">
                    <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">{{ $card['label'] }}</p>
                    <span class="rounded-lg {{ $tc['bg'] }} p-2 {{ $tc['text'] }} border border-slate-200/30 shadow-sm">
                        {!! $card['icon'] !!}
                    </span>
                </div>
                <p class="mt-2 text-xl font-black text-slate-800 leading-none">{{ $kpis[$card['key']] ?? 0 }}</p>
                <p class="mt-2.5 text-[9px] text-slate-500 font-light truncate">{{ $card['sub'] }}</p>
            </article>
        @endforeach
    </div>

    <!-- Panel de Gráficos con relieve -->
    @if(!$hasChartData)
        <div class="rounded-3xl border border-dashed border-slate-250 bg-slate-50/70 px-6 py-14 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400 border border-slate-200/30 shadow-sm">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 16v-3m4 3V8m4 8v-5"/></svg>
            </div>
            <p class="mt-4 font-bold text-slate-750">Aún no hay datos para mostrar gráficas</p>
            <p class="mt-1.5 text-sm text-slate-500 font-light max-w-sm mx-auto">En cuanto se registren solicitudes de admisión en este colegio, aquí verás el desglose analítico visual.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3 animate-fadeInUp">
            @foreach([
                ['id' => 'instChartEstado', 'key' => 'postulaciones_estado', 'title' => 'Postulaciones por estado', 'subtitle' => 'Flujo de admisión en tu unidad', 'type' => 'doughnut'],
                ['id' => 'instChartOferta', 'key' => 'postulaciones_oferta', 'title' => 'Postulaciones por oferta', 'subtitle' => 'Grado académico con mayor demanda (top 6)', 'type' => 'bar', 'indexAxis' => 'y'],
                ['id' => 'instChartMes', 'key' => 'postulaciones_mes', 'title' => 'Ingresos por mes', 'subtitle' => 'Evolución de postulantes (últimos 6 meses)', 'type' => 'line'],
                ['id' => 'instChartCupos', 'key' => 'cupos_resumen', 'title' => 'Capacidad y asignación', 'subtitle' => 'Cupos asignados, libres y en lista de espera', 'type' => 'pie'],
                ['id' => 'instChartDocs', 'key' => 'documentos_estado', 'title' => 'Control documental', 'subtitle' => 'Estado de aprobación de archivos', 'type' => 'doughnut'],
                ['id' => 'instChartEval', 'key' => 'evaluacion_avance', 'title' => 'Avance de evaluación', 'subtitle' => 'Solicitudes con puntaje registrado', 'type' => 'doughnut'],
            ] as $chartMeta)
                @php
                    $chartPayload = $charts[$chartMeta['key']] ?? ['labels' => [], 'data' => []];
                @endphp
                <article class="rounded-2xl bg-gradient-to-b from-white to-[#F9FAFD] p-5 border border-slate-100/50 shadow-[0_15px_35px_rgba(148,163,184,0.06),0_1px_2px_rgba(0,0,0,0.005)] transition-all duration-300 hover:shadow-lg">
                    <h3 class="text-sm font-bold text-slate-800 leading-tight">{{ $chartMeta['title'] }}</h3>
                    <p class="mt-1 text-[11px] text-slate-400 font-light line-clamp-1">{{ $chartMeta['subtitle'] }}</p>
                    <div class="relative mt-5 h-44">
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
            // Paleta HSL coordinada
            const palette = [
                '#6366f1', '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b',
                '#ec4899', '#3b82f6', '#ef4444', '#14b8a6', '#a855f7'
            ];

            // Ajuste de fuentes por defecto en ChartJS a Plus Jakarta Sans
            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
            Chart.defaults.font.size = 10;
            Chart.defaults.color = '#94a3b8'; // slate-400

            const commonPlugins = {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 8,
                        padding: 12,
                        usePointStyle: true,
                        font: { size: 9, weight: '500' }
                    }
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    padding: 12,
                    cornerRadius: 12,
                    titleFont: { size: 11, weight: '700' },
                    bodyFont: { size: 10 },
                    borderColor: 'rgba(255,255,255,0.08)',
                    borderWidth: 1,
                    shadowColor: 'rgba(0,0,0,0.1)'
                }
            };

            document.querySelectorAll('#instChartEstado, #instChartOferta, #instChartMes, #instChartCupos, #instChartDocs, #instChartEval').forEach((canvas) => {
                const labels = JSON.parse(canvas.dataset.labels || '[]');
                const values = JSON.parse(canvas.dataset.values || '[]');
                const type = canvas.dataset.chartType;
                const indexAxis = canvas.dataset.indexAxis || 'x';

                if (!labels.length || values.every((v) => Number(v) === 0)) {
                    const parent = canvas.parentElement;
                    canvas.remove();
                    const empty = document.createElement('div');
                    empty.className = 'flex h-full flex-col items-center justify-center text-xs text-slate-350 font-light';
                    empty.innerHTML = `
                        <svg class="h-5 w-5 mb-1.5 text-slate-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        <span>Sin registros en la gestión</span>
                    `;
                    parent.appendChild(empty);
                    return;
                }

                const colors = labels.map((_, i) => palette[i % palette.length]);

                let options = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        ...commonPlugins,
                        legend: {
                            ...commonPlugins.legend,
                            display: type === 'doughnut' || type === 'pie'
                        }
                    },
                    scales: {}
                };

                if (type === 'bar' || type === 'line') {
                    options.scales = {
                        x: {
                            grid: { display: false },
                            ticks: { maxRotation: 40, font: { size: 9 } }
                        },
                        y: {
                            grid: { color: '#f1f5f9' },
                            beginAtZero: true,
                            ticks: { precision: 0, font: { size: 9 } }
                        }
                    };

                    if (type === 'bar' && indexAxis === 'y') {
                        options.scales = {
                            x: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 9 } } },
                            y: { grid: { display: false }, ticks: { font: { size: 9 } } }
                        };
                    }
                }

                if (type === 'bar') {
                    options.indexAxis = indexAxis;
                }

                const dataset = {
                    label: 'Total',
                    data: values,
                    backgroundColor: type === 'line'
                        ? 'rgba(99, 102, 241, 0.06)'
                        : colors.map((c) => c + (type === 'bar' ? 'dd' : 'ee')),
                    borderColor: type === 'line' ? '#6366f1' : colors.map((c) => c + 'ff'),
                    borderWidth: type === 'line' ? 2.5 : 1,
                    fill: type === 'line',
                    tension: 0.4,
                    borderRadius: type === 'bar' ? 6 : 0,
                    maxBarThickness: 28,
                    pointRadius: type === 'line' ? 3 : 0,
                    pointHoverRadius: type === 'line' ? 5 : 0
                };

                new Chart(canvas, {
                    type,
                    data: { labels, datasets: [dataset] },
                    options
                });
            });
        });
    </script>
@endpush
