<?php
session_start();
require 'conexionDB.php';

if (!isset($_SESSION['usuario']['correo'])) {
    die("Usuario no autenticado.");
}

$correo = $_SESSION['usuario']['correo'];
$id_venta = filter_input(INPUT_POST, 'id_venta', FILTER_SANITIZE_STRING);

if (!$id_venta) {
    die("ID de venta no válido.");
}

// Datos de la tarjeta (no se guardarán por seguridad)
$numero_tarjeta = filter_input(INPUT_POST, 'numero_tarjeta', FILTER_SANITIZE_STRING);
$nombre_titular = filter_input(INPUT_POST, 'nombre_titular', FILTER_SANITIZE_STRING);
$fecha_vencimiento = filter_input(INPUT_POST, 'fecha_vencimiento', FILTER_SANITIZE_STRING);
$cvv = filter_input(INPUT_POST, 'cvv', FILTER_SANITIZE_STRING);

try {
    // Iniciar transacción
    pg_query($conexion, "BEGIN");

    // Registrar el pago
    $metodo_pago = "Tarjeta de crédito/débito";
    $query_pago = "INSERT INTO pagos (id_venta, metodo_pago, fecha_pago) VALUES ($1, $2, NOW()) RETURNING id_pago";
    $result_pago = pg_query_params($conexion, $query_pago, [$id_venta, $metodo_pago]);

    if (!$result_pago) {
        throw new Exception("Error al registrar el pago.");
    }

    $id_pago = pg_fetch_result($result_pago, 0, 'id_pago');

    // Confirmar transacción
    pg_query($conexion, "COMMIT");

    // Eliminar productos visualmente del carrito del usuario (sin afectar la base de datos)
    $_SESSION['carritocompras'] = [];

    // Redirigir al archivo de confirmación con el id de venta
    header("Location: confirmar_pago.php?id_venta=$id_venta");
    exit;
} catch (Exception $e) {
    // Revertir transacción en caso de error
    pg_query($conexion, "ROLLBACK");
    die("Error al procesar el pago: " . $e->getMessage());
}
?>