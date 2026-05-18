@php
    $p = ($usuario ?? null)?->persona;
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label for="nombres_per" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Nombres</label>
        <input type="text" name="nombres_per" id="nombres_per" value="{{ old('nombres_per', $p->nombres_per ?? '') }}" required
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
        @error('nombres_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="ap_paterno_per" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Apellido paterno</label>
        <input type="text" name="ap_paterno_per" id="ap_paterno_per" value="{{ old('ap_paterno_per', $p->ap_paterno_per ?? '') }}" required
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
        @error('ap_paterno_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="ap_materno_per" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Apellido materno</label>
        <input type="text" name="ap_materno_per" id="ap_materno_per" value="{{ old('ap_materno_per', $p->ap_materno_per ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
        @error('ap_materno_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="ci_per" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">CI</label>
        <input type="text" name="ci_per" id="ci_per" value="{{ old('ci_per', $p->ci_per ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
        @error('ci_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="fecha_nac_per" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Fecha de nacimiento</label>
        <input type="date" name="fecha_nac_per" id="fecha_nac_per" value="{{ old('fecha_nac_per', optional($p?->fecha_nac_per)->format('Y-m-d')) }}"
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
        @error('fecha_nac_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="genero_per" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Género (M/F)</label>
        <input type="text" name="genero_per" id="genero_per" maxlength="1" value="{{ old('genero_per', $p->genero_per ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
        @error('genero_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="correo_per" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Correo (persona)</label>
        <input type="email" name="correo_per" id="correo_per" value="{{ old('correo_per', $p->correo_per ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
        @error('correo_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="telefono_per" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Teléfono</label>
        <input type="text" name="telefono_per" id="telefono_per" value="{{ old('telefono_per', $p->telefono_per ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
        @error('telefono_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>
