@extends('layouts.dashboard')

@section('title', 'Ofertas y cupos | Admin institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Ofertas académicas</span>
@endsection

@section('content')
    <div class="mb-8">
        <p class="text-xs text-slate-400">Panel / Ofertas académicas</p>
        <h1 class="text-2xl font-bold text-slate-900">Ofertas académicas y cupos</h1>
    </div>

    <form method="POST" action="{{ route('admin.institucional.ofertas.store') }}" class="mb-8 grid gap-3 rounded-2xl bg-white p-5 shadow-sm md:grid-cols-3">
        @csrf
        <select name="id_ges_oac" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">@foreach($gestiones as $g)<option value="{{ $g->id_ges }}">{{ $g->nombre_ges }}</option>@endforeach</select>
        <select name="id_niv_oac" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">@foreach($niveles as $n)<option value="{{ $n->id_niv }}">{{ $n->nombre_niv }}</option>@endforeach</select>
        <select name="id_cur_oac" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">@foreach($cursos as $c)<option value="{{ $c->id_cur }}">{{ $c->nombre_cur }}</option>@endforeach</select>
        <select name="id_par_oac" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">@foreach($paralelos as $p)<option value="{{ $p->id_par }}">{{ $p->nombre_par }}</option>@endforeach</select>
        <input name="descripcion_oac" placeholder="Descripción" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
        <button class="rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 text-sm font-semibold text-white transition hover:from-indigo-700 hover:to-purple-700">Crear oferta</button>
    </form>

    <div class="overflow-x-auto rounded-2xl bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="border-b border-slate-100 bg-slate-50 text-slate-500 uppercase text-xs"><tr><th class="px-4 py-3 text-left">Oferta</th><th class="px-4 py-3 text-left">Descripción</th><th class="px-4 py-3 text-left">Cupos</th><th class="px-4 py-3 text-right">Acciones</th></tr></thead>
            <tbody>
            @foreach($ofertas as $oferta)
                @php($cupo = $oferta->cupos->first())
                <tr class="border-b border-slate-50 transition hover:bg-indigo-50/30 last:border-0">
                    <td class="px-4 py-3">{{ $oferta->gestion->nombre_ges }} / {{ $oferta->nivel->nombre_niv }} / {{ $oferta->curso->nombre_cur }} {{ $oferta->paralelo->nombre_par }}</td>
                    <td class="px-4 py-3">{{ $oferta->descripcion_oac ?: '—' }}</td>
                    <td class="px-4 py-3">
                        @if($cupo)
                            <form method="POST" action="{{ route('admin.institucional.cupos.update', $cupo) }}" class="flex gap-2 items-center">
                                @csrf @method('PUT')
                                <input type="number" min="0" name="total_cup" value="{{ $cupo->total_cup }}" class="w-20 rounded border border-slate-200 bg-slate-50 px-2 py-1">
                                <input type="number" min="0" name="disponibles_cup" value="{{ $cupo->disponibles_cup }}" class="w-20 rounded border border-slate-200 bg-slate-50 px-2 py-1">
                                <button class="rounded bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700">Guardar</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.institucional.cupos.store') }}" class="flex gap-2 items-center">
                                @csrf
                                <input type="hidden" name="id_oac_cup" value="{{ $oferta->id_oac }}">
                                <input type="number" min="0" name="total_cup" placeholder="Total" class="w-20 rounded border border-slate-200 bg-slate-50 px-2 py-1">
                                <input type="number" min="0" name="disponibles_cup" placeholder="Disp." class="w-20 rounded border border-slate-200 bg-slate-50 px-2 py-1">
                                <button class="rounded bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700">Crear</button>
                            </form>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.institucional.ofertas.edit', $oferta) }}" class="mr-1 inline-flex rounded-lg bg-slate-100 px-2.5 py-2 text-xs font-semibold text-slate-600 transition hover:bg-indigo-100 hover:text-indigo-700">Editar</a>
                        <form method="POST" action="{{ route('admin.institucional.ofertas.destroy', $oferta) }}" class="inline" onsubmit="return confirm('¿Eliminar oferta?')">@csrf @method('DELETE')<button class="inline-flex rounded-lg bg-red-50 px-2.5 py-2 text-xs font-semibold text-rose-600 transition hover:bg-red-100">Eliminar</button></form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $ofertas->links() }}</div>
    </div>
@endsection

