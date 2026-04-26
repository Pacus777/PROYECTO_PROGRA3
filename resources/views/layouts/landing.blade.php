<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistema de admisión escolar digital para familias, tutores e instituciones.">
    <title>@yield('title', 'AdmisiónEscolar')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#F8FAFF] text-slate-900 font-sans antialiased">
@yield('content')

<script>
document.addEventListener('alpine:init', () => {
    // Controla estado del navbar (scroll + menú mobile).
    Alpine.data('navbarState', () => ({
        mobileOpen: false,
        scrolled: false,
        init() {
            this.onScroll();
            window.addEventListener('scroll', () => this.onScroll(), { passive: true });
        },
        onScroll() {
            this.scrolled = window.scrollY > 20;
        },
    }));

    // Efecto de entrada por sección al entrar al viewport.
    Alpine.data('revealOnScroll', () => ({
        shown: false,
        init() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        this.shown = true;
                        observer.disconnect();
                    }
                });
            }, { threshold: 0.15 });
            observer.observe(this.$el);
        }
    }));
});
</script>
</body>
</html>
