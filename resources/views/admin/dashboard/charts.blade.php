@php
    $kpis = $adminDashboard['kpis'] ?? [];
    $charts = $adminDashboard['charts'] ?? [];
    $hasChartData = collect($charts)->contains(fn (array $chart): bool => array_sum($chart['data'] ?? []) > 0);
@endphp

@if(!empty($kpis['sin_gestion_activa']))
<div class="mb-6 rounded-2xl border border-amber-300 bg-amber-50/70 p-5 text-sm text-amber-900 shadow-[0_4px_15px_rgba(245,158,11,0.02)] flex gap-3 items-start animate-fadeInUp">
    <svg class="h-5 w-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <div>
        <p class="font-bold text-slate-800">No hay gestión académica activa</p>
        <p class="mt-1 text-slate-600 font-light font-sans">Activa un período en <a href="{{ route('admin.gestiones.index') }}" class="font-bold text-indigo-650 underline hover:text-indigo-855">Gestiones</a> para que las unidades puedan operar con el ciclo correcto.</p>
    </div>
</div>
@endif

<!-- KPIs Premium con relieve, degradado sutil y bordes definidos -->
<div class="mb-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6 animate-fadeInUp">
    @foreach([
        [
            'key' => 'usuarios', 
            'label' => 'Usuarios', 
            'sub' => ($kpis['usuarios_activos'] ?? 0).' activos', 
            'color' => 'indigo', 
            'icon' => '<svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-1a4 4 0 00-5-3.87M9 20H4v-1a4 4 0 015-3.87m0-6.13a4 4 0 110-8 4 4 0 010 8zm8 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'
        ],
        [
            'key' => 'estudiantes', 
            'label' => 'Estudiantes', 
            'sub' => 'Registrados', 
            'color' => 'violet', 
            'icon' => '<svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6"/></svg>'
        ],
        [
            'key' => 'tutores', 
            'label' => 'Tutores', 
            'sub' => 'Perfiles', 
            'color' => 'fuchsia', 
            'icon' => '<svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'
        ],
        [
            'key' => 'postulaciones', 
            'label' => 'Postulaciones', 
            'sub' => 'Totales', 
            'color' => 'cyan', 
            'icon' => '<svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>'
        ],
        [
            'key' => 'unidades', 
            'label' => 'Unidades', 
            'sub' => 'Educativas', 
            'color' => 'emerald', 
            'icon' => '<svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 21h16M6 21V7l6-4 6 4v14"/></svg>'
        ],
        [
            'key' => 'documentos', 
            'label' => 'Documentos', 
            'sub' => 'Cargados', 
            'color' => 'amber', 
            'icon' => '<svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 3v5h5"/></svg>'
        ]
    ] as $card)
    @endforeach
</div>
@php
    // Clear themeColors to avoid clashes
    $themeColors = [
        'indigo' => ['bg' => 'bg-indigo-50/70', 'text' => 'text-indigo-600', 'glow' => 'bg-indigo-500/5', 'shadow' => 'shadow-[0_12px_30px_rgba(99,102,241,0.06)] border-indigo-100/30'],
        'violet' => ['bg' => 'bg-violet-50/70', 'text' => 'text-violet-600', 'glow' => 'bg-violet-500/5', 'shadow' => 'shadow-[0_12px_30px_rgba(139,92,246,0.06)] border-violet-100/30'],
        'fuchsia' => ['bg' => 'bg-fuchsia-50/70', 'text' => 'text-fuchsia-600', 'glow' => 'bg-fuchsia-500/5', 'shadow' => 'shadow-[0_12px_30px_rgba(217,70,239,0.06)] border-fuchsia-100/30'],
        'cyan' => ['bg' => 'bg-cyan-50/70', 'text' => 'text-cyan-600', 'glow' => 'bg-cyan-500/5', 'shadow' => 'shadow-[0_12px_30px_rgba(6,182,212,0.06)] border-cyan-100/30'],
        'emerald' => ['bg' => 'bg-emerald-50/70', 'text' => 'text-emerald-600', 'glow' => 'bg-emerald-500/5', 'shadow' => 'shadow-[0_12px_30px_rgba(16,185,129,0.06)] border-emerald-100/30'],
        'amber' => ['bg' => 'bg-amber-50/70', 'text' => 'text-amber-600', 'glow' => 'bg-amber-500/5', 'shadow' => 'shadow-[0_12px_30px_rgba(245,158,11,0.06)] border-amber-100/30'],
    ];
@endphp
<div class="mb-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6 animate-fadeInUp">
    @foreach([
        [
            'key' => 'usuarios', 
            'label' => 'Usuarios', 
            'sub' => ($kpis['usuarios_activos'] ?? 0).' activos', 
            'color' => 'indigo', 
            'icon' => '<svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-1a4 4 0 00-5-3.87M9 20H4v-1a4 4 0 015-3.87m0-6.13a4 4 0 110-8 4 4 0 010 8zm8 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'
        ],
        [
            'key' => 'estudiantes', 
            'label' => 'Estudiantes', 
            'sub' => 'Registrados', 
            'color' => 'violet', 
            'icon' => '<svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6"/></svg>'
        ],
        [
            'key' => 'tutores', 
            'label' => 'Tutores', 
            'sub' => 'Perfiles', 
            'color' => 'fuchsia', 
            'icon' => '<svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'
        ],
        [
            'key' => 'postulaciones', 
            'label' => 'Postulaciones', 
            'sub' => 'Totales', 
            'color' => 'cyan', 
            'icon' => '<svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>'
        ],
        [
            'key' => 'unidades', 
            'label' => 'Unidades', 
            'sub' => 'Educativas', 
            'color' => 'emerald', 
            'icon' => '<svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 21h16M6 21V7l6-4 6 4v14"/></svg>'
        ],
        [
            'key' => 'documentos', 
            'label' => 'Documentos', 
            'sub' => 'Cargados', 
            'color' => 'amber', 
            'icon' => '<svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 3v5h5"/></svg>'
        ]
    ] as $card)
        @php
            $tc = $themeColors[$card['color']];
        @endphp
        <article class="group relative overflow-hidden rounded-2xl bg-gradient-to-b from-white to-[#FAFAFD] border {{ $tc['shadow'] }} p-4.5 transition-all duration-350 hover:-translate-y-1 hover:shadow-xl hover:border-transparent">
            <div class="absolute -right-4 -bottom-4 h-16 w-16 rounded-full {{ $tc['glow'] }} opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $card['label'] }}</p>
                <span class="rounded-xl {{ $tc['bg'] }} p-2.5 {{ $tc['text'] }} border border-slate-200/30 shadow-sm">
                    {!! $card['icon'] !!}
                </span>
            </div>
            <p class="mt-3 text-2xl font-black text-slate-800 leading-none">{{ $kpis[$card['key']] ?? 0 }}</p>
            <p class="mt-2 text-[10px] text-slate-500 font-light truncate">{{ $card['sub'] }}</p>
        </article>
    @endforeach
</div>

<!-- Barra de Período Académico Vigente con Relieve -->
<article class="mb-8 rounded-2xl border border-indigo-100/40 bg-gradient-to-r from-indigo-50/50 via-violet-50/15 to-transparent px-5 py-4 shadow-[0_12px_35px_rgba(99,102,241,0.05)]">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3.5">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-md shadow-indigo-200/35 border border-indigo-500/20">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-indigo-500">Gestión académica vigente</p>
                <p class="text-lg font-black text-slate-800 leading-none mt-1">{{ $kpis['gestion_activa'] ?? 'Ninguna registrada' }}</p>
            </div>
        </div>
        <p class="text-sm text-slate-650 font-light">
            <strong class="font-bold text-slate-800">{{ $kpis['gestiones'] ?? 0 }}</strong> períodos académicos · <strong class="font-bold text-slate-800">{{ $kpis['ofertas'] ?? 0 }}</strong> ofertas publicadas
        </p>
    </div>
</article>

@if(!$hasChartData)
<div class="mb-8 rounded-3xl border border-dashed border-slate-250 bg-slate-50/70 px-6 py-14 text-center">
    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400 border border-slate-200/30 shadow-sm">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 16v-3m4 3V8m4 8v-5"/></svg>
    </div>
    <p class="mt-4 font-bold text-slate-750">Aún no hay datos para mostrar gráficas</p>
    <p class="mt-1.5 text-sm text-slate-500 font-light max-w-sm mx-auto">En cuanto se registren usuarios, postulaciones o documentos en el sistema, aquí se desplegará el panel analítico.</p>
</div>
@else
<div class="mb-6 animate-fadeInUp">
    <h2 class="text-lg font-bold text-slate-800">Análisis del sistema</h2>
    <p class="mt-0.5 text-xs text-slate-400 font-light">Métricas agregadas a nivel nacional en tiempo real.</p>
</div>

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3 animate-fadeInUp">
    @foreach([
        ['id' => 'chartUsuariosRol', 'key' => 'usuarios_por_rol', 'title' => 'Cuentas por tipo', 'subtitle' => 'Distribución de accesos autorizados', 'type' => 'doughnut'],
        ['id' => 'chartUsuariosEstado', 'key' => 'usuarios_estado', 'title' => 'Estado de cuentas', 'subtitle' => 'Usuarios activos vs inactivos', 'type' => 'doughnut'],
        ['id' => 'chartPostulacionesEstado', 'key' => 'postulaciones_estado', 'title' => 'Postulaciones por estado', 'subtitle' => 'Flujo consolidado nacional', 'type' => 'bar'],
        ['id' => 'chartPostulacionesUnidad', 'key' => 'postulaciones_unidad', 'title' => 'Postulaciones por unidad', 'subtitle' => 'Top colegios con mayor demanda', 'type' => 'bar', 'indexAxis' => 'y'],
        ['id' => 'chartPostulacionesMes', 'key' => 'postulaciones_mes', 'title' => 'Postulaciones por mes', 'subtitle' => 'Evolución histórica (últimos 6 meses)', 'type' => 'line'],
        ['id' => 'chartEstudiantesTutor', 'key' => 'estudiantes_tutor', 'title' => 'Vínculos familiares', 'subtitle' => 'Tutelados asociados a cuentas', 'type' => 'pie'],
        ['id' => 'chartEstudiantesCalidad', 'key' => 'estudiantes_calidad', 'title' => 'Integridad de padrón', 'subtitle' => 'Incidencias u omisiones en el RUDE/CI', 'type' => 'bar'],
        ['id' => 'chartDocumentosEstado', 'key' => 'documentos_estado', 'title' => 'Control de documentos', 'subtitle' => 'Revisión y validaciones físicas', 'type' => 'doughnut'],
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Paleta moderna HSL para un acabado gráfico de nivel experto
            const palette = [
                '#6366f1', '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b',
                '#ec4899', '#3b82f6', '#ef4444', '#14b8a6', '#a855f7'
            ];

            // Configurar fuente unificada 'Plus Jakarta Sans' para todo el motor de Chart.js
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
                    backgroundColor: '#0f172a', // slate-900 (ultra oscuro premium)
                    padding: 12,
                    cornerRadius: 12,
                    titleFont: { size: 11, weight: '700' },
                    bodyFont: { size: 10 },
                    borderColor: 'rgba(255,255,255,0.08)',
                    borderWidth: 1,
                    shadowColor: 'rgba(0,0,0,0.1)'
                }
            };

            document.querySelectorAll('canvas[data-chart-type]').forEach((canvas) => {
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
                        <span>Sin registros en el período</span>
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
