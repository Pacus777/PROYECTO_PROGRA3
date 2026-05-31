@extends('layouts.landing')

@section('title', $unidad->nombre_ued.' | AdmisiónEscolar')

@section('content')
    <x-landing.navbar />

    @php
        $portada = $unidad->urlPortada();
        $fotos = $unidad->fotosGaleria();
        $ubicacion = $unidad->municipio
            ? collect([
                $unidad->municipio->nombre_mun ?? null,
                $unidad->municipio->provincia->nombre_prov ?? null,
                $unidad->municipio->provincia->departamento->nombre_dep ?? null,
            ])->filter()->implode(' · ')
            : null;
    @endphp

    {{-- Portada --}}
    <section class="relative border-b border-slate-100">
        <div class="relative h-48 sm:h-64 md:h-80 overflow-hidden bg-gradient-to-br from-blue-700 to-cyan-600">
            @if($portada)
                <img src="{{ $portada }}" alt="{{ $unidad->nombre_ued }}" class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/30 to-slate-900/10"></div>
            @endif
            <div class="absolute inset-0 max-w-7xl mx-auto px-6 lg:px-8 flex flex-col justify-end pb-8">
                <nav class="mb-4 text-sm text-white/80 flex flex-wrap items-center gap-2">
                    <a href="{{ route('home') }}" class="hover:text-white hover:underline">Inicio</a>
                    <span>/</span>
                    <a href="{{ route('colegios.index') }}" class="hover:text-white hover:underline">Colegios</a>
                    <span>/</span>
                    <span class="text-white font-medium">{{ $unidad->nombre_ued }}</span>
                </nav>
                @if($ofertasAbiertas->isNotEmpty())
                    <span class="mb-3 inline-flex w-fit items-center gap-1.5 rounded-full bg-emerald-500/90 px-3 py-1 text-xs font-bold text-white">
                        <span class="h-2 w-2 rounded-full bg-white animate-pulse"></span>
                        {{ $ofertasAbiertas->count() }} {{ $ofertasAbiertas->count() === 1 ? 'convocatoria abierta' : 'convocatorias abiertas' }}
                    </span>
                @endif
                <h1 class="text-3xl md:text-4xl font-extrabold text-white drop-shadow-sm">{{ $unidad->nombre_ued }}</h1>
                @if($unidad->codigo_ued)
                    <p class="mt-2 text-sm font-mono text-white/80">Código UE: {{ $unidad->codigo_ued }}</p>
                @endif
            </div>
        </div>
    </section>

    <section class="bg-slate-50/50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-10">
            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-8">
                    {{-- Información general --}}
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-bold text-slate-900">Sobre el colegio</h2>
                        @if($unidad->descripcion_ued)
                            <p class="mt-3 text-slate-600 leading-relaxed">{{ $unidad->descripcion_ued }}</p>
                        @else
                            <p class="mt-3 text-slate-500 text-sm">Este colegio aún no publicó una descripción detallada.</p>
                        @endif

                        <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                            @if($unidad->niveles_ued)
                                <div class="rounded-xl bg-blue-50/60 px-4 py-3">
                                    <dt class="text-[10px] font-bold uppercase tracking-wide text-blue-600">Niveles</dt>
                                    <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $unidad->niveles_ued }}</dd>
                                </div>
                            @endif
                            @if($unidad->turno_ued)
                                <div class="rounded-xl bg-amber-50/60 px-4 py-3">
                                    <dt class="text-[10px] font-bold uppercase tracking-wide text-amber-700">Turno</dt>
                                    <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $unidad->turno_ued }}</dd>
                                </div>
                            @endif
                            @if($unidad->telefono_ued)
                                <div class="rounded-xl bg-slate-50 px-4 py-3">
                                    <dt class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Teléfono</dt>
                                    <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $unidad->telefono_ued }}</dd>
                                </div>
                            @endif
                            @if($unidad->correo_ued)
                                <div class="rounded-xl bg-slate-50 px-4 py-3">
                                    <dt class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Correo</dt>
                                    <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $unidad->correo_ued }}</dd>
                                </div>
                            @endif
                            @if($ubicacion)
                                <div class="rounded-xl bg-slate-50 px-4 py-3 sm:col-span-2">
                                    <dt class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Ubicación administrativa</dt>
                                    <dd class="mt-1 text-sm text-slate-800">
                                        {{ $ubicacion }}
                                        @if($unidad->distritoEducativo?->nombre_dis)
                                            · Distrito {{ $unidad->distritoEducativo->nombre_dis }}
                                        @endif
                                    </dd>
                                </div>
                            @endif
                            @if($unidad->direccion_ued)
                                <div class="rounded-xl bg-slate-50 px-4 py-3 sm:col-span-2">
                                    <dt class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Dirección</dt>
                                    <dd class="mt-1 text-sm text-slate-800">{{ $unidad->direccion_ued }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    @if($fotos !== [])
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 mb-4">Galería</h2>
                            <x-public.colegio-galeria :fotos="$fotos" :nombre="$unidad->nombre_ued" />
                        </div>
                    @endif
                </div>

                <aside class="space-y-6">
                    <div class="rounded-3xl border border-blue-100 bg-white p-6 shadow-xl shadow-blue-100/40 sticky top-24">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">¿Listo para postular?</h2>

                        @if($ofertasAbiertas->isEmpty())
                            <p class="mt-3 text-sm text-slate-600">
                                No hay convocatorias abiertas en este momento. Vuelva más adelante o consulte las próximas fechas.
                            </p>
                        @elseif($esTutor)
                            <p class="mt-3 text-sm text-slate-600">
                                Tiene sesión activa. Registre una postulación para alguna oferta abierta de este colegio.
                            </p>
                            <a href="{{ route('tutor.postulaciones.create', ['colegio' => $unidad->codigo_ued]) }}"
                               class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 px-5 py-3 text-sm font-semibold text-white shadow-md hover:opacity-95 transition">
                                Iniciar postulación →
                            </a>
                        @else
                            <ol class="mt-4 space-y-2 text-xs text-slate-500">
                                <li class="flex gap-2"><span class="font-bold text-blue-600">1.</span> Regístrese como tutor</li>
                                <li class="flex gap-2"><span class="font-bold text-blue-600">2.</span> Vincule el RUDE de su hijo</li>
                                <li class="flex gap-2"><span class="font-bold text-blue-600">3.</span> Complete la postulación</li>
                            </ol>
                            <div class="mt-5 space-y-3">
                                <button type="button" onclick="window.abrirRegistroTutor()"
                                        class="inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 px-5 py-3 text-sm font-semibold text-white shadow-md hover:opacity-95 transition">
                                    Registrarme y postular
                                </button>
                                <a href="{{ route('login.show', ['redirect' => route('tutor.postulaciones.create', ['colegio' => $unidad->codigo_ued])]) }}"
                                   class="inline-flex w-full items-center justify-center rounded-xl border border-blue-200 bg-blue-50 px-5 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-100 transition">
                                    Ya tengo cuenta — iniciar sesión
                                </a>
                            </div>
                        @endif
                    </div>
                </aside>
            </div>
        </div>
    </section>

    @if(!$esTutor && $ofertasAbiertas->isNotEmpty())
        @php
            $registroStep = 1;
            if ($errors->hasAny(['correo_usu', 'password_usu'])) {
                $registroStep = 2;
            }
            if ($errors->hasAny(['rudes', 'rudes.0', 'rudes.1', 'rudes.2', 'rudes.3'])) {
                $registroStep = 3;
            }
        @endphp
        <x-landing.tutor-registro-modal
            :colegio-nombre="$unidad->nombre_ued"
            :auto-open="$errors->any()"
            :initial-step="$registroStep"
        />
    @endif

    <section class="py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 grid lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 space-y-10">
                @if($ofertasAbiertas->isNotEmpty())
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Convocatorias abiertas</h2>
                        <p class="mt-2 text-slate-600 text-sm">Puede postular a estas ofertas mientras estén dentro del periodo indicado.</p>
                        <div class="mt-6 space-y-4">
                            @foreach($ofertasAbiertas as $oferta)
                                @include('public.colegios._oferta-card', ['oferta' => $oferta, 'unidad' => $unidad, 'esTutor' => $esTutor])
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($ofertasProximas->isNotEmpty())
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Próximas convocatorias</h2>
                        <div class="mt-4 space-y-4">
                            @foreach($ofertasProximas as $oferta)
                                @include('public.colegios._oferta-card', ['oferta' => $oferta, 'unidad' => $unidad, 'esTutor' => $esTutor, 'estado' => 'proxima'])
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($ofertasCerradas->isNotEmpty())
                    <details class="group">
                        <summary class="cursor-pointer text-lg font-semibold text-slate-700 hover:text-blue-700">
                            Convocatorias cerradas ({{ $ofertasCerradas->count() }})
                        </summary>
                        <div class="mt-4 space-y-4 opacity-80">
                            @foreach($ofertasCerradas as $oferta)
                                @include('public.colegios._oferta-card', ['oferta' => $oferta, 'unidad' => $unidad, 'esTutor' => $esTutor, 'estado' => 'cerrada'])
                            @endforeach
                        </div>
                    </details>
                @endif

                @if($ofertasAbiertas->isEmpty() && $ofertasProximas->isEmpty() && $ofertasCerradas->isEmpty())
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-10 text-center text-slate-500">
                        <p class="font-medium">Este colegio aún no publicó ofertas académicas.</p>
                    </div>
                @endif
            </div>

            <aside class="space-y-6">
                @if($unidad->lat_ued && $unidad->lng_ued)
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wide text-slate-400 mb-4">Ubicación en mapa</h3>
                        <x-public.colegio-mapa
                            :lat="$unidad->lat_ued"
                            :lng="$unidad->lng_ued"
                            :nombre="$unidad->nombre_ued"
                            :direccion="$unidad->direccion_ued"
                        />
                    </div>
                @elseif($unidad->direccion_ued)
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="text-xs font-bold uppercase tracking-wide text-slate-400 mb-2">Dirección</h3>
                        <p class="text-sm text-slate-700">{{ $unidad->direccion_ued }}</p>
                    </div>
                @endif

                <div class="rounded-3xl border border-teal-100 bg-gradient-to-br from-teal-50 to-emerald-50/50 p-5">
                    <h3 class="font-semibold text-teal-900">Pasos para postular</h3>
                    <ol class="mt-3 space-y-3 text-sm text-teal-900/80">
                        @foreach([
                            'Regístrese como tutor (CI + RUDE del estudiante).',
                            'Indique el domicilio en el mapa.',
                            'Seleccione la oferta académica.',
                            'El colegio evalúa su postulación según los criterios publicados.',
                        ] as $i => $paso)
                            <li class="flex gap-3">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-teal-600 text-[11px] font-bold text-white">{{ $i + 1 }}</span>
                                <span>{{ $paso }}</span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </aside>
        </div>
    </section>

    <x-landing.footer />
@endsection
