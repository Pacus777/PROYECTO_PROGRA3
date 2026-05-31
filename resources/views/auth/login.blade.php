@extends('layouts.landing')

@section('title', 'Iniciar sesión | AdmisiónEscolar')

@section('content')
    <style>
        /* Animaciones principales de entrada */
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-60px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }
        @keyframes badgeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .anim-left { animation: slideInLeft 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .anim-right { animation: fadeInUp 0.7s 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
        .anim-float { animation: float 6s ease-in-out infinite; }
        .anim-shake { animation: shake 0.36s ease-in-out; }
        .badge-1 { animation: badgeIn 0.5s ease-out 0.2s both; }
        .badge-2 { animation: badgeIn 0.5s ease-out 0.35s both; }
        .badge-3 { animation: badgeIn 0.5s ease-out 0.5s both; }
    </style>

    <section class="min-h-screen bg-gradient-to-br from-blue-950 via-blue-900 to-slate-900">
        <div class="h-2 bg-gradient-to-r from-blue-600 to-cyan-500 lg:hidden"></div>

        <div class="min-h-[calc(100vh-0.5rem)] lg:min-h-screen lg:grid lg:grid-cols-[45%_55%]">
            {{-- Panel visual izquierdo --}}
            <aside class="hidden lg:flex relative overflow-hidden rounded-r-[3rem] bg-gradient-to-br from-blue-600 via-blue-500 to-cyan-400 p-12 anim-left">
                <div class="absolute -top-14 -left-10 w-64 h-64 rounded-full bg-white/10 blur-2xl anim-float"></div>
                <div class="absolute top-1/3 -right-6 w-48 h-48 rounded-full bg-cyan-300/20 blur-2xl" style="animation: float 9s ease-in-out infinite 2s;"></div>
                <div class="absolute -bottom-8 left-16 w-32 h-32 rounded-full bg-white/10 blur-2xl" style="animation: float 12s ease-in-out infinite 4s;"></div>

                <div class="relative z-10 h-full flex flex-col justify-between">
                    <div class="flex-1 flex items-center justify-center">
                        <svg viewBox="0 0 320 360" class="w-[280px] h-[320px] anim-float" role="img" aria-label="Ilustración académica">
                            <defs>
                                <linearGradient id="bookGrad" x1="0%" x2="100%">
                                    <stop offset="0%" stop-color="#ffffff"/>
                                    <stop offset="100%" stop-color="#dbeafe"/>
                                </linearGradient>
                            </defs>
                            <ellipse cx="160" cy="320" rx="92" ry="18" fill="#0ea5e933"/>
                            <path d="M70 255c30-20 65-30 90-28v58c-25-2-60 8-90 28z" fill="url(#bookGrad)" stroke="#bfdbfe" stroke-width="2"/>
                            <path d="M250 255c-30-20-65-30-90-28v58c25-2 60 8 90 28z" fill="#e0f2fe" stroke="#93c5fd" stroke-width="2"/>
                            <path d="M160 227v58" stroke="#60a5fa" stroke-width="2"/>
                            <g style="animation: float 7s ease-in-out infinite 1s;">
                                <rect x="116" y="170" width="20" height="26" rx="3" fill="#ffffffcc"/>
                                <rect x="142" y="154" width="20" height="26" rx="3" fill="#e0f2fe"/>
                                <rect x="168" y="170" width="20" height="26" rx="3" fill="#ffffffcc"/>
                            </g>
                            <g style="animation: float 8s ease-in-out infinite 1.2s;">
                                <path d="M120 90l40-18 40 18-40 12z" fill="#0f172a22"/>
                                <path d="M120 88l40-18 40 18-40 12z" fill="#0f172a"/>
                                <path d="M183 97v18" stroke="#0f172a" stroke-width="2"/>
                                <circle cx="183" cy="117" r="4" fill="#ffffff"/>
                            </g>
                            <g fill="#ffffffd9" style="animation: float 6.5s ease-in-out infinite 0.8s;">
                                <path d="M54 122l5 10 10 5-10 5-5 10-5-10-10-5 10-5z"/>
                                <path d="M250 118l4 8 8 4-8 4-4 8-4-8-8-4 8-4z"/>
                                <path d="M274 214l3 6 6 3-6 3-3 6-3-6-6-3 6-3z"/>
                            </g>
                            <g style="animation: float 9s ease-in-out infinite 1.6s;">
                                <path d="M52 210l28 8-22 17z" fill="#ffffffaa"/>
                                <path d="M236 238l24-8-14 22z" fill="#e0f2fe"/>
                            </g>
                        </svg>
                    </div>

                    <div>
                        <h2 class="font-black text-5xl text-white leading-none tracking-tight">
                            APRENDE.<br>POSTULA.<br>TRIUNFA.
                        </h2>
                        <p class="text-sm font-medium text-white/70 mt-3">Tu futuro académico comienza aquí.</p>

                        <div class="mt-8 flex flex-wrap gap-2">
                            <span class="badge-1 bg-white/15 backdrop-blur-sm text-white text-xs font-medium px-4 py-2 rounded-full border border-white/20">📋 Postulación fácil</span>
                            <span class="badge-2 bg-white/15 backdrop-blur-sm text-white text-xs font-medium px-4 py-2 rounded-full border border-white/20">⚡ Resultados rápidos</span>
                            <span class="badge-3 bg-white/15 backdrop-blur-sm text-white text-xs font-medium px-4 py-2 rounded-full border border-white/20">🔒 100% Seguro</span>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- Panel formulario derecho --}}
            <main class="bg-white lg:rounded-l-[3rem] px-8 py-12 md:px-12 md:py-16 lg:px-16 flex flex-col justify-center anim-right"
                  x-data="{ role: 'tutor', showPassword: false, loading: false, shakeForm: false }">
                <div class="w-full max-w-md mx-auto">
                    <div class="flex flex-col items-center lg:items-start">
                        <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 18h16M6 15l6-8 6 8"/></svg>
                        </span>
                        <p class="mt-3 font-bold text-xl text-slate-800">AdmisiónEscolar</p>
                    </div>

                    <h1 class="mt-8 font-black text-3xl text-slate-900 tracking-tight">BIENVENIDO DE VUELTA</h1>
                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">Ingresa tus credenciales para acceder a tu cuenta.</p>

                    @if($errors->any())
                        <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 text-sm px-4 py-3">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mt-8 mb-1">¿Cómo ingresas?</p>
                    <p class="mb-3 text-xs text-slate-500 leading-relaxed">Hay <strong>tres tipos de cuenta</strong>: Ministerio de Educación, personal de un colegio (director o secretaría) y tutor/apoderado. Los postulantes no inician sesión.</p>
                    <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap p-1 bg-slate-100 rounded-2xl">
                        <button type="button" @click="role='tutor'" :class="role==='tutor' ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'" class="flex-1 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 flex items-center justify-center gap-2 min-w-[7rem]">
                            Tutor / apoderado
                        </button>
                        <button type="button" @click="role='ministerio'" :class="role==='ministerio' ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'" class="flex-1 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 flex items-center justify-center gap-2 min-w-[7rem]">
                            Ministerio
                        </button>
                        <button type="button" @click="role='colegio'" :class="role==='colegio' ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'" class="flex-1 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 flex items-center justify-center gap-2 min-w-[7rem]">
                            Unidad educativa
                        </button>
                    </div>
                    <p class="mt-2 text-[11px] text-slate-400 leading-relaxed" x-show="role==='tutor'">Postula, sube documentos y da seguimiento a tus tutelados.</p>
                    <p class="mt-2 text-[11px] text-slate-400 leading-relaxed" x-show="role==='ministerio'" x-cloak>Visión nacional: postulaciones, colegios y reportes territoriales.</p>
                    <p class="mt-2 text-[11px] text-slate-400 leading-relaxed" x-show="role==='colegio'" x-cloak>Gestiona ofertas, cupos y postulaciones de su colegio.</p>

                    <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5"
                          :class="shakeForm ? 'anim-shake' : ''"
                          @submit="if (!$refs.correo.value || !$refs.password.value) { shakeForm = true; setTimeout(() => shakeForm = false, 420); $event.preventDefault(); } else { loading = true; }">
                        @csrf
                        @if(!empty($redirect))
                            <input type="hidden" name="redirect" value="{{ $redirect }}">
                        @endif

                        <div x-data="{ focused: false }">
                            <label for="correo_usu" :class="focused ? 'text-blue-600' : 'text-slate-600'" class="block text-xs font-semibold uppercase tracking-wide mb-2 transition-colors">Correo electrónico</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6m-18 8h18V8H3z"/></svg>
                                </span>
                                <input x-ref="correo" @focus="focused = true" @blur="focused = false" id="correo_usu" name="correo_usu" type="email" value="{{ old('correo_usu') }}" placeholder="tu@correo.com" class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition-all duration-200" required>
                            </div>
                        </div>

                        <div x-data="{ focused: false }">
                            <label for="password_usu" :class="focused ? 'text-blue-600' : 'text-slate-600'" class="block text-xs font-semibold uppercase tracking-wide mb-2 transition-colors">Contraseña</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 10-8 0v4m-1 0h10v9H7z"/></svg>
                                </span>
                                <input x-ref="password" @focus="focused = true" @blur="focused = false" id="password_usu" name="password_usu" :type="showPassword ? 'text' : 'password'" placeholder="••••••••" class="w-full pl-11 pr-11 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition-all duration-200" required>
                                <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 rounded-md text-slate-400 hover:text-slate-600">
                                    <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M1.5 12s4-7 10.5-7 10.5 7 10.5 7-4 7-10.5 7S1.5 12 1.5 12zm10.5 3a3 3 0 100-6 3 3 0 000 6z"/></svg>
                                    <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.58 10.58a3 3 0 004.24 4.24M9.88 5.08A11.6 11.6 0 0112 4.5c6.5 0 10.5 7.5 10.5 7.5a17.1 17.1 0 01-4.18 4.8M6.23 6.23A17.5 17.5 0 001.5 12s4 7.5 10.5 7.5a11.6 11.6 0 005.08-1.2"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-1">
                            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                Recordarme
                            </label>
                            <a href="#" class="text-sm text-blue-600 font-medium hover:underline">¿Olvidaste tu contraseña?</a>
                        </div>

                        <button type="submit" :disabled="loading" class="mt-8 w-full bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold text-base py-4 rounded-xl shadow-lg shadow-blue-300/50 hover:opacity-90 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed">
                            <span x-show="!loading" class="inline-flex items-center gap-2">Iniciar sesión <span aria-hidden="true">→</span></span>
                            <span x-show="loading" x-cloak class="inline-flex items-center gap-2">
                                <svg class="w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity=".25" stroke-width="4"></circle><path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path></svg>
                                Verificando...
                            </span>
                        </button>
                    </form>

                    <div class="mt-6 flex items-center gap-4">
                        <span class="flex-1 h-px bg-slate-200"></span>
                        <span class="text-xs text-slate-400">o continúa con</span>
                        <span class="flex-1 h-px bg-slate-200"></span>
                    </div>

                    <button type="button" class="mt-6 w-full flex items-center justify-center gap-3 border-2 border-slate-200 text-slate-700 font-semibold text-sm py-3.5 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#EA4335" d="M12 10.2v3.9h5.4c-.24 1.26-.96 2.33-2.04 3.05l3.3 2.56c1.92-1.77 3.03-4.38 3.03-7.5 0-.72-.06-1.4-.18-2.07H12z"/>
                            <path fill="#34A853" d="M12 22c2.7 0 4.96-.9 6.6-2.43l-3.3-2.56c-.9.6-2.07.96-3.3.96-2.52 0-4.65-1.7-5.4-3.99l-3.42 2.64A9.98 9.98 0 0012 22z"/>
                            <path fill="#FBBC05" d="M6.6 13.98A5.95 5.95 0 016.3 12c0-.69.12-1.35.3-1.98L3.18 7.38A9.98 9.98 0 002 12c0 1.62.39 3.18 1.08 4.62l3.52-2.64z"/>
                            <path fill="#4285F4" d="M12 6.03c1.47 0 2.8.51 3.84 1.5l2.88-2.88C16.95 2.99 14.7 2 12 2 8.08 2 4.7 4.24 3.18 7.38l3.42 2.64C7.35 7.73 9.48 6.03 12 6.03z"/>
                        </svg>
                        Ingresar con Google
                    </button>

                    <p class="mt-8 text-center text-sm text-slate-500">
                        ¿Aún no tienes cuenta?
                        <a href="{{ route('colegios.index') }}" class="text-blue-600 font-semibold hover:underline ml-1">Regístrate desde un colegio</a>
                    </p>
                </div>
            </main>
        </div>
    </section>

    <x-tutor.assistant-widget context="landing" />
@endsection
