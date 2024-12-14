<?php


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago en Establecimiento</title>
    <link rel="stylesheet" href="Estilo pagos.css">
</head>
<body>
    <div class="container">
        <h2>Selecciona el punto de pago</h2>
        <form action="procesar_establecimiento.php" method="post">
        <input type="hidden" name="id_venta" value="<?php echo htmlspecialchars($_GET['id_venta']); ?>">
        <div class="form-group">
                <label for="punto_pago">Punto de Pago</label>
                <select id="punto_pago" name="punto_pago" required>
                    <option value="oxxo">OXXO</option>
                    <option value="7eleven">7-Eleven</option>
                    <option value="soriana">Soriana</option>
                    <option value="chedraui">Chedraui</option>
                </select>
            </div>
            <input type="submit" value="Generar Orden">
        </form>
    </div>
</body>
</html>