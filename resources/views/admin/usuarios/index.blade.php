@extends('layouts.dashboard')

@section('title', 'Usuarios | Administración')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Usuarios</span>
@endsection

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs text-slate-400">Panel / Usuarios</p>
            <h1 class="text-2xl font-bold text-slate-900">Cuentas de acceso</h1>
            <p class="mt-1 text-sm text-slate-500">Solo existen <strong>tres roles</strong> con inicio de sesión. Los postulantes (estudiantes) no tienen cuenta: se identifican con el <strong>RUDE</strong> y los gestiona su tutor.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-admin.export-report route="admin.usuarios.export" />
            <button type="button"
                    onclick="window.abrirModal('usuario-create')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:from-indigo-700 hover:to-purple-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nuevo usuario
            </button>
        </div>
    </div>

    <x-admin.roles-legend />

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Persona</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Correo</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Tipo de cuenta</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Unidad (UE)</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Estado</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-400">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                        @php
                            $per = $usuario->persona;
                            $nombre = trim(($per->nombres_per ?? '').' '.($per->ap_paterno_per ?? '').' '.($per->ap_materno_per ?? ''));
                            $inicial = strtoupper(mb_substr($nombre ?: $usuario->correo_usu, 0, 1));
                        @endphp
                        <tr class="border-b border-slate-50 transition hover:bg-indigo-50/30 last:border-0">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-indigo-400 to-purple-400 text-xs font-bold text-white">{{ $inicial }}</div>
                                    <p class="font-medium text-slate-800">{{ $nombre ?: '—' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $usuario->correo_usu }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ \App\Support\Roles::label($usuario->rol->nombre_rol ?? null) }}</td>
                            <td class="px-6 py-4 text-slate-600">
                                @if($usuario->unidadEducativa)
                                    <span class="block text-xs font-medium">{{ $usuario->unidadEducativa->nombre_ued }}</span>
                                    @if($usuario->unidadEducativa->codigo_ued)
                                        <span class="font-mono text-[10px] text-indigo-600">{{ $usuario->unidadEducativa->codigo_ued }}</span>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($usuario->activo_usu)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700"><span class="h-1.5 w-1.5 rounded-full bg-current"></span>Activo</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600"><span class="h-1.5 w-1.5 rounded-full bg-current"></span>Inactivo</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.usuarios.show', $usuario) }}" class="mr-1 inline-flex rounded-lg bg-slate-100 p-2 text-slate-500 transition hover:bg-indigo-100 hover:text-indigo-600" title="Ver">Ver</a>
                                <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="mr-1 inline-flex rounded-lg bg-slate-100 p-2 text-slate-500 transition hover:bg-indigo-100 hover:text-indigo-600" title="Editar">Editar</a>
                                <form method="POST" action="{{ route('admin.usuarios.toggle-activo', $usuario) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="mr-1 inline-flex rounded-lg bg-amber-50 px-2.5 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                                        {{ $usuario->activo_usu ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                                <x-ui.confirm-delete :action="route('admin.usuarios.destroy', $usuario)" message="Se eliminará la cuenta y los datos asociados." />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-indigo-500">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-1a4 4 0 00-5-3.87M7 20H2v-1a4 4 0 015-3.87M17 20H7m10 0v-1c0-1.66-1.34-3-3-3h-4c-1.66 0-3 1.34-3 3v1"/></svg>
                                </div>
                                <p class="mt-4 font-semibold text-slate-700">Sin datos aún</p>
                                <p class="mt-2 text-sm text-slate-400">No hay usuarios registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($usuarios->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">{{ $usuarios->links() }}</div>
        @endif
    </div>

    <x-ui.modal name="usuario-create" title="Nuevo usuario" subtitle="Registro por pasos sin salir del listado" max-width="max-w-3xl" :open="$openUsuarioModal ?? false">
        @include('admin.usuarios._wizard-form', [
            'usuario' => null,
            'roles' => $roles,
            'unidades' => $unidades,
            'action' => route('admin.usuarios.store'),
            'submitLabel' => 'Crear usuario',
            'modal' => 'usuario-create',
        ])
    </x-ui.modal>
@endsection
