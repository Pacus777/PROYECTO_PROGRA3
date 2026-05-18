{{-- RUDE y código interno del postulante --}}
<div class="mb-6 space-y-4 rounded-xl border-2 border-indigo-200 bg-indigo-50 p-4">
    <div>
        <label for="rude_est" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-indigo-700">
            RUDE (Registro Único de Estudiantes)
            <span class="ml-1 font-normal text-indigo-500">— Bolivia, solo números</span>
        </label>
        <input type="text" name="rude_est" id="rude_est"
               value="{{ old('rude_est', $estudiante->rude_est ?? '') }}"
               inputmode="numeric" maxlength="12"
               placeholder="Ej: 1234567890"
               class="w-full rounded-xl border border-indigo-200 bg-white px-4 py-3 text-sm font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-400">
        @error('rude_est')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        <p class="mt-1.5 text-xs text-indigo-700">El tutor puede vincular al estudiante ingresando este RUDE.</p>
    </div>
    <div>
        <label for="codigo_est" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-indigo-600">
            Código de vínculo en la app (opcional)
        </label>
        <input type="text" name="codigo_est" id="codigo_est"
               value="{{ old('codigo_est', $estudiante->codigo_est ?? '') }}"
               placeholder="Si lo dejas vacío y hay RUDE, se usará el RUDE"
               class="w-full rounded-xl border border-indigo-100 bg-white px-4 py-3 text-sm font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-300">
        @error('codigo_est')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        <p class="mt-1.5 text-xs text-indigo-600">No es el código del colegio (<code class="rounded bg-white px-1">codigo_ued</code>).</p>
    </div>
</div>
