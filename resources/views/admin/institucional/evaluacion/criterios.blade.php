@extends('layouts.dashboard')

@section('title', 'Criterios de evaluación | Admin institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Criterios de evaluación</span>
@endsection

@section('content')
    <x-institucional.page module="evaluacion" title="Criterios de evaluación">
        <x-institucional.panel module="evaluacion" title="Nuevo criterio">
            <form method="POST" action="{{ route('admin.institucional.criterios.store') }}" class="grid gap-3 p-5 md:grid-cols-4">
                @csrf
                <select name="id_tic_cri" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">@foreach($tipos as $tipo)<option value="{{ $tipo->id_tic }}">{{ $tipo->nombre_tic }}</option>@endforeach</select>
                <input name="nombre_cri" placeholder="Nombre del criterio" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <input name="peso_cri" type="number" step="0.0001" min="0" max="100" placeholder="Peso" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <button class="rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-sm font-semibold text-white transition hover:from-indigo-700 hover:to-purple-700">Crear criterio</button>
            </form>
        </x-institucional.panel>

        <x-institucional.panel module="evaluacion" title="Criterios registrados">
            <div class="overflow-x-auto" data-inst-table>
                <table class="min-w-full text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Tipo</th><th class="px-4 py-3 text-left">Nombre</th><th class="px-4 py-3 text-left">Peso</th><th class="px-4 py-3 text-right">Acciones</th></tr></thead>
                    <tbody>
                    @foreach($criterios as $criterio)
                        <tr class="border-b border-slate-50 transition hover:bg-indigo-50/30 last:border-0">
                            <td class="px-4 py-3">{{ $criterio->tipoCriterio->nombre_tic ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $criterio->nombre_cri }}</td>
                            <td class="px-4 py-3">{{ $criterio->peso_cri }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('admin.institucional.criterios.update', $criterio) }}" class="inline-flex items-center gap-2">
                                    @csrf @method('PUT')
                                    <select name="id_tic_cri" class="rounded border border-slate-200 bg-slate-50 px-2 py-1 text-xs">@foreach($tipos as $tipo)<option value="{{ $tipo->id_tic }}" @selected($criterio->id_tic_cri === $tipo->id_tic)>{{ $tipo->nombre_tic }}</option>@endforeach</select>
                                    <input name="nombre_cri" value="{{ $criterio->nombre_cri }}" class="rounded border border-slate-200 bg-slate-50 px-2 py-1 text-xs">
                                    <input name="peso_cri" value="{{ $criterio->peso_cri }}" class="w-16 rounded border border-slate-200 bg-slate-50 px-2 py-1 text-xs">
                                    <button class="rounded bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700">Guardar</button>
                                </form>
                                <form method="POST" action="{{ route('admin.institucional.criterios.destroy', $criterio) }}" class="inline" onsubmit="return confirm('¿Eliminar criterio?')">@csrf @method('DELETE')<button class="ml-2 rounded bg-red-50 px-2 py-1 text-xs font-semibold text-rose-600">Eliminar</button></form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 p-4">{{ $criterios->links() }}</div>
        </x-institucional.panel>
    </x-institucional.page>
@endsection
