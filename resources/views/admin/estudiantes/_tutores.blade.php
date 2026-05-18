{{-- Tutores / apoderados vinculados (p. ej. papá y mamá) --}}
<div class="mt-10 max-w-2xl">
    <div class="mb-4">
        <h2 class="text-lg font-semibold text-slate-900">Tutores vinculados</h2>
        <p class="mt-1 text-sm text-slate-500">
            Puedes asignar más de un tutor al mismo estudiante (por ejemplo padre y madre). Cada uno inicia sesión con su propia cuenta.
        </p>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-100 bg-slate-50 text-left">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Nombre</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">CI</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-400">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tutoresVinculados as $tutor)
                    @php
                        $tp = $tutor->persona;
                        $nombreTutor = trim(($tp->nombres_per ?? '').' '.($tp->ap_paterno_per ?? '').' '.($tp->ap_materno_per ?? ''));
                    @endphp
                    <tr class="border-b border-slate-50 last:border-0">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $nombreTutor ?: '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $tp->ci_per ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST"
                                  action="{{ route('admin.estudiantes.tutores.detach', [$estudiante, $tutor]) }}"
                                  class="inline"
                                  onsubmit="return confirm('¿Desvincular a este tutor del estudiante?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-100">
                                    Desvincular
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500">
                            Ningún tutor vinculado. El apoderado puede vincularse con el RUDE desde su panel, o puedes asignarlo aquí.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tutoresDisponibles->isNotEmpty())
        <form method="POST"
              action="{{ route('admin.estudiantes.tutores.attach', $estudiante) }}"
              class="mt-4 flex flex-col gap-3 rounded-xl border border-dashed border-indigo-200 bg-indigo-50/50 p-4 sm:flex-row sm:items-end">
            @csrf
            <div class="flex-1">
                <label for="id_tut" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-indigo-700">
                    Agregar tutor
                </label>
                <select name="id_tut" id="id_tut" required
                        class="w-full rounded-xl border border-indigo-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="">Seleccione un tutor…</option>
                    @foreach($tutoresDisponibles as $tutor)
                        @php
                            $tp = $tutor->persona;
                            $label = trim(($tp->nombres_per ?? '').' '.($tp->ap_paterno_per ?? '').' '.($tp->ap_materno_per ?? ''));
                        @endphp
                        <option value="{{ $tutor->id_tut }}">{{ $label ?: 'Tutor #'.$tutor->id_tut }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                    class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                Vincular tutor
            </button>
        </form>
    @else
        <p class="mt-3 text-xs text-slate-500">
            No hay más tutores disponibles.
            <a href="{{ route('admin.usuarios.create') }}" class="font-semibold text-indigo-600 hover:underline">Crear usuario tutor</a>
            o revisa la lista en
            <a href="{{ route('admin.tutores.index') }}" class="font-semibold text-indigo-600 hover:underline">Tutores</a>.
        </p>
    @endif
</div>
