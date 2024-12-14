document.addEventListener('DOMContentLoaded', function () {
    // Seleccionar todos los botones de "Agregar al carrito"
    const botonesAgregar = document.querySelectorAll('.btn-agregar-carrito');

    // Agregar un evento de clic a cada botón
    botonesAgregar.forEach(boton => {
        boton.addEventListener('click', function () {
            const productoId = this.getAttribute('data-producto-id'); // Obtener el ID del producto

            // Realizar una solicitud al servidor para agregar el producto
            fetch('agregar_carrito.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'include', // Enviar las cookies de sesión
                body: JSON.stringify({
                    id_producto: productoId,
                    cantidad: 1 // Por defecto, agregar una unidad
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Producto agregado al carrito correctamente.');
                    } else {
                        alert('Error al agregar el producto: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un problema al agregar el producto al carrito.');
                });
        });
    });
});