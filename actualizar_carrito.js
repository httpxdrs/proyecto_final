document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-accion').forEach(button => {
        button.addEventListener('click', function () {
            const accion = this.getAttribute('data-accion');
            const idProducto = this.getAttribute('data-id-producto');

            // Enviar acción al archivo actualizar_carrito.php
            fetch('actualizar_carrito.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accion, idProducto })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Recargar la página para reflejar cambios
                } else {
                    alert(data.message || 'Error al actualizar el carrito.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});