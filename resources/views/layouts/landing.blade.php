<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistema de admisión escolar digital para familias, tutores e instituciones.">
    <title>@yield('title', 'AdmisiónEscolar')</title>

    @include('partials.favicon')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
    @stack('styles')
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-[#F8FAFF] text-slate-900 font-sans antialiased">
@yield('content')

@stack('scripts')

@include('partials.ui-global-actions')

<script>
document.addEventListener('alpine:init', () => {
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

    Alpine.data('colegiosFilter', () => ({
        q: '',
        filtro: 'todos',
        visible(el) {
            const abierta = el.dataset.abierta === '1';
            if (this.filtro === 'abiertas' && !abierta) return false;
            const text = (el.dataset.search || '').toLowerCase();
            const query = this.q.trim().toLowerCase();
            if (query && !text.includes(query)) return false;
            return true;
        },
    }));

    Alpine.data('tutorRegistroWizard', (config = {}) => ({
        open: false,
        step: config.initialStep ?? 1,
        totalSteps: 3,
        stepLabels: ['Datos personales', 'Acceso', 'Estudiantes'],
        rudes: config.rudes ?? [''],
        get progress() {
            return (this.step / this.totalSteps) * 100;
        },
        init() {
            if (config.autoOpen) {
                this.open = true;
                document.body.style.overflow = 'hidden';
            }
        },
        openModal() {
            this.open = true;
            this.step = 1;
            document.body.style.overflow = 'hidden';
        },
        closeModal() {
            this.open = false;
            document.body.style.overflow = '';
            if (window.location.search.includes('registro=1')) {
                const url = new URL(window.location.href);
                url.searchParams.delete('registro');
                window.history.replaceState({}, '', url.pathname + url.hash);
            }
        },
        validateStep() {
            const panel = this.$refs['step' + this.step];
            if (!panel) return true;
            const inputs = panel.querySelectorAll('input, select, textarea');
            for (const input of inputs) {
                if (input.offsetParent === null) continue;
                if (!input.checkValidity()) {
                    input.reportValidity();
                    return false;
                }
            }
            return true;
        },
        nextStep() {
            if (!this.validateStep()) return;
            if (this.step < this.totalSteps) this.step++;
        },
        prevStep() {
            if (this.step > 1) this.step--;
        },
        addRude() {
            if (this.rudes.length < 8) this.rudes.push('');
        },
        removeRude(i) {
            if (this.rudes.length > 1) this.rudes.splice(i, 1);
        },
        onSubmit(e) {
            if (!this.validateStep()) {
                e.preventDefault();
            }
        },
    }));
});
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</body>
</html>
