@extends('layouts.dashboard')

@section('title', 'Ofertas y cupos | Admin institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Ofertas académicas</span>
@endsection

@section('content')
    @php
        $pageSubtitle = $unidad
            ? trim($unidad->nombre_ued . ($unidad->codigo_ued ? ' (' . $unidad->codigo_ued . ')' : '')) . ' — gestione ofertas de admisión y cupos por gestión, nivel, curso y paralelo.'
            : 'Ofertas académicas y cupos de su unidad educativa.';
    @endphp

    <x-institucional.page module="ofertas" title="Ofertas y cupos" :subtitle="$pageSubtitle">
        <x-slot:actions>
            <x-admin.export-report route="admin.institucional.ofertas.export" />
        </x-slot:actions>

        <x-slot:kpis>
            <x-institucional.kpi-grid module="ofertas" :items="[
                ['label' => 'Ofertas (filtro)', 'value' => $resumen['total']],
                ['label' => 'Con cupo definido', 'value' => $resumen['con_cupo']],
                ['label' => 'Cupos disponibles', 'value' => $resumen['cupos_disponibles']],
                ['label' => 'Postulaciones recibidas', 'value' => $resumen['postulaciones']],
            ]" />
        </x-slot:kpis>

        <x-institucional.panel module="ofertas" title="Información">
            <div class="p-5 text-sm text-indigo-900">
                <p>La combinación <strong>gestión + nivel + curso + paralelo</strong> debe ser única en su unidad.
                    El nivel debe coincidir con el del curso. Defina cupos totales y disponibles al registrar o editar cada oferta.</p>
                <a href="{{ route('admin.institucional.academic.index') }}" class="mt-2 inline-block text-xs font-semibold text-indigo-700 hover:underline">Gestionar catálogo académico →</a>
            </div>
        </x-institucional.panel>

        <x-institucional.panel module="ofertas" title="Filtros">
            <form method="GET" class="flex flex-wrap items-end gap-3 p-5">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-500">Gestión</label>
                    <select name="id_ges_oac" class="min-w-[130px] rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                        <option value="">Todas</option>
                        @foreach($gestiones as $g)
                            <option value="{{ $g->id_ges }}" @selected(request('id_ges_oac') == $g->id_ges)>{{ $g->nombre_ges }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-500">Nivel</label>
                    <select name="id_niv_oac" class="min-w-[130px] rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                        <option value="">Todos</option>
                        @foreach($niveles as $n)
                            <option value="{{ $n->id_niv }}" @selected(request('id_niv_oac') == $n->id_niv)>{{ $n->nombre_niv }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-500">Curso</label>
                    <select name="id_cur_oac" class="min-w-[130px] rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                        <option value="">Todos</option>
                        @foreach($cursos as $c)
                            <option value="{{ $c->id_cur }}" @selected(request('id_cur_oac') == $c->id_cur)>{{ $c->nombre_cur }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Filtrar</button>
                <a href="{{ route('admin.institucional.ofertas.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Limpiar</a>
            </form>
        </x-institucional.panel>

        <x-institucional.panel module="ofertas" title="Nueva oferta">
            <section class="p-5"
                     x-data="ofertaForm({
                        cursos: @js($cursosParaJs),
                        paralelos: @js($paralelosParaJs),
                        nivelId: '{{ old('id_niv_oac') }}',
                        cursoId: '{{ old('id_cur_oac') }}',
                        paraleloId: '{{ old('id_par_oac') }}'
                     })">
                <form method="POST" action="{{ route('admin.institucional.ofertas.store') }}" class="grid gap-3 md:grid-cols-3 lg:grid-cols-4">
                    @csrf
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-500">Gestión</label>
                        <select name="id_ges_oac" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                            @foreach($gestiones as $g)
                                <option value="{{ $g->id_ges }}" @selected(old('id_ges_oac', $gestionActiva?->id_ges) == $g->id_ges)>{{ $g->nombre_ges }}</option>
                            @endforeach
                        </select>
                        @error('id_ges_oac')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-500">Nivel</label>
                        <select name="id_niv_oac" x-model="nivelId" @change="onNivelChange" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                            <option value="">Seleccione…</option>
                            @foreach($niveles as $n)
                                <option value="{{ $n->id_niv }}">{{ $n->nombre_niv }}</option>
                            @endforeach
                        </select>
                        @error('id_niv_oac')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-500">Curso</label>
                        <select name="id_cur_oac" x-model="cursoId" @change="onCursoChange" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                            <option value="">Seleccione nivel primero</option>
                            <template x-for="c in cursosFiltrados" :key="c.id">
                                <option :value="c.id" x-text="c.nombre"></option>
                            </template>
                        </select>
                        @error('id_cur_oac')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-500">Paralelo</label>
                        <select name="id_par_oac" x-model="paraleloId" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                            <option value="">Seleccione curso</option>
                            <template x-for="p in paralelosFiltrados" :key="p.id">
                                <option :value="p.id" x-text="p.nombre"></option>
                            </template>
                        </select>
                        @error('id_par_oac')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold text-slate-500">Descripción</label>
                        <input name="descripcion_oac" value="{{ old('descripcion_oac') }}" maxlength="255" placeholder="Opcional"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-500">Cupos total</label>
                        <input type="number" min="0" name="total_cup" value="{{ old('total_cup', 0) }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-500">Cupos disponibles</label>
                        <input type="number" min="0" name="disponibles_cup" value="{{ old('disponibles_cup', 0) }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                        @error('disponibles_cup')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Registrar oferta</button>
                    </div>
                </form>
            </section>
        </x-institucional.panel>

        <x-institucional.panel module="ofertas" title="Ofertas registradas">
            <div class="overflow-x-auto">
                <table data-inst-table class="min-w-full text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-left">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Gestión</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Nivel / curso / paralelo</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Descripción</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Cupos</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-slate-400">Postul.</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ofertas as $oferta)
                            @php($cupo = $oferta->cupos->first())
                            <tr class="border-b border-slate-50 hover:bg-indigo-50/30 last:border-0">
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $oferta->gestion->nombre_ges ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-700">
                                    <p>{{ $oferta->nivel->nombre_niv ?? '—' }}</p>
                                    <p class="text-xs text-slate-500">{{ $oferta->curso->nombre_cur ?? '—' }} · Paralelo {{ $oferta->paralelo->nombre_par ?? '—' }}</p>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $oferta->descripcion_oac ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    @if($cupo)
                                        <form method="POST" action="{{ route('admin.institucional.cupos.update', $cupo) }}" class="flex flex-wrap items-center gap-2">
                                            @csrf @method('PUT')
                                            <label class="text-[10px] text-slate-400">Total</label>
                                            <input type="number" min="0" name="total_cup" value="{{ $cupo->total_cup }}" class="w-16 rounded border border-slate-200 px-2 py-1 text-xs">
                                            <label class="text-[10px] text-slate-400">Disp.</label>
                                            <input type="number" min="0" name="disponibles_cup" value="{{ $cupo->disponibles_cup }}" class="w-16 rounded border border-slate-200 px-2 py-1 text-xs">
                                            <button class="rounded bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700">OK</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.institucional.cupos.store') }}" class="flex flex-wrap items-center gap-2">
                                            @csrf
                                            <input type="hidden" name="id_oac_cup" value="{{ $oferta->id_oac }}">
                                            <input type="number" min="0" name="total_cup" placeholder="Total" class="w-16 rounded border border-slate-200 px-2 py-1 text-xs" required>
                                            <input type="number" min="0" name="disponibles_cup" placeholder="Disp." class="w-16 rounded border border-slate-200 px-2 py-1 text-xs" required>
                                            <button class="rounded bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700">+ Cupo</button>
                                        </form>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($oferta->postulaciones_count > 0)
                                        <a href="{{ route('admin.institucional.postulaciones.index', ['id_cur_oac' => $oferta->id_cur_oac, 'id_ges_oac' => $oferta->id_ges_oac]) }}"
                                           class="font-semibold text-indigo-600 hover:underline">{{ $oferta->postulaciones_count }}</a>
                                    @else
                                        <span class="text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.institucional.ofertas.edit', $oferta) }}" class="mr-2 text-xs font-semibold text-indigo-600 hover:underline">Editar</a>
                                    <form method="POST" action="{{ route('admin.institucional.ofertas.destroy', $oferta) }}" class="inline" onsubmit="return confirm('¿Eliminar esta oferta?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                                    No hay ofertas para su unidad. Registre la primera arriba o revise el
                                    <a href="{{ route('admin.institucional.academic.index') }}" class="text-indigo-600 hover:underline">catálogo académico</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($ofertas->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">{{ $ofertas->links() }}</div>
            @endif
        </x-institucional.panel>

        @include('admin.institucional.ofertas._alpine-oferta-form')
    </x-institucional.page>
@endsection
