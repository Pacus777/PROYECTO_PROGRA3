<div class="mb-6 rounded-xl border border-amber-200 bg-amber-50/80 p-4">
    <label for="id_ued_mat_est" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-amber-900">
        Unidad educativa de matrícula actual
    </label>
    <select name="id_ued_mat_est" id="id_ued_mat_est"
            class="w-full rounded-xl border border-amber-200 bg-white px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400">
        <option value="">Sin matrícula registrada / no aplica</option>
        @foreach($unidades as $ue)
            <option value="{{ $ue->id_ued }}"
                @selected((string) old('id_ued_mat_est', $estudiante?->id_ued_mat_est ?? '') === (string) $ue->id_ued)>
                @if($ue->codigo_ued)[{{ $ue->codigo_ued }}] @endif{{ $ue->nombre_ued }}
            </option>
        @endforeach
    </select>
    @error('id_ued_mat_est')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    <p class="mt-2 text-xs text-amber-900">
        Si el estudiante se traslada a otro colegio, cambia solo esta unidad. El <strong>RUDE permanece igual</strong>.
    </p>
</div>
