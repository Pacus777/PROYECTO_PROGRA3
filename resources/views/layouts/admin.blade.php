<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administración | AdmisiónEscolar')</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <div class="flex min-h-screen">
        {{-- Barra lateral --}}
        <aside class="hidden lg:flex w-64 flex-col border-r border-slate-200/80 bg-white shadow-sm">
            <div class="p-6 border-b border-slate-100">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-blue-700 font-bold text-lg tracking-tight">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-white text-sm">AE</span>
                    AdmisiónEscolar
                </a>
                <p class="mt-2 text-xs font-semibold uppercase tracking-wider text-slate-400">@yield('admin_label', 'Admin general')</p>
            </div>
            <nav class="flex-1 p-4 space-y-1">
                @hasSection('admin_sidebar')
                    @yield('admin_sidebar')
                @else
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-700 transition-colors">
                        Inicio
                    </a>
                    <a href="{{ route('admin.usuarios.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.usuarios.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700' }} transition-colors">
                        Usuarios
                    </a>
                    <a href="{{ route('admin.unidades.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.unidades.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700' }} transition-colors">
                        Unidades educativas
                    </a>
                @endif
            </nav>
            <div class="p-4 border-t border-slate-100 text-xs text-slate-500">
                @if($layoutWebUsuario)
                    <p class="font-medium text-slate-800 truncate">{{ trim(($layoutWebUsuario->persona->nombres_per ?? '').' '.($layoutWebUsuario->persona->ap_paterno_per ?? '')) ?: $layoutWebUsuario->correo_usu }}</p>
                    <p class="truncate mt-0.5">{{ $layoutWebUsuario->rol->nombre_rol ?? '' }}</p>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="text-blue-600 font-semibold hover:underline">Cerrar sesión</button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="lg:hidden flex items-center justify-between px-4 py-3 bg-white border-b border-slate-200">
                <a href="{{ route('dashboard') }}" class="font-bold text-blue-700">Admin</a>
                <form method="POST" action="{{ route('logout') }}" class="text-sm">
                    @csrf
                    <button type="submit" class="text-slate-600">Salir</button>
                </form>
            </header>
            <div class="h-1 bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-500"></div>

            <main class="flex-1 p-6 lg:p-10 max-w-6xl w-full mx-auto">
                @if(session('success'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
