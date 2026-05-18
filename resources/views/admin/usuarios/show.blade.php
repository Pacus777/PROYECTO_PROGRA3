@extends('layouts.dashboard')

@section('title', 'Usuario | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span>Usuarios</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Detalle</span>
@endsection

@section('content')
    @php
        $per = $usuario->persona;
        $nombre = trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? '').' '.($per->ap_materno_per ?? ''));
    @endphp
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="text-xs text-slate-400">Panel / Usuarios</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">{{ $nombre ?: 'Usuario' }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $usuario->correo_usu }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.usuarios.index') }}" class="inline-flex items-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Volver</a>
            <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 transition hover:bg-slate-50">Editar</a>
            <form method="POST" action="{{ route('admin.usuarios.toggle-activo', $usuario) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="inline-flex items-center rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-semibold text-amber-900 transition hover:bg-amber-100">
                    {{ $usuario->activo_usu ? 'Desactivar' : 'Activar' }}
                </button>
            </form>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-xs font-bold uppercase tracking-wide text-slate-400">Cuenta</h2>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-slate-500">Tipo de cuenta</dt>
                    <dd class="font-semibold text-slate-900">{{ \App\Support\Roles::label($usuario->rol->nombre_rol ?? null) }}</dd>
                    <dd class="mt-1 text-xs text-slate-500">{{ \App\Support\Roles::description($usuario->rol->nombre_rol ?? null) }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Unidad educativa</dt>
                    <dd class="font-semibold text-slate-900">
                        @if($usuario->unidadEducativa)
                            {{ $usuario->unidadEducativa->nombre_ued }}
                            @if($usuario->unidadEducativa->codigo_ued)
                                <span class="mt-0.5 block font-mono text-xs font-normal text-indigo-600">Código UE: {{ $usuario->unidadEducativa->codigo_ued }}</span>
                            @endif
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div><dt class="text-slate-500">Estado</dt><dd class="font-semibold text-slate-900">{{ $usuario->activo_usu ? 'Activo' : 'Inactivo' }}</dd></div>
                @if($isTutorRole)
                    <div>
                        <dt class="text-slate-500">Perfil tutor</dt>
                        <dd class="font-semibold {{ $hasTutorProfile ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $hasTutorProfile ? 'Vinculado correctamente' : 'Falta registro en tabla tutor' }}
                        </dd>
                    </div>
                    @if($hasTutorProfile)
                        <div>
                            <dt class="text-slate-500">Estudiantes</dt>
                            <dd>
                                <a href="{{ route('admin.tutores.estudiantes.index', $tutorProfile) }}"
                                   class="inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 transition hover:text-indigo-800">
                                    Gestionar estudiantes vinculados
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </dd>
                        </div>
                    @endif
                @endif
            </dl>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-xs font-bold uppercase tracking-wide text-slate-400">Persona</h2>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-slate-500">CI</dt><dd class="font-semibold text-slate-900">{{ $per->ci_per ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Nacimiento</dt><dd class="font-semibold text-slate-900">{{ optional($per->fecha_nac_per)->format('d/m/Y') ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Teléfono</dt><dd class="font-semibold text-slate-900">{{ $per->telefono_per ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Correo persona</dt><dd class="font-semibold text-slate-900">{{ $per->correo_per ?? '—' }}</dd></div>
            </dl>
        </div>
    </div>

    @if($isTutorRole && ! $hasTutorProfile)
        <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
            <p class="font-semibold">Integridad pendiente para rol tutor.</p>
            <p class="mt-1 text-sm text-amber-800">Este usuario tiene rol tutor, pero no existe su fila vinculada en la tabla tutor.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}" class="mt-8" onsubmit="return confirm('¿Eliminar definitivamente este usuario?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-xl bg-red-50 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-red-100">Eliminar usuario</button>
    </form>
@endsection
