@php
    $u = $usuario ?? null;
    $activoVal = old('activo_usu', $u->activo_usu ?? true);
    if ($activoVal === '0' || $activoVal === 0 || $activoVal === false) {
        $activoVal = false;
    } else {
        $activoVal = (bool) $activoVal;
    }
    $roleHints = $roles->mapWithKeys(fn ($rol) => [(string) $rol->id_rol => \App\Support\Roles::description($rol->nombre_rol)])->all();
    $selectedRolId = (string) old('id_rol_usu', $u->id_rol_usu ?? $roles->first()?->id_rol ?? '');
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2" x-data="{ hints: @js($roleHints), selected: @js($selectedRolId) }">
        <label for="id_rol_usu" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Tipo de cuenta</label>
        <select name="id_rol_usu" id="id_rol_usu" required @change="selected = $event.target.value"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
            @foreach($roles as $rol)
                <option value="{{ $rol->id_rol }}" @selected(old('id_rol_usu', $u->id_rol_usu ?? '') == $rol->id_rol)>{{ \App\Support\Roles::label($rol->nombre_rol) }}</option>
            @endforeach
        </select>
        <p class="mt-2 rounded-lg bg-indigo-50 px-3 py-2 text-xs text-indigo-900 leading-relaxed" x-show="hints[selected]" x-text="hints[selected]"></p>
        @error('id_rol_usu')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label for="id_ued_usu" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Unidad educativa (código UE)</label>
        <p class="mb-1.5 text-xs text-slate-500">Obligatorio para cuenta de <strong>unidad educativa</strong> (director o secretaría).</p>
        <select name="id_ued_usu" id="id_ued_usu"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
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
        <label for="correo_usu" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Correo de acceso</label>
        <input type="email" name="correo_usu" id="correo_usu" value="{{ old('correo_usu', $u->correo_usu ?? '') }}" required
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
        @error('correo_usu')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label for="password_usu" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">
            Contraseña @if($u)(opcional) @else<span class="text-rose-600">*</span>@endif
        </label>
        <input type="password" name="password_usu" id="password_usu" @if(!$u) required @endif autocomplete="new-password"
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300"
               placeholder="{{ $u ? 'Dejar vacío para no cambiar' : 'Mínimo 8 caracteres' }}">
        @error('password_usu')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="activo_usu" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">Estado</label>
        <select name="activo_usu" id="activo_usu"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <option value="1" @selected($activoVal)>Activo</option>
            <option value="0" @selected(! $activoVal)>Inactivo</option>
        </select>
        @error('activo_usu')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>
