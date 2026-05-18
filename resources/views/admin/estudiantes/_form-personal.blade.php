@php $p = ($estudiante ?? null)?->persona; @endphp

<div class="space-y-4">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Nombres *</label>
            <input type="text" name="nombres_per" value="{{ old('nombres_per', $p->nombres_per ?? '') }}" required
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
            @error('nombres_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Ap. Paterno *</label>
            <input type="text" name="ap_paterno_per" value="{{ old('ap_paterno_per', $p->ap_paterno_per ?? '') }}" required
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
            @error('ap_paterno_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Ap. Materno</label>
            <input type="text" name="ap_materno_per" value="{{ old('ap_materno_per', $p->ap_materno_per ?? '') }}"
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">CI</label>
            <input type="text" name="ci_per" value="{{ old('ci_per', $p->ci_per ?? '') }}"
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Fecha de nacimiento</label>
            <input type="date" name="fecha_nac_per" value="{{ old('fecha_nac_per', optional($p?->fecha_nac_per)->format('Y-m-d')) }}"
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Género</label>
            <select name="genero_per"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <option value="">—</option>
                <option value="M" @selected(old('genero_per', $p->genero_per ?? '') === 'M')>Masculino</option>
                <option value="F" @selected(old('genero_per', $p->genero_per ?? '') === 'F')>Femenino</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Correo</label>
            <input type="email" name="correo_per" value="{{ old('correo_per', $p->correo_per ?? '') }}"
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Teléfono</label>
            <input type="text" name="telefono_per" value="{{ old('telefono_per', $p->telefono_per ?? '') }}"
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>
    </div>
</div>
