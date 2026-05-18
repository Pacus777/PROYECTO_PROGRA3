<div class="mb-8 max-w-4xl overflow-hidden rounded-2xl bg-white shadow-sm">
    <div class="border-b border-slate-100 px-6 py-4">
        <h2 class="text-base font-semibold text-slate-800">Postulantes con matrícula en esta UE</h2>
        <p class="mt-1 text-xs text-slate-500">El RUDE es del estudiante y no cambia al trasladarse; actualiza la matrícula en Postulantes → Editar.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-100 bg-slate-50 text-left">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Nombre</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">RUDE</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-400">Ficha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($matriculados as $est)
                    @php $pe = $est->persona; $nom = trim(($pe->nombres_per ?? '').' '.($pe->ap_paterno_per ?? '')); @endphp
                    <tr class="border-b border-slate-50 last:border-0">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $nom ?: '—' }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-emerald-800">{{ $est->rude_est ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.estudiantes.edit', $est) }}" class="text-xs font-semibold text-indigo-600 hover:underline">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500">Ningún postulante con matrícula actual en esta unidad.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
