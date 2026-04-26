@extends('layouts.dashboard')

@section('title', 'Editar oferta | Admin institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span>Ofertas académicas</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Editar</span>
@endsection

@section('content')
    <div class="mb-6">
        <p class="text-xs text-slate-400">Panel / Ofertas académicas</p>
        <h1 class="text-2xl font-bold text-slate-900">Editar oferta</h1>
    </div>

    <form method="POST" action="{{ route('admin.institucional.ofertas.update', $oferta_academica) }}" class="grid max-w-3xl gap-3 rounded-2xl bg-white p-5 shadow-sm md:grid-cols-2">
        @csrf @method('PUT')
        <select name="id_ges_oac" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">@foreach($gestiones as $g)<option value="{{ $g->id_ges }}" @selected($oferta_academica->id_ges_oac === $g->id_ges)>{{ $g->nombre_ges }}</option>@endforeach</select>
        <select name="id_niv_oac" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">@foreach($niveles as $n)<option value="{{ $n->id_niv }}" @selected($oferta_academica->id_niv_oac === $n->id_niv)>{{ $n->nombre_niv }}</option>@endforeach</select>
        <select name="id_cur_oac" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">@foreach($cursos as $c)<option value="{{ $c->id_cur }}" @selected($oferta_academica->id_cur_oac === $c->id_cur)>{{ $c->nombre_cur }}</option>@endforeach</select>
        <select name="id_par_oac" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">@foreach($paralelos as $p)<option value="{{ $p->id_par }}" @selected($oferta_academica->id_par_oac === $p->id_par)>{{ $p->nombre_par }}</option>@endforeach</select>
        <input name="descripcion_oac" value="{{ $oferta_academica->descripcion_oac }}" placeholder="Descripción" class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
        <div class="md:col-span-2 flex gap-3">
            <button class="rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white transition hover:from-indigo-700 hover:to-purple-700">Actualizar oferta</button>
            <a href="{{ route('admin.institucional.ofertas.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancelar</a>
        </div>
    </form>
@endsection

