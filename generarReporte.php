<?php
require 'conexionDB.php';

// Parámetros dinámicos
$fecha_inicio = $_GET['fecha_inicio'] ?? '2000-01-01';
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
$correo = $_GET['correo'] ?? '';
$id_producto = $_GET['producto'] ?? '';

// Consulta dinámica
$sql_reporte = "
    SELECT 
        v.id_venta, 
        v.correo, 
        v.total, 
        v.fecha_venta, 
        c.id_productos, 
        c.cantidad, 
        c.precio_unitario, 
        c.subtotal
    FROM ventas v
    JOIN carritocompras c ON v.id_carrito = c.id_carrito
    WHERE 
        (v.fecha_venta BETWEEN $1 AND $2) AND
        (v.correo = $3 OR $3 = '') AND
        (c.id_productos = $4 OR $4 = '')";
$params = array($fecha_inicio, $fecha_fin, $correo, $id_producto);
$result_reporte = pg_query_params($conexion, $sql_reporte, $params);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
    
    <link rel="stylesheet" href="Estilo reporte_ventas.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #007bff; /* Color azul */
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        header h1 {
            margin: 0;
            font-size: 24px;
        }
        header nav a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
        }
        header nav a:hover {
            text-decoration: underline;
        }
        main {
            padding-top: 70px; /* Espacio para el encabezado */
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        table th {
            background-color: #f4f4f4;
        }
        button {
            padding: 10px 20px;
            background: #c490d1;
            color: white;
            font-size: 18px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #a36ab7;
        }
    </style>
</head>
<body>



<header>
    <h1>HuasTecShop</h1>

    
</header>

<main class="container">
    <h1>Reporte de Ventas</h1>
    <form method="get">
        <label>Fecha Inicio: <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio); ?>"></label>
        <label>Fecha Fin: <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin); ?>"></label>
        <label>Correo Usuario: <input type="text" name="correo" value="<?= htmlspecialchars($correo); ?>"></label>
        <label>ID Producto: <input type="text" name="producto" value="<?= htmlspecialchars($id_producto); ?>"></label>
        <button type="submit">Buscar</button>
    </form>

    <table>
        <tr>
            <th>ID Venta</th>
            <th>Correo</th>
            <th>Total</th>
            <th>Fecha Venta</th>
            <th>ID Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
        </tr>
        <?php while ($reporte = pg_fetch_assoc($result_reporte)) { ?>
        <tr>
            <td><?= htmlspecialchars($reporte['id_venta']); ?></td>
            <td><?= htmlspecialchars($reporte['correo']); ?></td>
            <td><?= htmlspecialchars($reporte['total']); ?></td>
            <td><?= htmlspecialchars($reporte['fecha_venta']); ?></td>
            <td><?= htmlspecialchars($reporte['id_productos']); ?></td>
            <td><?= htmlspecialchars($reporte['cantidad']); ?></td>
            <td><?= htmlspecialchars($reporte['precio_unitario']); ?></td>
            <td><?= htmlspecialchars($reporte['subtotal']); ?></td>
        </tr>
        <?php } ?>
    </table>

    <div style="margin-top: 20px; text-align: right;">
        <button type="button" onclick="window.location.href='administrador.php';">Regresar</button>
    </div>
</main>

</body>
</html>