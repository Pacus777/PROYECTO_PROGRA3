@props([
    'colegioNombre' => null,
    'autoOpen' => false,
    'initialStep' => 1,
])

@php
    $nombreColegio = $colegioNombre ?? session('postular_colegio_nombre');
@endphp

<div
    x-data="tutorRegistroWizard({
        autoOpen: @js($autoOpen),
        initialStep: @js($initialStep),
        rudes: @js(old('rudes', [''])),
    })"
    x-on:open-tutor-registro.window="openModal()"
    @keydown.escape.window="open && closeModal()"
>
    {{-- Modal --}}
    <div
        x-show="open"
        x-cloak
        style="display: none;"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="registro-modal-title"
    >
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal()"></div>

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
            class="relative w-full max-w-lg max-h-[94vh] overflow-hidden rounded-t-3xl sm:rounded-3xl bg-white shadow-2xl flex flex-col"
            @click.stop
        >
            {{-- Header --}}
            <div class="shrink-0 border-b border-slate-100 bg-gradient-to-r from-teal-600 to-emerald-600 px-6 py-5 text-white">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-teal-100">Registro de tutor</p>
                        <h2 id="registro-modal-title" class="mt-1 text-lg font-bold leading-snug">
                            @if($nombreColegio)
                                Postular en {{ $nombreColegio }}
                            @else
                                Crear cuenta de tutor
                            @endif
                        </h2>
                    </div>
                    <button type="button" @click.stop="closeModal()" class="relative z-10 rounded-xl bg-white/15 p-2 hover:bg-white/25 transition" aria-label="Cerrar">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Barra de progreso --}}
                <div class="mt-5">
                    <div class="flex items-center justify-between text-[11px] font-semibold text-teal-100">
                        <span x-text="'Paso ' + step + ' de ' + totalSteps"></span>
                        <span x-text="Math.round(progress) + '%'"></span>
                    </div>
                    <div class="mt-2 h-2 rounded-full bg-white/20 overflow-hidden">
                        <div class="h-full rounded-full bg-white transition-all duration-300 ease-out" :style="'width:' + progress + '%'"></div>
                    </div>
                    <div class="mt-3 flex gap-1">
                        <template x-for="(label, i) in stepLabels" :key="i">
                            <div class="flex-1 text-center">
                                <div class="mx-auto flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold transition-colors"
                                     :class="step > i + 1 ? 'bg-white text-teal-700' : (step === i + 1 ? 'bg-white text-teal-700 ring-2 ring-white/50' : 'bg-white/20 text-white/70')"
                                     x-text="i + 1"></div>
                                <p class="mt-1 hidden sm:block text-[10px] font-medium truncate px-0.5"
                                   :class="step === i + 1 ? 'text-white' : 'text-teal-100/80'"
                                   x-text="label"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="flex-1 overflow-y-auto px-6 py-5">
                @if($errors->any())
                    <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        <p class="font-semibold">Revise los datos:</p>
                        <ul class="mt-2 list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('registro.tutor.store') }}" id="tutor-registro-form" @submit="onSubmit">
                    @csrf

                    {{-- Paso 1: Datos personales --}}
                    <div x-show="step === 1" x-cloak x-ref="step1" class="space-y-4">
                        <p class="text-sm text-slate-600">Ingrese sus datos tal como figuran en su documento de identidad.</p>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nombres *</label>
                            <input type="text" name="nombres_per" value="{{ old('nombres_per') }}" required
                                   class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Ap. paterno *</label>
                                <input type="text" name="ap_paterno_per" value="{{ old('ap_paterno_per') }}" required
                                       class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Ap. materno</label>
                                <input type="text" name="ap_materno_per" value="{{ old('ap_materno_per') }}"
                                       class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Cédula de identidad *</label>
                                <input type="text" name="ci_per" value="{{ old('ci_per') }}" required maxlength="32"
                                       class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                                @error('ci_per')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Teléfono</label>
                                <input type="text" name="telefono_per" value="{{ old('telefono_per') }}"
                                       class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                            </div>
                        </div>
                    </div>

                    {{-- Paso 2: Acceso --}}
                    <div x-show="step === 2" x-cloak x-ref="step2" class="space-y-4">
                        <p class="text-sm text-slate-600">Estos datos le permitirán ingresar al panel de postulaciones.</p>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Correo electrónico *</label>
                            <input type="email" name="correo_usu" value="{{ old('correo_usu') }}" required autocomplete="email"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                            @error('correo_usu')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Contraseña *</label>
                            <input type="password" name="password_usu" required minlength="8" autocomplete="new-password"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                            <p class="mt-1 text-xs text-slate-400">Mínimo 8 caracteres.</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Confirmar contraseña *</label>
                            <input type="password" name="password_usu_confirmation" required autocomplete="new-password"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                        </div>
                    </div>

                    {{-- Paso 3: RUDE --}}
                    <div x-show="step === 3" x-cloak x-ref="step3" class="space-y-4">
                        <p class="text-sm text-slate-600">
                            Vincule a sus hijos con el <strong>RUDE</strong> (Registro Único de Estudiantes).
                            Los estudiantes no necesitan cuenta propia.
                        </p>
                        <div class="flex items-center justify-between gap-2">
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">RUDE de hijos / hijas *</label>
                            <button type="button" @click="addRude()" class="text-xs font-semibold text-teal-700 hover:text-teal-900">+ Agregar otro</button>
                        </div>
                        <template x-for="(rude, index) in rudes" :key="index">
                            <div class="flex gap-2">
                                <input type="text"
                                       :name="'rudes[' + index + ']'"
                                       x-model="rudes[index]"
                                       inputmode="numeric"
                                       maxlength="12"
                                       placeholder="Ej: 40850052015265"
                                       required
                                       class="flex-1 rounded-xl border border-slate-200 px-4 py-3 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-teal-300">
                                <button type="button" @click="removeRude(index)" x-show="rudes.length > 1"
                                        class="rounded-xl border border-slate-200 px-3 text-slate-500 hover:bg-rose-50 hover:text-rose-600">×</button>
                            </div>
                        </template>
                        <p class="text-xs text-slate-500">8 a 12 dígitos. El estudiante debe estar registrado previamente en su unidad educativa.</p>

                        <div class="rounded-xl border border-teal-100 bg-teal-50/60 px-4 py-3 text-xs text-teal-900">
                            <p class="font-semibold">Al finalizar</p>
                            <p class="mt-1">
                                Se creará su cuenta, registrará el domicilio del estudiante en el mapa y podrá continuar con la postulación
                                @if($nombreColegio)
                                    en <strong>{{ $nombreColegio }}</strong>
                                @endif.
                            </p>
                        </div>
                    </div>
                </form>

                <p class="mt-4 text-center text-xs text-slate-500">
                    ¿Ya tiene cuenta?
                    <a href="{{ route('login.show') }}" class="font-semibold text-blue-600 hover:underline">Inicie sesión</a>
                </p>
            </div>

            {{-- Footer acciones --}}
            <div class="shrink-0 border-t border-slate-100 bg-slate-50 px-6 py-4 flex gap-3">
                <button type="button" x-show="step > 1" @click="prevStep()"
                        class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">
                    ← Anterior
                </button>
                <button type="button" x-show="step < totalSteps" @click="nextStep()"
                        class="flex-1 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 px-4 py-3 text-sm font-bold text-white shadow-md hover:from-teal-700 hover:to-emerald-700 transition">
                    Siguiente →
                </button>
                <button type="submit" form="tutor-registro-form" x-show="step === totalSteps"
                        class="flex-1 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 px-4 py-3 text-sm font-bold text-white shadow-md hover:from-teal-700 hover:to-emerald-700 transition">
                    Crear cuenta →
                </button>
            </div>
        </div>
    </div>
</div>
