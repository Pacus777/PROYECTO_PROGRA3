@once
    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('ofertaForm', (config) => ({
                cursos: config.cursos,
                paralelos: config.paralelos,
                nivelId: String(config.nivelId || ''),
                cursoId: String(config.cursoId || ''),
                paraleloId: String(config.paraleloId || ''),
                get cursosFiltrados() {
                    if (!this.nivelId) return [];
                    return this.cursos.filter(c => String(c.nivel_id) === String(this.nivelId));
                },
                get paralelosFiltrados() {
                    if (!this.cursoId) return [];
                    return this.paralelos.filter(p => String(p.curso_id) === String(this.cursoId));
                },
                onNivelChange() {
                    this.cursoId = '';
                    this.paraleloId = '';
                },
                onCursoChange() {
                    const curso = this.cursos.find(c => String(c.id) === String(this.cursoId));
                    if (curso) this.nivelId = String(curso.nivel_id);
                    this.paraleloId = '';
                },
                init() {
                    if (this.cursoId && !this.nivelId) {
                        const curso = this.cursos.find(c => String(c.id) === String(this.cursoId));
                        if (curso) this.nivelId = String(curso.nivel_id);
                    }
                },
            }));
        });
    </script>
    @endpush
@endonce
