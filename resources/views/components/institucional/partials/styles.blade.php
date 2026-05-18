<style>
    [x-cloak] { display: none !important; }

    .inst-pattern-dots {
        background-image: radial-gradient(circle at 1px 1px, rgb(99 102 241 / 0.08) 1px, transparent 0);
        background-size: 24px 24px;
    }
    .inst-pattern-grid {
        background-image: linear-gradient(rgb(139 92 246 / 0.06) 1px, transparent 1px),
            linear-gradient(90deg, rgb(139 92 246 / 0.06) 1px, transparent 1px);
        background-size: 28px 28px;
    }
    .inst-pattern-waves {
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 30 Q15 20 30 30 T60 30' fill='none' stroke='%2306b6d4' stroke-opacity='0.08' stroke-width='2'/%3E%3C/svg%3E");
    }
    .inst-pattern-flow {
        background-image: linear-gradient(135deg, rgb(59 130 246 / 0.04) 25%, transparent 25%);
        background-size: 20px 20px;
    }
    .inst-pattern-lines {
        background-image: repeating-linear-gradient(
            -45deg,
            transparent,
            transparent 8px,
            rgb(245 158 11 / 0.06) 8px,
            rgb(245 158 11 / 0.06) 9px
        );
    }
    .inst-pattern-rank {
        background-image: linear-gradient(180deg, rgb(16 185 129 / 0.05) 0%, transparent 40%);
    }
    .inst-pattern-bolt {
        background-image: radial-gradient(ellipse at top right, rgb(249 115 22 / 0.12), transparent 50%);
    }
    .inst-pattern-queue {
        background-image: repeating-linear-gradient(
            90deg,
            rgb(148 163 184 / 0.08) 0,
            rgb(148 163 184 / 0.08) 2px,
            transparent 2px,
            transparent 12px
        );
    }
    .inst-pattern-docs {
        background-image: linear-gradient(rgb(244 63 94 / 0.04) 2px, transparent 2px);
        background-size: 100% 32px;
    }
    .inst-pattern-timeline {
        background-image: linear-gradient(90deg, rgb(113 113 122 / 0.06) 1px, transparent 1px);
        background-size: 40px 100%;
    }
    .inst-pattern-chart {
        background-image: linear-gradient(rgb(192 38 211 / 0.04) 1px, transparent 1px),
            linear-gradient(90deg, rgb(192 38 211 / 0.04) 1px, transparent 1px);
        background-size: 32px 32px;
    }

    .inst-module [data-inst-table] thead {
        @apply text-xs uppercase tracking-wide;
    }
    .inst-module[data-module="resultados"] [data-inst-table] thead {
        @apply bg-emerald-800 text-emerald-50;
    }
    .inst-module[data-module="documentos"] [data-inst-table] tbody tr:hover {
        @apply bg-rose-50/50;
    }
    .inst-module[data-module="historial"] [data-inst-timeline-item]::before {
        content: '';
        @apply absolute -left-6 top-3 h-3 w-3 rounded-full border-2 border-white bg-zinc-500 shadow;
    }
    .inst-module[data-module="asignacion"] [data-inst-primary-btn] {
        @apply bg-orange-500 shadow-lg shadow-orange-200 hover:bg-orange-600;
    }
    .inst-module[data-module="documentos"] [data-inst-filter-active] {
        @apply bg-rose-600 text-white border-rose-600;
    }
    .inst-module[data-module="reportes"] [data-inst-export-card]:nth-child(3n+1) {
        @apply border-fuchsia-200 bg-gradient-to-br from-fuchsia-50 to-white;
    }
    .inst-module[data-module="evaluacion"] [data-inst-table] tbody tr:hover {
        @apply bg-amber-50/60;
    }
    .inst-module[data-module="lista-espera"] [data-inst-queue-row]:first-child {
        @apply ring-2 ring-amber-400 ring-offset-2;
    }
</style>
