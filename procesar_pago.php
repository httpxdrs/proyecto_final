<?php
require 'conexionDB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_venta = filter_input(INPUT_POST, 'id_venta', FILTER_SANITIZE_STRING);
    $metodo_pago = filter_input(INPUT_POST, 'metodo_pago', FILTER_SANITIZE_STRING);

    // Validar datos básicos
    if (!$id_venta || !$metodo_pago) {
        die("Datos incompletos. Por favor, selecciona un método de pago.");
    }

    // Validar método de pago
    $metodos_validos = ['tarjeta', 'tienda', 'efectivo'];
    if (!in_array($metodo_pago, $metodos_validos)) {
        die("Método de pago no válido.");
    }

    // Registrar el pago
    $sql_pago = "INSERT INTO pagos (id_venta, metodo_pago, fecha_pago) VALUES ($1, $2, NOW())";
    $result_pago = pg_query_params($conexion, $sql_pago, [$id_venta, $metodo_pago]);

    if (!$result_pago) {
        die("Error al registrar el pago.");
    }

    // Redirigir según el método de pago
    if ($metodo_pago === 'tarjeta') {
        header("Location: formularioTarjeta.php?id_venta=$id_venta");
    } elseif ($metodo_pago === 'tienda') {
        header("Location: formularioPagoEstable.php?id_venta=$id_venta");
    } else {
        header("Location: confirmacion_pago.php?id_venta=$id_venta");
    }
    exit;
}
?>