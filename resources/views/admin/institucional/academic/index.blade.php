@extends('layouts.dashboard')

@section('title', 'Gestión académica | Admin institucional')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Gestión académica</span>
@endsection

@section('content')
    <div class="mb-8">
        <p class="text-xs text-slate-400">Panel / Gestión académica</p>
        <h1 class="text-2xl font-bold text-slate-900">Gestión académica</h1>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="rounded-2xl bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-800">Niveles</h2>
            <form method="POST" action="{{ route('admin.institucional.niveles.store') }}" class="mb-4">
                @csrf
                <input name="nombre_niv" placeholder="Nuevo nivel" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <button class="mt-2 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">Crear nivel</button>
            </form>
            <div class="space-y-2">
                @foreach($niveles as $nivel)
                    <form method="POST" action="{{ route('admin.institucional.niveles.update', $nivel) }}" class="flex gap-2">
                        @csrf @method('PUT')
                        <input name="nombre_niv" value="{{ $nivel->nombre_niv }}" class="flex-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <button class="rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">Guardar</button>
                    </form>
                    <form method="POST" action="{{ route('admin.institucional.niveles.destroy', $nivel) }}" onsubmit="return confirm('¿Eliminar nivel?')">
                        @csrf @method('DELETE')
                        <button class="rounded-lg bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-100">Eliminar</button>
                    </form>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-800">Cursos</h2>
            <form method="POST" action="{{ route('admin.institucional.cursos.store') }}" class="space-y-2 mb-4">
                @csrf
                <select name="id_niv_cur" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    @foreach($niveles as $nivel)<option value="{{ $nivel->id_niv }}">{{ $nivel->nombre_niv }}</option>@endforeach
                </select>
                <input name="nombre_cur" placeholder="Nuevo curso" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <button class="rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">Crear curso</button>
            </form>
            <div class="space-y-2">
                @foreach($cursos as $curso)
                    <form method="POST" action="{{ route('admin.institucional.cursos.update', $curso) }}" class="space-y-2 rounded-lg border border-slate-100 p-2">
                        @csrf @method('PUT')
                        <select name="id_niv_cur" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            @foreach($niveles as $nivel)<option value="{{ $nivel->id_niv }}" @selected($curso->id_niv_cur === $nivel->id_niv)>{{ $nivel->nombre_niv }}</option>@endforeach
                        </select>
                        <input name="nombre_cur" value="{{ $curso->nombre_cur }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <div class="flex justify-between items-center">
                            <button class="rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">Guardar</button>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('admin.institucional.cursos.destroy', $curso) }}" onsubmit="return confirm('¿Eliminar curso?')">
                        @csrf @method('DELETE')
                        <button class="rounded-lg bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-100">Eliminar</button>
                    </form>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-800">Paralelos</h2>
            <form method="POST" action="{{ route('admin.institucional.paralelos.store') }}" class="space-y-2 mb-4">
                @csrf
                <select name="id_cur_par" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    @foreach($cursos as $curso)<option value="{{ $curso->id_cur }}">{{ $curso->nombre_cur }} ({{ $curso->nivel->nombre_niv }})</option>@endforeach
                </select>
                <input name="nombre_par" placeholder="Paralelo (A, B...)" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <button class="rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">Crear paralelo</button>
            </form>
            <div class="space-y-2">
                @foreach($paralelos as $paralelo)
                    <form method="POST" action="{{ route('admin.institucional.paralelos.update', $paralelo) }}" class="space-y-2 rounded-lg border border-slate-100 p-2">
                        @csrf @method('PUT')
                        <select name="id_cur_par" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            @foreach($cursos as $curso)<option value="{{ $curso->id_cur }}" @selected($paralelo->id_cur_par === $curso->id_cur)>{{ $curso->nombre_cur }}</option>@endforeach
                        </select>
                        <input name="nombre_par" value="{{ $paralelo->nombre_par }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <div class="flex justify-between items-center">
                            <button class="rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">Guardar</button>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('admin.institucional.paralelos.destroy', $paralelo) }}" onsubmit="return confirm('¿Eliminar paralelo?')">
                        @csrf @method('DELETE')
                        <button class="rounded-lg bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-100">Eliminar</button>
                    </form>
                @endforeach
            </div>
        </section>
    </div>
@endsection

