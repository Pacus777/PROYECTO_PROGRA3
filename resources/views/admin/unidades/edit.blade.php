@extends('layouts.dashboard')

@section('title', 'Editar unidad | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span>Unidades educativas</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Editar</span>
@endsection

@section('content')
    <div class="mb-8">
        <p class="text-xs text-slate-400">Panel / Unidades educativas</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900">Editar unidad educativa</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $unidad->nombre_ued }}</p>
    </div>

    <form method="POST" action="{{ route('admin.unidades.update', $unidad) }}" class="max-w-2xl rounded-2xl bg-white p-6 shadow-sm md:p-8">
        @csrf
        @method('PUT')
        <div class="space-y-5">
            <div>
                <label for="nombre_ued" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Nombre</label>
                <input type="text" name="nombre_ued" id="nombre_ued" value="{{ old('nombre_ued', $unidad->nombre_ued) }}" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                @error('nombre_ued')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="codigo_ued" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Código de la unidad educativa (UE)</label>
                <input type="text" name="codigo_ued" id="codigo_ued" value="{{ old('codigo_ued', $unidad->codigo_ued) }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                @error('codigo_ued')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                <p class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">Ubicación territorial</p>
                <x-admin.filtro-territorio
                    :departamentos="$departamentos"
                    mode="form"
                    :show-unidad="false"
                    :selected="$territorioSeleccionado"
                />
                @error('id_mun_ued')<p class="mt-2 text-xs text-rose-600">{{ $message }}</p>@enderror
                @error('id_dis_ued')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <x-admin.address-location-picker
                :address="old('direccion_ued', $unidad->direccion_ued)"
                :lat="old('lat_ued', $unidad->lat_ued)"
                :lng="old('lng_ued', $unidad->lng_ued)"
            />

            <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-4 space-y-4">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Perfil público (catálogo de colegios)</p>
                <div>
                    <label for="descripcion_ued" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Descripción</label>
                    <textarea name="descripcion_ued" id="descripcion_ued" rows="4"
                              class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('descripcion_ued', $unidad->descripcion_ued) }}</textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="telefono_ued" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Teléfono</label>
                        <input type="text" name="telefono_ued" id="telefono_ued" value="{{ old('telefono_ued', $unidad->telefono_ued) }}"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label for="correo_ued" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Correo</label>
                        <input type="email" name="correo_ued" id="correo_ued" value="{{ old('correo_ued', $unidad->correo_ued) }}"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label for="turno_ued" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Turno</label>
                        <input type="text" name="turno_ued" id="turno_ued" value="{{ old('turno_ued', $unidad->turno_ued) }}" placeholder="Ej: Mañana y Tarde"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label for="niveles_ued" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Niveles</label>
                        <input type="text" name="niveles_ued" id="niveles_ued" value="{{ old('niveles_ued', $unidad->niveles_ued) }}" placeholder="Ej: Inicial, Primaria, Secundaria"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm">
                    </div>
                </div>
                <div>
                    <label for="imagen_portada_ued" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Imagen de portada (URL)</label>
                    <input type="url" name="imagen_portada_ued" id="imagen_portada_ued" value="{{ old('imagen_portada_ued', $unidad->imagen_portada_ued) }}"
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm" placeholder="https://...">
                </div>
                <div>
                    <label for="galeria_ued_text" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Galería de fotos (una URL por línea)</label>
                    <textarea name="galeria_ued_text" id="galeria_ued_text" rows="4"
                              class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-mono text-xs"
                              placeholder="https://ejemplo.com/foto1.jpg">{{ old('galeria_ued_text', implode("\n", $unidad->fotosGaleria())) }}</textarea>
                </div>
            </div>
        </div>
        <div class="mt-8 flex flex-wrap gap-3">
            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:from-indigo-700 hover:to-purple-700">Actualizar</button>
            <a href="{{ route('admin.unidades.show', $unidad) }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancelar</a>
        </div>
    </form>
@endsection
