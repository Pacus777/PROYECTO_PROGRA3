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
                selectedDocs: config.selectedDocs || [],
                
                // Wizard variables
                step: 1,
                maxStep: 3,
                
                get CursosFiltrados() {
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
                
                // Wizard navigation
                prevStep() {
                    if (this.step > 1) {
                        this.step--;
                    }
                },
                nextStep() {
                    if (this.step < this.maxStep) {
                        if (this.validateStep(this.step)) {
                            this.step++;
                        }
                    }
                },
                goToStep(s) {
                    if (s < this.step) {
                        this.step = s;
                    } else if (s > this.step) {
                        let valid = true;
                        for (let i = this.step; i < s; i++) {
                            if (!this.validateStep(i)) {
                                valid = false;
                                break;
                            }
                        }
                        if (valid) {
                            this.step = s;
                        }
                    }
                },
                validateStep(s) {
                    if (s === 1) {
                        if (!this.nivelId || !this.cursoId || !this.paraleloId) {
                            alert('Por favor, complete la selección de Nivel, Curso y Paralelo para avanzar.');
                            return false;
                        }
                    }
                    return true;
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
