@props(['compact' => false])

<div {{ $attributes->merge(['class' => $compact ? 'grid gap-2 sm:grid-cols-3 text-xs' : 'mb-6 grid gap-3 md:grid-cols-3']) }}>
    @foreach(\App\Support\Roles::assignable() as $roleKey)
        <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4">
            <p class="{{ $compact ? 'text-xs font-bold' : 'text-sm font-semibold' }} text-slate-900">{{ \App\Support\Roles::label($roleKey) }}</p>
            <p class="mt-1 {{ $compact ? 'text-[11px]' : 'text-xs' }} text-slate-600 leading-relaxed">{{ \App\Support\Roles::description($roleKey) }}</p>
        </div>
    @endforeach
</div>
