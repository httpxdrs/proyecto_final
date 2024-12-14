<?php
session_start();
require 'conexionDB.php';

if (!isset($_SESSION['usuario']['correo'])) {
    die("Usuario no autenticado.");
}

$correo = $_SESSION['usuario']['correo'];

try {
    // Consulta para obtener las compras realizadas
    $query_compras = "SELECT id_carrito, correo, total, fecha_venta FROM ventas WHERE correo = $1 ORDER BY fecha_venta DESC";
    $result_compras = pg_query_params($conexion, $query_compras, [$correo]);

    if (!$result_compras || pg_num_rows($result_compras) === 0) {
        $compras = [];
    } else {
        $compras = pg_fetch_all($result_compras);
    }
} catch (Exception $e) {
    die("Error al cargar las compras: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Compras</title>
    <link rel="stylesheet" href="Estilo compras.css">
</head>
<body>
    <div class="container">
        <h1>Mis Compras</h1>

        <?php if (empty($compras)): ?>
            <p>No has realizado ninguna compra aún.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Carrito</th>
                        <th>Correo</th>
                        <th>Total</th>
                        <th>Fecha de Compra</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($compras as $compra): ?>
                        <tr>
                            <td><?= htmlspecialchars($compra['id_carrito']); ?></td>
                            <td><?= htmlspecialchars($compra['correo']); ?></td>
                            <td>$<?= number_format($compra['total'], 2); ?></td>
                            <td><?= htmlspecialchars($compra['fecha_venta']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <button onclick="window.location.href='Pantalla-Principal.php'">Regresar</button>
    </div>
</body>
</html>