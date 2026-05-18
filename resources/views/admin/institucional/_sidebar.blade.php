<a href="{{ route('admin.institucional.dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.institucional.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700' }} transition-colors">
    Dashboard
</a>
<a href="{{ route('admin.institucional.academic.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.institucional.academic.*') || request()->routeIs('admin.institucional.niveles.*') || request()->routeIs('admin.institucional.cursos.*') || request()->routeIs('admin.institucional.paralelos.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700' }} transition-colors">
    Gestión académica
</a>
<a href="{{ route('admin.institucional.ofertas.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.institucional.ofertas.*') || request()->routeIs('admin.institucional.cupos.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700' }} transition-colors">
    Ofertas y cupos
</a>
<a href="{{ route('admin.institucional.postulaciones.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.institucional.postulaciones.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700' }} transition-colors">
    Postulaciones
</a>
<a href="{{ route('admin.institucional.criterios.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.institucional.criterios.*') || request()->routeIs('admin.institucional.evaluaciones.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700' }} transition-colors">
    Evaluación
</a>
<a href="{{ route('admin.institucional.resultados.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.institucional.resultados.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700' }} transition-colors">
    Resultados
</a>
<a href="{{ route('admin.institucional.asignacion.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.institucional.asignacion.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700' }} transition-colors">
    Asignación
</a>
<a href="{{ route('admin.institucional.lista-espera.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.institucional.lista-espera.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700' }} transition-colors">
    Lista de espera
</a>
<a href="{{ route('admin.institucional.historial.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.institucional.historial.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700' }} transition-colors">
    Historial
</a>
<a href="{{ route('admin.institucional.reportes.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.institucional.reportes.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700' }} transition-colors">
    Reportes
</a>

