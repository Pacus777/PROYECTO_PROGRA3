{{-- Acciones UI globales (funcionan sin depender de x-data de Alpine) --}}
<script>
window.abrirRegistroTutor = function () {
    window.dispatchEvent(new CustomEvent('open-tutor-registro'));
};

window.abrirModal = function (name) {
    window.dispatchEvent(new CustomEvent('open-modal', { detail: name }));
};

window.cerrarModal = function (name) {
    window.dispatchEvent(new CustomEvent('close-modal', { detail: name ?? null }));
};
</script>
