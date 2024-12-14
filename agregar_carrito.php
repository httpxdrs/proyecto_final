<?php
header('Content-Type: application/json');
session_start();
require 'conexionDB.php';

if (!isset($_SESSION['usuario']['correo'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit;
}

$correo = $_SESSION['usuario']['correo'];
$id_producto = $_POST['id_producto'] ?? null;
$cantidad = $_POST['cantidad'] ?? 1;

if (!$id_producto || !$cantidad) {
    echo "<script>alert('Datos incompletos.'); window.history.back();</script>";
    exit;
}

// Obtener precio unitario del producto
$query_precio = "SELECT precio FROM productos WHERE id_productos = $1";
$result_precio = pg_query_params($conexion, $query_precio, [$id_producto]);

if (!$result_precio || pg_num_rows($result_precio) === 0) {
    echo "<script>alert('Producto no encontrado.'); window.history.back();</script>";
    exit;
}

$row_precio = pg_fetch_assoc($result_precio);
$precio_unitario = $row_precio['precio'];
$subtotal = $cantidad * $precio_unitario;

// Verificar si el producto ya está en el carrito
$query_existente = "SELECT cantidad FROM carritocompras WHERE correo = $1 AND id_productos = $2";
$result_existente = pg_query_params($conexion, $query_existente, [$correo, $id_producto]);

if ($result_existente && pg_num_rows($result_existente) > 0) {
    $query_actualizar = "UPDATE carritocompras SET cantidad = cantidad + $1, subtotal = subtotal + $2 WHERE correo = $3 AND id_productos = $4";
    pg_query_params($conexion, $query_actualizar, [$cantidad, $subtotal, $correo, $id_producto]);
} else {
    $query_insertar = "INSERT INTO carritocompras (correo, id_productos, cantidad, precio_unitario, subtotal, fecha_agregacion) 
                       VALUES ($1, $2, $3, $4, $5, NOW())";
    pg_query_params($conexion, $query_insertar, [$correo, $id_producto, $cantidad, $precio_unitario, $subtotal]);
}

header("Location: carrito.php");
exit;
// Cerrar conexión a la base de datos
pg_close($conexion);
?>