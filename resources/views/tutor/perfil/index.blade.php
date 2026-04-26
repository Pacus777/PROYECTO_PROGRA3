@extends('layouts.dashboard')

@section('title', 'Tutor | Perfil')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Perfil</span>
@endsection

@section('content')
    <div class="mb-6">
        <p class="text-xs text-slate-400">Tutor / Perfil</p>
        <h1 class="text-2xl font-bold text-slate-900">Mi perfil</h1>
    </div>

    @if($usuario === null)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-amber-900">
            <p>No se pudo cargar el usuario de sesión.</p>
        </div>
    @else
        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-2xl bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-slate-800">Usuario</h2>
                <dl class="space-y-2 text-sm text-slate-700">
                    <div><dt class="font-semibold text-slate-500">Correo</dt><dd>{{ $usuario->correo_usu }}</dd></div>
                    <div><dt class="font-semibold text-slate-500">Rol</dt><dd class="capitalize">{{ str_replace('_', ' ', $usuario->rol->nombre_rol ?? '—') }}</dd></div>
                    <div><dt class="font-semibold text-slate-500">Activo</dt><dd>{{ $usuario->activo_usu ? 'Sí' : 'No' }}</dd></div>
                </dl>
            </section>
            <section class="rounded-2xl bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-slate-800">Persona</h2>
                @if($usuario->persona)
                    @php $p = $usuario->persona; @endphp
                    <dl class="space-y-2 text-sm text-slate-700">
                        <div><dt class="font-semibold text-slate-500">Nombre completo</dt><dd>{{ trim(($p->nombres_per ?? '').' '.($p->ap_paterno_per ?? '').' '.($p->ap_materno_per ?? '')) ?: '—' }}</dd></div>
                        <div><dt class="font-semibold text-slate-500">CI</dt><dd>{{ $p->ci_per ?? '—' }}</dd></div>
                        <div><dt class="font-semibold text-slate-500">Correo (persona)</dt><dd>{{ $p->correo_per ?? '—' }}</dd></div>
                        <div><dt class="font-semibold text-slate-500">Teléfono</dt><dd>{{ $p->telefono_per ?? '—' }}</dd></div>
                    </dl>
                @else
                    <p class="text-sm text-slate-500">Sin registro de persona vinculado.</p>
                @endif
            </section>
            <section class="rounded-2xl bg-white p-6 shadow-sm lg:col-span-2">
                <h2 class="mb-4 text-lg font-semibold text-slate-800">Perfil tutor</h2>
                @if($tutor === null)
                    <p class="text-sm text-amber-800">Sin registro en tabla tutor vinculado a tu persona.</p>
                @else
                    <p class="text-sm text-slate-700">ID tutor: <strong>{{ $tutor->id_tut }}</strong> · Estudiantes vinculados: <strong>{{ $tutor->estudiantes->count() }}</strong></p>
                    <a href="{{ route('tutor.estudiantes.index') }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:underline">Ver estudiantes →</a>
                @endif
            </section>
        </div>
    @endif
@endsection
