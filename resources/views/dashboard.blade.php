@extends('layouts.dashboard')

@section('title', 'Dashboard | Sistema de Admisión Escolar')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">Inicio</span>
@endsection

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 p-8 text-white">
            <div class="absolute -right-14 -top-14 h-48 w-48 rounded-full bg-white/10"></div>
            <p class="text-xs uppercase tracking-[0.2em] text-white/70">Panel principal</p>
            <h1 class="mt-2 text-3xl font-bold">Bienvenido al sistema de admisión</h1>
            <p class="mt-2 text-sm text-white/80">Gestiona todo el flujo académico y administrativo desde un solo lugar.</p>
        </div>

        <section class="rounded-2xl bg-white p-8 shadow-sm">
            <h2 class="text-xl font-semibold text-slate-900">Resumen rápido</h2>
            <p class="mt-2 text-slate-600 leading-relaxed">
                Bienvenido al panel principal del sistema de admisión escolar.
            </p>

            @if(($usuario->rol->nombre_rol ?? '') === \App\Support\Roles::ADMIN_GENERAL)
                <div class="mt-6">
                    <a href="{{ route('admin.usuarios.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 px-5 py-3 text-sm font-bold text-white shadow-md shadow-blue-200/50 hover:opacity-95 transition">
                        Ir al panel de administración general
                    </a>
                </div>
            @endif

            @if(($usuario->rol->nombre_rol ?? '') === \App\Support\Roles::ADMIN_INSTITUCIONAL)
                <div class="mt-6">
                    <a href="{{ route('admin.institucional.dashboard') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 px-5 py-3 text-sm font-bold text-white shadow-md shadow-blue-200/50 hover:opacity-95 transition">
                        Ir al panel institucional
                    </a>
                </div>
            @endif

            @if(!empty($tutorDashboard['active']))
                @php
                    $td = $tutorDashboard;
                    $stats = $td['stats'] ?? [];
                    $totalPos = (int) ($stats['total_postulaciones'] ?? 0);
                    $nombreTutor = trim(($usuario->persona->nombres_per ?? '').' '.($usuario->persona->ap_paterno_per ?? '')) ?: 'Tutor';
                @endphp

                <div class="mt-10 space-y-8">
                    <div class="rounded-2xl border border-teal-100 bg-gradient-to-r from-teal-600 via-emerald-600 to-cyan-600 p-6 text-white shadow-md">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">Vista tutor</p>
                        <h3 class="mt-2 text-xl font-bold">Hola, {{ $nombreTutor }}</h3>
                        <p class="mt-2 text-sm text-white/85">
                            Resumen de tus estudiantes vinculados y sus postulaciones (misma información que usa el administrador institucional, filtrada por tus tutelados).
                        </p>
                    </div>

                    @if(!empty($td['warning']))
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-amber-900">
                            @if($td['warning'] === 'sin_registro_tutor')
                                <p class="font-semibold">No hay perfil de tutor asociado a tu usuario.</p>
                                <p class="mt-2 text-sm text-amber-800">Tu cuenta tiene rol <strong>tutor</strong>, pero falta el registro en la tabla <code class="rounded bg-amber-100 px-1">tutor</code> ligado a tu persona. Pide a un administrador que complete el vínculo.</p>
                            @elseif($td['warning'] === 'sin_estudiantes')
                                <p class="font-semibold">Aún no tienes estudiantes vinculados.</p>
                                <p class="mt-2 text-sm text-amber-800">Cuando se registre la relación tutor–estudiante en el sistema, aquí verás métricas y postulaciones.</p>
                            @endif
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            <article class="rounded-2xl bg-gradient-to-br from-teal-500 to-teal-700 p-5 text-white shadow-lg">
                                <p class="text-xs font-medium uppercase tracking-wide text-teal-100">Estudiantes</p>
                                <p class="mt-2 text-3xl font-black">{{ (int) ($stats['total_estudiantes'] ?? 0) }}</p>
                                <p class="mt-1 text-sm text-teal-100">Vinculados a tu perfil</p>
                            </article>
                            <article class="rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600 p-5 text-white shadow-lg">
                                <p class="text-xs font-medium uppercase tracking-wide text-cyan-100">Postulaciones</p>
                                <p class="mt-2 text-3xl font-black">{{ $totalPos }}</p>
                                <p class="mt-1 text-sm text-cyan-100">Total de tus tutelados</p>
                            </article>
                            <article class="rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 p-5 text-white shadow-lg">
                                <p class="text-xs font-medium uppercase tracking-wide text-violet-100">En evaluación</p>
                                <p class="mt-2 text-3xl font-black">{{ (int) ($stats['en_evaluacion'] ?? 0) }}</p>
                                <p class="mt-1 text-sm text-violet-100">Estado <span class="font-semibold">en_evaluacion</span></p>
                            </article>
                            <article class="rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 p-5 text-white shadow-lg">
                                <p class="text-xs font-medium uppercase tracking-wide text-emerald-100">Aprobadas</p>
                                <p class="mt-2 text-3xl font-black">{{ (int) ($stats['aprobadas'] ?? 0) }}</p>
                                <p class="mt-1 text-sm text-emerald-100">Según <span class="font-semibold">estado_postulacion</span></p>
                            </article>
                        </div>

                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                            <div class="rounded-2xl bg-white p-6 shadow-sm lg:col-span-2">
                                <h3 class="text-lg font-semibold text-slate-800">Postulaciones por estado</h3>
                                <p class="mt-1 text-sm text-slate-500">Conteos reales alineados con el módulo institucional de postulaciones.</p>
                                @php $porEstado = $stats['por_estado'] ?? collect(); @endphp
                                @if($porEstado->isEmpty())
                                    <p class="mt-6 text-center text-sm text-slate-500">Tus estudiantes aún no tienen postulaciones registradas.</p>
                                @else
                                    <ul class="mt-6 space-y-4">
                                        @foreach($porEstado as $fila)
                                            @php
                                                $n = (int) ($fila['total'] ?? 0);
                                                $pct = $totalPos > 0 ? round($n * 100 / $totalPos) : 0;
                                            @endphp
                                            <li>
                                                <div class="mb-1 flex justify-between text-xs font-medium text-slate-600">
                                                    <span>{{ $fila['nombre'] ?? '—' }}</span>
                                                    <span>{{ $n }} ({{ $pct }}%)</span>
                                                </div>
                                                <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
                                                    <div class="h-full rounded-full bg-gradient-to-r from-teal-500 to-cyan-400" style="width: {{ $pct }}%"></div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <div class="rounded-2xl bg-white p-6 shadow-sm">
                                <h3 class="text-lg font-semibold text-slate-800">Seguimiento</h3>
                                <ul class="mt-4 space-y-3 text-sm text-slate-700">
                                    <li class="flex justify-between gap-2 border-b border-slate-100 pb-2">
                                        <span>Enviadas</span>
                                        <strong>{{ (int) ($stats['enviadas'] ?? 0) }}</strong>
                                    </li>
                                    <li class="flex justify-between gap-2 border-b border-slate-100 pb-2">
                                        <span>Rechazadas</span>
                                        <strong>{{ (int) ($stats['rechazadas'] ?? 0) }}</strong>
                                    </li>
                                    <li class="flex justify-between gap-2 border-b border-slate-100 pb-2">
                                        <span>Con resultado (ranking)</span>
                                        <strong>{{ (int) ($stats['con_resultado'] ?? 0) }}</strong>
                                    </li>
                                    <li class="flex justify-between gap-2">
                                        <span>En lista de espera</span>
                                        <strong>{{ (int) ($stats['en_lista_espera'] ?? 0) }}</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-slate-800">Tus estudiantes</h3>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach($td['estudiantes'] as $est)
                                    @php
                                        $nom = trim(($est->persona->nombres_per ?? '').' '.($est->persona->ap_paterno_per ?? '').' '.($est->persona->ap_materno_per ?? ''));
                                    @endphp
                                    <span class="inline-flex items-center rounded-full bg-teal-50 px-3 py-1 text-xs font-medium text-teal-800 ring-1 ring-inset ring-teal-100">
                                        {{ $nom ?: ('ID '.$est->id_est) }}
                                        @if($est->codigo_est)
                                            <span class="ml-1 text-teal-600">· {{ $est->codigo_est }}</span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white p-6 shadow-sm">
                            <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <h3 class="text-lg font-semibold text-slate-800">Postulaciones recientes</h3>
                                <p class="text-xs text-slate-500">Orden por fecha de postulación (como en el listado institucional).</p>
                            </div>
                            @php $recent = $td['recent'] ?? collect(); @endphp
                            @if($recent->isEmpty())
                                <p class="py-8 text-center text-sm text-slate-500">Sin postulaciones para mostrar.</p>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-left text-sm">
                                        <thead>
                                            <tr class="border-b border-slate-200 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                <th class="py-2 pr-4">Estudiante</th>
                                                <th class="py-2 pr-4">Oferta</th>
                                                <th class="py-2 pr-4">Unidad</th>
                                                <th class="py-2 pr-4">Estado</th>
                                                <th class="py-2">Fecha</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @foreach($recent as $pos)
                                                @php
                                                    $oac = $pos->ofertaAcademica;
                                                    $estNom = trim(($pos->estudiante->persona->nombres_per ?? '').' '.($pos->estudiante->persona->ap_paterno_per ?? ''));
                                                    $ofertaTxt = $oac
                                                        ? trim(implode(' · ', array_filter([
                                                            $oac->gestion->nombre_ges ?? null,
                                                            $oac->nivel->nombre_niv ?? null,
                                                            $oac->curso->nombre_cur ?? null,
                                                            $oac->paralelo->nombre_par ?? null,
                                                        ])))
                                                        : '';
                                                @endphp
                                                <tr class="text-slate-700">
                                                    <td class="py-3 pr-4 font-medium text-slate-900">{{ $estNom ?: '—' }}</td>
                                                    <td class="py-3 pr-4">{{ $ofertaTxt ?: ($oac?->descripcion_oac ?? '—') }}</td>
                                                    <td class="py-3 pr-4">{{ $oac?->unidadEducativa?->nombre_ued ?? '—' }}</td>
                                                    <td class="py-3 pr-4">
                                                        <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">{{ $pos->estadoPostulacion->nombre_ept ?? '—' }}</span>
                                                    </td>
                                                    <td class="py-3 whitespace-nowrap text-slate-600">{{ optional($pos->fecha_pos)->format('d/m/Y H:i') ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <p class="text-center text-xs text-slate-500">
                            Para crear o editar postulaciones por API usa el prefijo <code class="rounded bg-slate-100 px-1">/api/v1/postulaciones</code> con token Sanctum (mismos permisos que ya tiene el rol tutor en la API).
                        </p>
                    @endif
                </div>
            @endif

            <div class="mt-8 grid gap-4 sm:grid-cols-3">
                <article class="rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                    <strong class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Usuario</strong>
                    <span class="mt-1 block text-sm font-medium text-slate-900">
                        {{ trim(($usuario->persona->nombres_per ?? '').' '.($usuario->persona->ap_paterno_per ?? '').' '.($usuario->persona->ap_materno_per ?? '')) ?: $usuario->correo_usu }}
                    </span>
                </article>
                <article class="rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                    <strong class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Rol</strong>
                    <span class="mt-1 block text-sm font-medium text-slate-900">{{ $usuario->rol->nombre_rol ?? 'sin rol' }}</span>
                </article>
                <article class="rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                    <strong class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Correo</strong>
                    <span class="mt-1 block text-sm font-medium text-slate-900 truncate">{{ $usuario->correo_usu }}</span>
                </article>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="mt-8">
                @csrf
                <button type="submit" class="rounded-xl bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-100">Cerrar sesión</button>
            </form>
        </section>
    </div>
@endsection
