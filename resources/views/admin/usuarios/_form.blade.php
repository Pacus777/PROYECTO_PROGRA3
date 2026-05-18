@php
    /** @var \App\Models\Usuario|null $usuario */
    $u = $usuario ?? null;
    $p = $u?->persona;
    $activoVal = old('activo_usu', $u->activo_usu ?? true);
    if ($activoVal === '0' || $activoVal === 0 || $activoVal === false) {
        $activoVal = false;
    } else {
        $activoVal = (bool) $activoVal;
    }
@endphp

<div class="grid gap-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide border-b border-slate-200 pb-2">Persona</h3>
    </div>
    <div>
        <label for="nombres_per" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Nombres</label>
        <input type="text" name="nombres_per" id="nombres_per" value="{{ old('nombres_per', $p->nombres_per ?? '') }}" required
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
        @error('nombres_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="ap_paterno_per" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Apellido paterno</label>
        <input type="text" name="ap_paterno_per" id="ap_paterno_per" value="{{ old('ap_paterno_per', $p->ap_paterno_per ?? '') }}" required
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
        @error('ap_paterno_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="ap_materno_per" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Apellido materno</label>
        <input type="text" name="ap_materno_per" id="ap_materno_per" value="{{ old('ap_materno_per', $p->ap_materno_per ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
        @error('ap_materno_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="ci_per" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">CI</label>
        <input type="text" name="ci_per" id="ci_per" value="{{ old('ci_per', $p->ci_per ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
        @error('ci_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="fecha_nac_per" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Fecha de nacimiento</label>
        <input type="date" name="fecha_nac_per" id="fecha_nac_per" value="{{ old('fecha_nac_per', optional($p?->fecha_nac_per)->format('Y-m-d')) }}"
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
        @error('fecha_nac_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="genero_per" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Género (M/F)</label>
        <input type="text" name="genero_per" id="genero_per" maxlength="1" value="{{ old('genero_per', $p->genero_per ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
        @error('genero_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="correo_per" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Correo (persona)</label>
        <input type="email" name="correo_per" id="correo_per" value="{{ old('correo_per', $p->correo_per ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
        @error('correo_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="telefono_per" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Teléfono</label>
        <input type="text" name="telefono_per" id="telefono_per" value="{{ old('telefono_per', $p->telefono_per ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
        @error('telefono_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2 pt-4">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide border-b border-slate-200 pb-2">Cuenta</h3>
    </div>
    @php
        $roleHints = $roles->mapWithKeys(fn ($rol) => [(string) $rol->id_rol => \App\Support\Roles::description($rol->nombre_rol)])->all();
        $selectedRolId = (string) old('id_rol_usu', $u->id_rol_usu ?? $roles->first()?->id_rol ?? '');
    @endphp
    <div x-data="{ hints: @js($roleHints), selected: @js($selectedRolId) }">
        <label for="id_rol_usu" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Tipo de cuenta</label>
        <select name="id_rol_usu" id="id_rol_usu" required
                @change="selected = $event.target.value"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
            @foreach($roles as $rol)
                <option value="{{ $rol->id_rol }}" @selected(old('id_rol_usu', $u->id_rol_usu ?? '') == $rol->id_rol)>{{ \App\Support\Roles::label($rol->nombre_rol) }}</option>
            @endforeach
        </select>
        <p class="mt-2 rounded-lg bg-indigo-50 px-3 py-2 text-xs text-indigo-900 leading-relaxed" x-show="hints[selected]" x-text="hints[selected]"></p>
        @error('id_rol_usu')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="id_ued_usu" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Unidad educativa (código UE)</label>
        <p class="mb-1.5 text-xs text-slate-500">Obligatorio solo para <strong>Unidad educativa (director / secretaría)</strong>: la cuenta queda ligada a ese colegio. No aplica al tutor ni al RUDE del estudiante.</p>
        <select name="id_ued_usu" id="id_ued_usu"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
            <option value="">— Sin asignar —</option>
            @foreach($unidades as $unidad)
                <option value="{{ $unidad->id_ued }}" @selected(old('id_ued_usu', $u->id_ued_usu ?? '') == $unidad->id_ued)>
                    @if($unidad->codigo_ued)[{{ $unidad->codigo_ued }}] @endif{{ $unidad->nombre_ued }}
                </option>
            @endforeach
        </select>
        @error('id_ued_usu')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label for="correo_usu" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Correo de acceso</label>
        <input type="email" name="correo_usu" id="correo_usu" value="{{ old('correo_usu', $u->correo_usu ?? '') }}" required
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
        @error('correo_usu')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label for="password_usu" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Contraseña @if($u)(dejar vacío para no cambiar) @else<span class="text-rose-600">*</span>@endif</label>
        <input type="password" name="password_usu" id="password_usu" @if(!$u) required @endif autocomplete="new-password"
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
               placeholder="{{ $u ? '••••••••' : 'Mínimo 8 caracteres' }}">
        @error('password_usu')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="activo_usu" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Estado</label>
        <select name="activo_usu" id="activo_usu"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
            <option value="1" @selected($activoVal)>Activo</option>
            <option value="0" @selected(! $activoVal)>Inactivo</option>
        </select>
        @error('activo_usu')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>
