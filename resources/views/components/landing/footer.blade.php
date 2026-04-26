<footer id="contacto" class="bg-slate-900 text-slate-300 pt-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 grid md:grid-cols-4 gap-10">
        <div>
            <div class="flex items-center gap-2 text-white font-bold text-xl">
                <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 18h16M6 15l6-8 6 8"/></svg>
                AdmisiónEscolar
            </div>
            <p class="mt-4 text-slate-400">Simplificando el acceso a la educación de calidad.</p>
            <div class="mt-5 flex gap-3">
                @foreach (range(1,3) as $i)
                    <span class="w-9 h-9 rounded-full border border-slate-700 flex items-center justify-center text-slate-400 hover:text-white hover:border-slate-500 transition-colors">●</span>
                @endforeach
            </div>
        </div>
        <div>
            <h3 class="text-white font-semibold mb-4">Plataforma</h3>
            <ul class="space-y-3 text-slate-400">
                <li><a href="#inicio" class="hover:text-blue-400 transition-colors">Inicio</a></li>
                <li><a href="#como-funciona" class="hover:text-blue-400 transition-colors">Cómo funciona</a></li>
                <li><a href="#beneficios" class="hover:text-blue-400 transition-colors">Beneficios</a></li>
                <li><a href="{{ route('login.show') }}" class="hover:text-blue-400 transition-colors">Postular</a></li>
            </ul>
        </div>
        <div>
            <h3 class="text-white font-semibold mb-4">Acceso</h3>
            <ul class="space-y-3 text-slate-400">
                <li><a href="{{ route('login.show') }}" class="hover:text-blue-400 transition-colors">Iniciar sesión</a></li>
                <li><a href="{{ route('login.show') }}" class="hover:text-blue-400 transition-colors">Registrarse</a></li>
                <li><a href="{{ route('login.show') }}" class="hover:text-blue-400 transition-colors">Portal de Tutores</a></li>
                <li><a href="{{ route('login.show') }}" class="hover:text-blue-400 transition-colors">Panel Administrativo</a></li>
            </ul>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-6 lg:px-8 mt-12 pt-8 pb-6 border-t border-slate-800 flex flex-col md:flex-row gap-3 justify-between text-sm text-slate-500">
        <p>© 2026 AdmisiónEscolar. Todos los derechos reservados.</p>
        <p>Política de privacidad | Términos de uso</p>
    </div>
</footer>
