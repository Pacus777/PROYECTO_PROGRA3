@extends('layouts.dashboard')

@section('title', 'Gestión académica | Admin institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Gestión académica</span>
@endsection

@section('content')
    @php
        $inputClass = 'w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100';
        $selectClass = $inputClass;
        $pageSubtitle = $unidad
            ? 'Organice el catálogo de '.$unidad->nombre_ued.($unidad->codigo_ued ? ' ('.$unidad->codigo_ued.')' : '').' para crear ofertas de admisión claras y consistentes.'
            : 'Defina niveles, cursos y paralelos antes de publicar ofertas.';
    @endphp

    <x-institucional.page module="academic" title="Gestión académica" :subtitle="$pageSubtitle">
        <x-slot:actions>
            <a href="{{ route('admin.institucional.ofertas.index') }}"
               class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-teal-700 shadow-md transition hover:bg-teal-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7L12 3 4 7l8 4 8-4zM4 12l8 4 8-4"/></svg>
                Ir a ofertas y cupos
            </a>
        </x-slot:actions>

        <x-slot:kpis>
            <x-institucional.kpi-grid module="academic" :items="[
                ['label' => 'Ofertas publicadas', 'value' => $resumen['ofertas_unidad']],
                ['label' => 'Niveles', 'value' => $resumen['niveles']],
                ['label' => 'Cursos', 'value' => $resumen['cursos']],
                ['label' => 'Paralelos', 'value' => $resumen['paralelos']],
            ]" />
        </x-slot:kpis>

        <div
            x-data="{
                tab: @js(old('nombre_par') || old('id_cur_par') ? 'paralelo' : (old('nombre_cur') || old('id_niv_cur') ? 'curso' : 'nivel')),
                editNivel: null,
                editCurso: null,
                editParalelo: null
            }"
            class="pb-10"
        >
            @include('admin.institucional.academic._agregar')

            <x-institucional.panel module="academic" title="Estructura del catálogo">
                <div class="p-5">
                    <p class="mb-4 text-xs text-slate-500">{{ $resumen['niveles'] }} nivel(es) · {{ $resumen['cursos'] }} curso(s) · {{ $resumen['paralelos'] }} paralelo(s)</p>

                    @if($arbolCatalogo->isEmpty())
                        <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 px-6 py-16 text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6"/></svg>
                            </div>
                            <p class="mt-4 text-base font-semibold text-slate-700">Comience creando un nivel</p>
                            <p class="mt-2 text-sm text-slate-500">Use el formulario «Agregar elemento» de arriba para registrar el primer nivel.</p>
                        </div>
                    @else
                        @include('admin.institucional.academic._catalogo-cards')
                    @endif

                    @if($arbolOfertas->isNotEmpty())
                        <div class="mt-10">
                            <h3 class="mb-4 text-base font-semibold text-slate-900">Combinaciones en ofertas activas</h3>
                            <div class="space-y-4">
                                @foreach($arbolOfertas as $gestion => $filas)
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                                        <p class="mb-3 text-xs font-bold uppercase tracking-wider text-indigo-600">{{ $gestion }}</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($filas as $fila)
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-sm text-slate-700 ring-1 ring-slate-200">
                                                    <span class="font-medium text-indigo-700">{{ $fila['nivel'] }}</span>
                                                    <span class="text-slate-300">›</span>
                                                    <span>{{ $fila['curso'] }}</span>
                                                    <span class="rounded-md bg-slate-100 px-1.5 py-0.5 text-xs font-bold text-slate-600">{{ $fila['paralelo'] }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </x-institucional.panel>
        </div>
    </x-institucional.page>
@endsection
