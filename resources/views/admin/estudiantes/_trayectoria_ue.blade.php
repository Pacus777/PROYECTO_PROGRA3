<div class="mt-10 max-w-3xl">
    <h2 class="text-lg font-semibold text-slate-900">Trayectoria con unidades educativas</h2>
    <p class="mt-1 text-sm text-slate-500">
        Historial de postulaciones por colegio. El RUDE del estudiante es el mismo en todas las filas.
    </p>

    <div class="mt-4 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-100 bg-slate-50 text-left">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Unidad educativa</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Gestión / curso</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Estado</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trayectoriaPostulaciones as $pos)
                    @php
                        $oac = $pos->ofertaAcademica;
                        $ue = $oac?->unidadEducativa;
                        $esMatriculaActual = $estudiante->id_ued_mat_est && $ue && (int) $ue->id_ued === (int) $estudiante->id_ued_mat_est;
                    @endphp
                    <tr class="border-b border-slate-50 last:border-0 {{ $esMatriculaActual ? 'bg-amber-50/50' : '' }}">
                        <td class="px-4 py-3">
                            <span class="font-medium text-slate-900">{{ $ue?->nombre_ued ?? '—' }}</span>
                            @if($ue?->codigo_ued)
                                <span class="mt-0.5 block font-mono text-xs text-indigo-600">UE {{ $ue->codigo_ued }}</span>
                            @endif
                            @if($esMatriculaActual)
                                <span class="mt-1 inline-block rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold uppercase text-amber-800">Matrícula actual</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ $oac?->gestion?->nombre_ges ?? '—' }}
                            @if($oac?->curso)
                                <span class="block text-xs text-slate-400">{{ $oac->curso->nombre_cur ?? '' }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-700">{{ $pos->estadoPostulacion?->nombre_ept ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $pos->fecha_pos?->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">
                            Sin postulaciones registradas. La relación con un colegio aparecerá aquí cuando exista una postulación o al asignar matrícula actual.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
