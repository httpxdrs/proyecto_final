<?php
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
    <title>Pago Exitoso</title>
    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        h2 {
            color: green;
            font-size: 2em;
        }
        p {
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>¡Pago exitoso de su carrito, gracias por su compra!</h2>
        <p>Tu ID de venta es: <strong><?php echo htmlspecialchars($id_venta); ?></strong></p>
        <a href="Pantalla-Principal.php">Regresar al inicio</a>
    </div>
</body>
</html>