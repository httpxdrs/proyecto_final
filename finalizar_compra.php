<?php
session_start();
require 'conexionDB.php';

if (!isset($_SESSION['usuario']['correo'])) {
    die("Usuario no autenticado.");
}

$correo = $_SESSION['usuario']['correo'];

try {
    // Iniciar transacción
    pg_query($conexion, "BEGIN");

    // Calcular el total del carrito
    $query_total = "SELECT id_carrito, COALESCE(SUM(subtotal), 0) AS total FROM carritocompras WHERE correo = $1 GROUP BY id_carrito";
    $result_total = pg_query_params($conexion, $query_total, [$correo]);

    if (!$result_total || pg_num_rows($result_total) === 0) {
        throw new Exception("No hay productos en el carrito.");
    }

    $row_total = pg_fetch_assoc($result_total);
    $total = (float)$row_total['total'];
    $id_carrito = (int)$row_total['id_carrito'];

    if ($total == 0) {
        throw new Exception("El carrito está vacío.");
    }

    // Registrar la venta con el id_carrito
    $query_venta = "INSERT INTO ventas (id_carrito, correo, total, fecha_venta) VALUES ($1, $2, $3, NOW()) RETURNING id_venta";
    $result_venta = pg_query_params($conexion, $query_venta, [$id_carrito, $correo, $total]);

    if (!$result_venta) {
        throw new Exception("Error al registrar la venta.");
    }

    $id_venta = pg_fetch_result($result_venta, 0, 'id_venta');

    // Mantener el carrito lleno hasta que se realice el pago. No se elimina aún.

    // Confirmar transacción
    pg_query($conexion, "COMMIT");

    // Redirigir al archivo de pagos con el id_venta
    header("Location: pagos.php?id_venta=$id_venta");
    exit;
} catch (Exception $e) {
    // Revertir transacción en caso de error
    pg_query($conexion, "ROLLBACK");
    die("Error al finalizar la compra: " . $e->getMessage());
}
?>