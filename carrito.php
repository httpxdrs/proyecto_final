<?php
session_start();
require 'conexionDB.php'; // Conexión a la base de datos

// Verificar si el usuario está autenticado
$correo_usuario = $_SESSION['usuario']['correo'] ?? null;
if (!$correo_usuario) {
    die("Usuario no autenticado.");
}

// Función para calcular el total del carrito
function calcularTotal($conexion, $correo_usuario) {
    $sql_total = "SELECT COALESCE(SUM(subtotal), 0) AS total 
                  FROM carritocompras 
                  WHERE correo = $1";
    $result_total = pg_query_params($conexion, $sql_total, [$correo_usuario]);

    if (!$result_total) {
        die("Error al calcular el total: " . pg_last_error($conexion));
    }

    return pg_fetch_result($result_total, 0, 'total');
}

// Obtener el total del carrito
$total = calcularTotal($conexion, $correo_usuario);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="carrito.css">
    <script src="scripts.js" defer></script>
    <script src="actualizar_carrito.js" defer></script>

</head>
<body>
    <header>
        <h1>Carrito de Compras</h1>
    </header>

    <main>
        <section>
            <table border="1">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                        
                    </tr>
                </thead>
                <tbody id="carrito-container">
                    <?php
                    // Consulta para obtener los productos en el carrito del usuario
                    $sql_productos = "
                        SELECT 
                            p.nombre_producto, 
                            c.cantidad, 
                            c.precio_unitario, 
                            c.subtotal, 
                            c.id_productos 
                        FROM carritocompras c
                        INNER JOIN productos p ON c.id_productos = p.id_productos
                        WHERE c.correo = $1";
                    $result_productos = pg_query_params($conexion, $sql_productos, [$correo_usuario]);

                    if ($result_productos && pg_num_rows($result_productos) > 0) {
                        while ($producto = pg_fetch_assoc($result_productos)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($producto['nombre_producto']) . "</td>";
                            echo "<td>" . htmlspecialchars($producto['cantidad']) . "</td>";
                            echo "<td>$" . number_format($producto['precio_unitario'], 2) . "</td>";
                            echo "<td>$" . number_format($producto['subtotal'], 2) . "</td>";
                            echo "<td>
                            <button class='btn-accion' data-accion='sumar' data-id-producto='" . htmlspecialchars($producto['id_productos']) . "'>+</button>
                            <button class='btn-accion' data-accion='restar' data-id-producto='" . htmlspecialchars($producto['id_productos']) . "'>-</button>
                            <button class='btn-accion' data-accion='eliminar' data-id-producto='" . htmlspecialchars($producto['id_productos']) . "'>Eliminar</button>
                        </td>";
                        
                    echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No hay productos en el carrito.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <aside>
            <h2>Resumen de Compra</h2>
            <p><strong>Total:</strong> $<span id="total-carrito"><?php echo number_format($total, 2); ?></span></p>
            <form action="finalizar_compra.php" method="post">
                <button type="submit">Finalizar Compra</button>
            </form>
        </aside>
    </main>

    <footer>
        <p>Carrito de Compras &copy; 2024</p>
    </footer>
 
</body>
</html>