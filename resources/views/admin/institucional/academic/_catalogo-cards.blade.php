<div class="flex flex-row flex-nowrap gap-4 overflow-x-auto pb-3 snap-x snap-mandatory">
    @foreach($arbolCatalogo as $rama)
        @php
            $nivel = $rama['nivel'];
            $cursosRama = $rama['cursos'];
        @endphp
        <article class="relative flex aspect-square w-[280px] shrink-0 snap-start flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-md">
            <div class="shrink-0 border-b border-slate-100 bg-gradient-to-br from-indigo-50 to-white p-4">
                <div class="flex items-start gap-3">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h6"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <h3 class="line-clamp-2 text-sm font-bold leading-tight text-slate-900">{{ $nivel->nombre_niv }}</h3>
                        <p class="mt-1 text-[11px] text-slate-500">{{ $cursosRama->count() }} curso(s)</p>
                    </div>
                </div>
                @if($nivel->ofertas_unidad_count > 0)
                    <span class="mt-2 inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-800">{{ $nivel->ofertas_unidad_count }} oferta(s)</span>
                @endif
                <div class="mt-3 flex gap-2">
                    <button type="button" @click="editNivel = editNivel === {{ $nivel->id_niv }} ? null : {{ $nivel->id_niv }}; editCurso = null; editParalelo = null"
                            class="flex-1 rounded-lg bg-white py-1.5 text-[11px] font-semibold text-indigo-600 ring-1 ring-indigo-100 hover:bg-indigo-50">
                        Editar
                    </button>
                    <form method="POST" action="{{ route('admin.institucional.niveles.destroy', $nivel) }}" class="flex-1"
                          onsubmit="return confirm('¿Eliminar el nivel «{{ addslashes($nivel->nombre_niv) }}»?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full rounded-lg bg-white py-1.5 text-[11px] font-semibold text-rose-600 ring-1 ring-rose-100 hover:bg-rose-50">Eliminar</button>
                    </form>
                </div>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto p-3">
                @if($cursosRama->isEmpty())
                    <p class="flex h-full min-h-[120px] items-center justify-center text-center text-xs leading-relaxed text-slate-500">Sin cursos.<br>Agréguelos arriba.</p>
                @else
                    <ul class="space-y-2">
                        @foreach($cursosRama as $ramaCurso)
                            @php
                                $curso = $ramaCurso['curso'];
                                $paralelosCurso = $ramaCurso['paralelos'];
                            @endphp
                            <li class="rounded-xl border border-slate-100 bg-slate-50 p-2.5">
                                <div class="flex items-start justify-between gap-1">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-xs font-semibold text-slate-800" title="{{ $curso->nombre_cur }}">{{ $curso->nombre_cur }}</p>
                                        @if($curso->ofertas_unidad_count > 0)
                                            <span class="text-[10px] font-medium text-emerald-600">{{ $curso->ofertas_unidad_count }} oferta(s)</span>
                                        @endif
                                    </div>
                                    <div class="flex shrink-0 gap-0.5">
                                        <button type="button" @click="editCurso = editCurso === {{ $curso->id_cur }} ? null : {{ $curso->id_cur }}; editParalelo = null; editNivel = null"
                                                class="rounded p-1 text-indigo-600 hover:bg-indigo-100" title="Editar curso">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.institucional.cursos.destroy', $curso) }}" class="inline"
                                              onsubmit="return confirm('¿Eliminar curso?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="rounded p-1 text-rose-500 hover:bg-rose-50" title="Eliminar curso">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @if($paralelosCurso->isNotEmpty())
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        @foreach($paralelosCurso as $paralelo)
                                            <span class="inline-flex items-center gap-0.5 rounded-md bg-white px-1.5 py-0.5 text-[10px] font-semibold text-slate-600 ring-1 ring-slate-200">
                                                {{ $paralelo->nombre_par }}
                                                <button type="button" @click="editParalelo = editParalelo === {{ $paralelo->id_par }} ? null : {{ $paralelo->id_par }}; editCurso = null; editNivel = null"
                                                        class="text-indigo-500 hover:text-indigo-700" title="Editar">✎</button>
                                                <form method="POST" action="{{ route('admin.institucional.paralelos.destroy', $paralelo) }}" class="inline"
                                                      onsubmit="return confirm('¿Eliminar paralelo {{ $paralelo->nombre_par }}?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-rose-400 hover:text-rose-600" title="Eliminar">×</button>
                                                </form>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Overlay editar nivel --}}
            <div x-show="editNivel === {{ $nivel->id_niv }}" x-cloak
                 class="absolute inset-0 z-20 flex flex-col overflow-y-auto bg-white p-4">
                <p class="mb-3 text-xs font-bold uppercase tracking-wide text-indigo-600">Editar nivel</p>
                <form method="POST" action="{{ route('admin.institucional.niveles.update', $nivel) }}" class="flex flex-1 flex-col gap-3">
                    @csrf @method('PUT')
                    <input name="nombre_niv" value="{{ $nivel->nombre_niv }}" required class="{{ $inputClass }} text-sm">
                    <div class="mt-auto flex gap-2">
                        <button type="submit" class="flex-1 rounded-lg bg-indigo-600 py-2 text-xs font-semibold text-white">Guardar</button>
                        <button type="button" @click="editNivel = null" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600">Cancelar</button>
                    </div>
                </form>
            </div>

            @foreach($cursosRama as $ramaCurso)
                @php $curso = $ramaCurso['curso']; @endphp
                <div x-show="editCurso === {{ $curso->id_cur }}" x-cloak
                     class="absolute inset-0 z-20 flex flex-col overflow-y-auto bg-white p-4">
                    <p class="mb-3 text-xs font-bold uppercase tracking-wide text-cyan-600">Editar curso</p>
                    <form method="POST" action="{{ route('admin.institucional.cursos.update', $curso) }}" class="flex flex-1 flex-col gap-2">
                        @csrf @method('PUT')
                        <select name="id_niv_cur" class="{{ $selectClass }} text-xs">
                            @foreach($niveles as $n)
                                <option value="{{ $n->id_niv }}" @selected($curso->id_niv_cur === $n->id_niv)>{{ $n->nombre_niv }}</option>
                            @endforeach
                        </select>
                        <input name="nombre_cur" value="{{ $curso->nombre_cur }}" required class="{{ $inputClass }} text-sm">
                        <div class="mt-auto flex gap-2">
                            <button type="submit" class="flex-1 rounded-lg bg-indigo-600 py-2 text-xs font-semibold text-white">Guardar</button>
                            <button type="button" @click="editCurso = null" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600">Cancelar</button>
                        </div>
                    </form>
                </div>
            @endforeach

            @foreach($cursosRama as $ramaCurso)
                @foreach($ramaCurso['paralelos'] as $paralelo)
                    <div x-show="editParalelo === {{ $paralelo->id_par }}" x-cloak
                         class="absolute inset-0 z-20 flex flex-col overflow-y-auto bg-white p-4">
                        <p class="mb-3 text-xs font-bold uppercase tracking-wide text-violet-600">Editar paralelo</p>
                        <form method="POST" action="{{ route('admin.institucional.paralelos.update', $paralelo) }}" class="flex flex-1 flex-col gap-2">
                            @csrf @method('PUT')
                            <select name="id_cur_par" class="{{ $selectClass }} text-xs">
                                @foreach($cursos as $c)
                                    <option value="{{ $c->id_cur }}" @selected($paralelo->id_cur_par === $c->id_cur)>{{ $c->nombre_cur }}</option>
                                @endforeach
                            </select>
                            <input name="nombre_par" value="{{ $paralelo->nombre_par }}" maxlength="16" required class="{{ $inputClass }} text-sm">
                            <div class="mt-auto flex gap-2">
                                <button type="submit" class="flex-1 rounded-lg bg-indigo-600 py-2 text-xs font-semibold text-white">Guardar</button>
                                <button type="button" @click="editParalelo = null" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600">Cancelar</button>
                            </div>
                        </form>
                    </div>
                @endforeach
            @endforeach
        </article>
    @endforeach
</div>
