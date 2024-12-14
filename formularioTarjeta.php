<?php
require 'conexionDB.php';

$id_venta = filter_input(INPUT_GET, 'id_venta', FILTER_SANITIZE_STRING);

if (!$id_venta) {
    die("ID de venta no válido.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar Tarjeta</title>
    <link rel="stylesheet" href="Estilo pagos.css">
</head>
<body>
    <div class="container">
        <h2>Ingresa los datos de tu tarjeta</h2>
        <form action="procesar_tarjeta.php" method="post">
            <input type="hidden" name="id_venta" value="<?php echo htmlspecialchars($id_venta); ?>">
            <div class="form-group">
                <label for="numero_tarjeta">Número de Tarjeta</label>
                <input type="text" id="numero_tarjeta" name="numero_tarjeta" maxlength="16" required>
            </div>
            <div class="form-group">
                <label for="nombre_titular">Nombre del Titular</label>
                <input type="text" id="nombre_titular" name="nombre_titular" required>
            </div>
            <div class="form-group">
                <label for="fecha_vencimiento">Fecha de Vencimiento</label>
                <input type="month" id="fecha_vencimiento" name="fecha_vencimiento" required>
            </div>
            <div class="form-group">
                <label for="cvv">Código de Seguridad (CVV)</label>
                <input type="text" id="cvv" name="cvv" maxlength="3" required>
            </div>
            <input type="submit" value="Pagar">
        </form>
    </div>
</body>
</html>