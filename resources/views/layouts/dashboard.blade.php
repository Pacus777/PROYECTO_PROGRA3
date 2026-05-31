<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard | AdmisiónEscolar')</title>

    @include('partials.favicon')

    @vite(['resources/css/app.css'])
    @stack('styles')

    <style>
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #6366f1 #eef2ff;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #eef2ff;
            border-radius: 999px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #6366f1;
            border-radius: 999px;
            border: 2px solid #eef2ff;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse-soft {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: .75; }
        }

        @keyframes barGrow {
            from { width: 0; }
            to { width: var(--bar-w, 0%); }
        }

        .animate-fadeInUp {
            animation: fadeInUp .4s ease-out forwards;
        }

        .pulse-soft {
            animation: pulse-soft 1.8s ease-in-out infinite;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="bg-[#F5F7FF] text-[#1E1B4B] antialiased">
@php
    /** @var \App\Models\Usuario|null $dashboardUser */
    $dashboardUserId = session('web_usuario_id');
    $dashboardUser = $dashboardUserId
        ? \App\Models\Usuario::query()->with(['persona', 'rol'])->find($dashboardUserId)
        : null;

    $dashboardUserName = trim(($dashboardUser->persona->nombres_per ?? '').' '.($dashboardUser->persona->ap_paterno_per ?? ''));
    $dashboardUserName = $dashboardUserName !== '' ? $dashboardUserName : ($dashboardUser->correo_usu ?? 'Usuario');
    $dashboardRole = $dashboardUser->rol->nombre_rol ?? 'usuario';
    $dashboardRoleLabel = \App\Support\Roles::label($dashboardRole);
    $dashboardInitial = strtoupper(mb_substr($dashboardUserName, 0, 1));
@endphp

<div x-data="{ sidebarOpen: true, mobileSidebarOpen: false, successToast: true }">
    @include('layouts._sidebar', ['dashboardUser' => $dashboardUser, 'dashboardUserName' => $dashboardUserName, 'dashboardRole' => $dashboardRole, 'dashboardRoleLabel' => $dashboardRoleLabel, 'dashboardInitial' => $dashboardInitial])

    <div :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'" class="min-h-screen transition-all duration-300">
        @include('layouts._topbar', ['dashboardUser' => $dashboardUser, 'dashboardUserName' => $dashboardUserName, 'dashboardRole' => $dashboardRole, 'dashboardRoleLabel' => $dashboardRoleLabel, 'dashboardInitial' => $dashboardInitial])

        <main class="flex justify-center p-6 lg:p-8 animate-fadeInUp">
            <div class="w-full max-w-7xl mx-auto min-w-0">
            {{-- Toast de éxito con auto-cierre --}}
            @if(session('success'))
                <div
                    x-show="successToast"
                    x-init="setTimeout(() => successToast = false, 3000)"
                    x-transition.opacity.duration.300ms
                    class="fixed bottom-6 right-6 z-[60] max-w-sm rounded-2xl bg-emerald-600 px-5 py-4 text-white shadow-2xl"
                >
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-medium leading-relaxed">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M10.29 3.86l-7.39 12.8A1 1 0 003.76 18h16.48a1 1 0 00.86-1.34l-7.39-12.8a1 1 0 00-1.72 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-red-700">Se encontraron errores en el formulario.</p>
                            <ul class="mt-1 list-disc pl-5 text-xs text-red-600 space-y-0.5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @yield('content')
            </div>
        </main>
    </div>

    <div
        x-show="mobileSidebarOpen"
        @click="mobileSidebarOpen = false"
        x-transition.opacity
        class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden"
    ></div>
</div>

@if(($dashboardRole ?? '') === \App\Support\Roles::TUTOR)
    <x-tutor.assistant-widget context="tutor" />
@endif

    @stack('scripts')
    @include('partials.ui-global-actions')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</body>
</html>

