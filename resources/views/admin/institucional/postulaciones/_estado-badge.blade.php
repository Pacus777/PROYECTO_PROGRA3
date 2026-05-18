@php
    $nombreEstado = strtolower($estado ?? '');
    $badge = match (true) {
        str_contains($nombreEstado, 'aprob') => 'bg-emerald-100 text-emerald-700',
        str_contains($nombreEstado, 'rechaz') => 'bg-red-100 text-red-600',
        str_contains($nombreEstado, 'evalu') => 'bg-blue-100 text-blue-700',
        str_contains($nombreEstado, 'enviad') => 'bg-violet-100 text-violet-700',
        str_contains($nombreEstado, 'borrador') => 'bg-slate-100 text-slate-600',
        default => 'bg-amber-100 text-amber-700',
    };
@endphp
<span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $badge }}">
    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
    {{ $estado ?: '—' }}
</span>
