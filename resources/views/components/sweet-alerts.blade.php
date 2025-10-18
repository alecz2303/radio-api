{{-- resources/views/components/sweet-alerts.blade.php --}}
<script>
document.addEventListener('DOMContentLoaded', () => {

    const showAlert = (type, title, text) => {
        // Espera a que SweetAlert2 esté disponible
        if (typeof Swal === 'undefined') {
            setTimeout(() => showAlert(type, title, text), 100);
            return;
        }

        Swal.fire({
            icon: type,
            title: title,
            text: text,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Aceptar',
            timer: type === 'success' ? 2500 : undefined,
            timerProgressBar: type === 'success'
        });
    };

    @if(session('success'))
        showAlert('success', '¡Éxito!', '{{ session('success') }}');
    @endif

    @if(session('error'))
        showAlert('error', 'Error', '{{ session('error') }}');
    @endif

    @if(session('warning'))
        showAlert('warning', 'Atención', '{{ session('warning') }}');
    @endif
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Confirmación al eliminar (para todos los formularios con clase delete-form)
    document.querySelectorAll('.delete-form').forEach(form => {
        const btn = form.querySelector('.delete-btn');
        if (!btn) return;
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            // Espera a que SweetAlert esté disponible
            const waitForSwal = setInterval(() => {
                if (typeof Swal !== 'undefined') {
                    clearInterval(waitForSwal);
                    Swal.fire({
                        title: '¿Eliminar registro?',
                        text: 'Esta acción no se puede deshacer.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true,
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                }
            }, 100);
        });
    });
});
</script>
