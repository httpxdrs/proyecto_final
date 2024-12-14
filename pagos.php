<?php
require 'conexionDB.php';
// Capturar el ID de la venta desde la URL
$id_venta = filter_input(INPUT_GET, 'id_venta', FILTER_SANITIZE_STRING);

if (empty($id_venta)) {
    die("El ID de la venta no es válido o no fue enviado.");
}

// Validar que la venta existe
$sql = "SELECT id_venta, total FROM ventas WHERE id_venta = $1";
$result = pg_query_params($conexion, $sql, [$id_venta]);

if (!$result || pg_num_rows($result) === 0) {
    die("Venta no encontrada.");
}

$row = pg_fetch_assoc($result);
$total = htmlspecialchars($row['total']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Método de Pago</title>
    <link rel="stylesheet" href="Estilo pagos.css">
</head>
<body>
    <div class="container">
        <h2>Seleccionar Método de Pago</h2>
        <p>Total a pagar: $<?php echo number_format($total, 2); ?></p>
        <form action="procesar_pago.php" method="post">
            <input type="hidden" name="id_venta" value="<?php echo htmlspecialchars($id_venta); ?>">
            <div class="option">
                <input type="radio" id="tarjeta" name="metodo_pago" value="tarjeta" required>
                <label for="tarjeta">Tarjeta (Débito/Crédito)</label>
            </div>
            <div class="option">
                <input type="radio" id="tienda" name="metodo_pago" value="tienda">
                <label for="tienda">Pago en Tienda Departamental</label>
            </div>
            <div class="option">
                <input type="radio" id="efectivo" name="metodo_pago" value="efectivo">
                <label for="efectivo">Pago en Efectivo</label>
            </div>
            <input type="submit" value="Continuar">
        </form>
    </div>
</body>
</html>