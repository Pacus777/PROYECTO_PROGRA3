@extends('layouts.landing')

@section('title', 'Colegios | AdmisiónEscolar')

@section('content')
    <x-landing.navbar />

    <section class="relative overflow-hidden bg-gradient-to-br from-slate-50 via-blue-50/40 to-cyan-50/30 border-b border-slate-100">
        <div class="absolute -right-20 top-10 h-72 w-72 rounded-full bg-blue-200/30 blur-3xl"></div>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16 md:py-20 relative">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:underline">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Volver al inicio
            </a>
            <h1 class="mt-4 text-4xl md:text-5xl font-extrabold text-slate-900">Colegios e instituciones</h1>
            <p class="mt-4 max-w-2xl text-lg text-slate-600 leading-relaxed">
                Busque su unidad educativa, revise convocatorias abiertas y comience la postulación como tutor o apoderado.
            </p>

            @php
                $totalAbiertas = $unidades->sum('ofertas_abiertas_count');
                $conConvocatoria = $unidades->where('ofertas_abiertas_count', '>', 0)->count();
            @endphp
            <div class="mt-8 flex flex-wrap gap-4">
                <div class="rounded-2xl border border-white/80 bg-white/80 backdrop-blur px-5 py-3 shadow-sm">
                    <p class="text-2xl font-extrabold text-slate-900">{{ $unidades->count() }}</p>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Colegios</p>
                </div>
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50/80 px-5 py-3 shadow-sm">
                    <p class="text-2xl font-extrabold text-emerald-700">{{ $conConvocatoria }}</p>
                    <p class="text-xs font-medium text-emerald-600 uppercase tracking-wide">Con convocatoria abierta</p>
                </div>
                <div class="rounded-2xl border border-blue-100 bg-blue-50/80 px-5 py-3 shadow-sm">
                    <p class="text-2xl font-extrabold text-blue-700">{{ $totalAbiertas }}</p>
                    <p class="text-xs font-medium text-blue-600 uppercase tracking-wide">Cupos disponibles</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 md:py-16" x-data="colegiosFilter()">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            @if($unidades->isEmpty())
                <div class="rounded-3xl border border-dashed border-slate-200 bg-white px-8 py-16 text-center shadow-sm">
                    <p class="text-lg font-semibold text-slate-700">No hay colegios registrados todavía.</p>
                    <p class="mt-2 text-slate-500">Las unidades educativas aparecerán aquí cuando el administrador las dé de alta.</p>
                </div>
            @else
                <div class="mb-8 flex flex-col sm:flex-row gap-4 sm:items-center sm:justify-between">
                    <div class="relative flex-1 max-w-md">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="search" x-model="q" placeholder="Buscar por nombre, código o ciudad..."
                               class="w-full rounded-2xl border border-slate-200 bg-white py-3.5 pl-12 pr-4 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="filtro='todos'"
                                :class="filtro === 'todos' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-200'"
                                class="rounded-xl border px-4 py-2 text-xs font-semibold transition">Todos</button>
                        <button type="button" @click="filtro='abiertas'"
                                :class="filtro === 'abiertas' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-600 border-slate-200 hover:border-emerald-200'"
                                class="rounded-xl border px-4 py-2 text-xs font-semibold transition">Con convocatoria abierta</button>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($unidades as $unidad)
                        @php
                            $searchText = mb_strtolower(collect([
                                $unidad->nombre_ued,
                                $unidad->codigo_ued,
                                $unidad->direccion_ued,
                                $unidad->municipio->nombre_mun ?? null,
                                $unidad->municipio->provincia->departamento->nombre_dep ?? null,
                            ])->filter()->implode(' '));
                        @endphp
                        <div data-search="{{ e($searchText) }}"
                             data-abierta="{{ ($unidad->ofertas_abiertas_count ?? 0) > 0 ? '1' : '0' }}"
                             x-show="visible($el)"
                             x-transition>
                            @include('public.colegios._colegio-card', ['unidad' => $unidad])
                        </div>
                    @endforeach
                </div>

                <p x-show="q !== '' || filtro !== 'todos'" class="mt-8 text-center text-sm text-slate-500" x-cloak>
                    Use la búsqueda o el filtro para encontrar su colegio más rápido.
                </p>
            @endif
        </div>
    </section>

    <x-landing.footer />
@endsection
