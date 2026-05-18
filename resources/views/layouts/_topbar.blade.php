@php
    use App\Support\DashboardNav;

    $dashboardHomeUrl = DashboardNav::homeUrl($dashboardRole ?? null);
    $headerBreadcrumbs = DashboardNav::breadcrumbs($dashboardRole ?? null);
    $headerNotifications = DashboardNav::notifications();
    $headerSettingsLinks = DashboardNav::settingsLinks($dashboardRole ?? null);
    $profileUrl = DashboardNav::profileUrl($dashboardRole ?? null);
    $hasUnreadNotifications = collect($headerNotifications)->contains(fn ($n) => ($n['type'] ?? '') !== 'info');
@endphp

<header class="sticky top-0 z-30 border-b border-slate-100 bg-white/80 px-6 backdrop-blur-md">
    <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between gap-4">
        <div class="flex min-w-0 items-center gap-3">
            <button
                type="button"
                class="rounded-xl p-2 text-slate-600 transition hover:bg-slate-100 lg:hidden"
                @click="mobileSidebarOpen = true"
                aria-label="Abrir menú"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="hidden min-w-0 md:flex">
                <x-ui.breadcrumb :items="$headerBreadcrumbs" />
            </div>
        </div>

        <div class="flex items-center gap-2">
            <div x-data="{ open: false }" class="relative">
                <button
                    type="button"
                    @click="open = !open"
                    class="relative rounded-xl p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600"
                    aria-label="Notificaciones"
                    :aria-expanded="open"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.17V11a6 6 0 10-12 0v3.17a2 2 0 01-.6 1.43L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($hasUnreadNotifications)
                        <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-red-500"></span>
                    @endif
                </button>

                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition
                    x-cloak
                    class="absolute right-0 top-full z-50 mt-2 w-80 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-xl"
                >
                    <div class="border-b border-slate-100 px-4 py-3">
                        <p class="text-sm font-semibold text-slate-800">Notificaciones</p>
                    </div>
                    <ul class="max-h-72 overflow-y-auto p-2">
                        @foreach($headerNotifications as $notification)
                            <li class="rounded-xl px-3 py-2.5 text-sm
                                @if(($notification['type'] ?? '') === 'success') bg-emerald-50 text-emerald-800
                                @elseif(($notification['type'] ?? '') === 'error') bg-rose-50 text-rose-800
                                @else bg-slate-50 text-slate-600 @endif">
                                {{ $notification['text'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div x-data="{ open: false }" class="relative">
                <button
                    type="button"
                    @click="open = !open"
                    class="rounded-xl p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600"
                    aria-label="Accesos rápidos"
                    :aria-expanded="open"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317a1 1 0 011.35-.936l1.296.518a1 1 0 00.758 0l1.296-.518a1 1 0 011.35.936l.117 1.379a1 1 0 00.516.804l1.184.658a1 1 0 01.432 1.328l-.56 1.266a1 1 0 000 .804l.56 1.266a1 1 0 01-.432 1.328l-1.184.658a1 1 0 00-.516.804l-.117 1.379a1 1 0 01-1.35.936l-1.296-.518a1 1 0 00-.758 0l-1.296.518a1 1 0 01-1.35-.936l-.117-1.379a1 1 0 00-.516-.804l-1.184-.658a1 1 0 01-.432-1.328l.56-1.266a1 1 0 000-.804l-.56-1.266a1 1 0 01.432-1.328l1.184-.658a1 1 0 00.516-.804l.117-1.379zM12 15a3 3 0 100-6 3 3 0 000 6z"/>
                    </svg>
                </button>

                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition
                    x-cloak
                    class="absolute right-0 top-full z-50 mt-2 w-56 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-xl"
                >
                    <div class="border-b border-slate-100 px-4 py-3">
                        <p class="text-sm font-semibold text-slate-800">Accesos rápidos</p>
                    </div>
                    <div class="p-2">
                        @foreach($headerSettingsLinks as $link)
                            <a href="{{ $link['url'] }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div x-data="{ open: false }" class="relative">
                <button
                    type="button"
                    @click="open = !open"
                    class="flex items-center gap-3 rounded-xl p-2 transition hover:bg-slate-100"
                    aria-label="Menú de usuario"
                    :aria-expanded="open"
                >
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 text-xs font-bold text-white">
                        {{ $dashboardInitial ?: 'U' }}
                    </div>
                    <div class="hidden text-left md:block">
                        <p class="text-sm font-semibold text-slate-800">{{ $dashboardUserName }}</p>
                    </div>
                    <span class="hidden rounded-full bg-indigo-100 px-2 py-0.5 text-[10px] font-semibold text-indigo-600 md:inline-flex">
                        {{ $dashboardRoleLabel ?? \App\Support\Roles::label($dashboardRole) }}
                    </span>
                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition.origin.top.right
                    x-cloak
                    class="absolute right-0 top-full z-50 mt-2 w-56 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-xl"
                >
                    <div class="bg-indigo-50 px-4 py-3">
                        <p class="truncate text-sm font-semibold text-indigo-900">{{ $dashboardUserName }}</p>
                        <p class="truncate text-xs text-indigo-500">{{ $dashboardUser->correo_usu ?? '' }}</p>
                    </div>

                    <div class="border-t border-slate-100 p-2">
                        <a href="{{ $profileUrl }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a3 3 0 11-6 0 3 3 0 016 0zm-7 13a7 7 0 1114 0H8z"/></svg>
                            Mi perfil
                        </a>
                        <a href="{{ $dashboardHomeUrl }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-8 9 8v8a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-8z"/></svg>
                            Inicio
                        </a>
                    </div>

                    <div class="border-t border-slate-100 p-2">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-red-500 transition hover:bg-red-50">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H9m-4 8h8a2 2 0 002-2V6a2 2 0 00-2-2H5"/>
                                </svg>
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
