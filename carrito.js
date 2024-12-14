document.addEventListener('DOMContentLoaded', function () {
    // Seleccionar todos los botones de "Agregar al carrito"
    const botonesAgregar = document.querySelectorAll('.btn-agregar-carrito');

    if (botonesAgregar.length === 0) {
        console.warn('No se encontraron botones con la clase .btn-agregar-carrito');
        return;
    }

    // Agregar un evento de clic a cada botón
    botonesAgregar.forEach(boton => {
        boton.addEventListener('click', function () {
            const productoId = this.getAttribute('data-producto-id'); // Obtener el ID del producto

            if (!productoId) {
                alert('El producto no tiene un ID válido.');
                return;
            }

            // Crear un formulario dinámico y enviarlo
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'agregar_carrito.php';

            // Crear el campo oculto para el ID del producto
            const inputIdProducto = document.createElement('input');
            inputIdProducto.type = 'hidden';
            inputIdProducto.name = 'id_producto';
            inputIdProducto.value = productoId;

            // Crear el campo oculto para la cantidad (1 por defecto)
            const inputCantidad = document.createElement('input');
            inputCantidad.type = 'hidden';
            inputCantidad.name = 'cantidad';
            inputCantidad.value = 1;

            // Agregar los campos al formulario
            form.appendChild(inputIdProducto);
            form.appendChild(inputCantidad);

            // Agregar el formulario al documento y enviarlo
            document.body.appendChild(form);
            form.submit();
        });
    });
});