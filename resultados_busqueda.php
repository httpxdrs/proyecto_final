<?php
require 'conexionDB.php'; // Asegúrate de que el archivo esté en la ruta correcta

// Obtener el término de búsqueda desde la URL (método GET)
$busqueda = isset($_GET['q']) ? $_GET['q'] : '';

try {
    // Consulta SQL con parámetros
    $sql = "SELECT * FROM productos WHERE nombre_producto ILIKE $1"; // ILIKE para búsquedas no sensibles a mayúsculas/minúsculas
    $result = pg_query_params($conexion, $sql, ['%' . $busqueda . '%']); // Ejecutar la consulta con parámetros

    if (!$result) {
        throw new Exception(pg_last_error($conexion));
    }

    $productos = [];
    while ($row = pg_fetch_assoc($result)) {
        $productos[] = $row;
    }
} catch (Exception $e) {
    die("Error al realizar la consulta: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Búsqueda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Estilo busqueda.css">
</head>
<body>
    <header>
       <?php
       require 'barraPrincipal.php';
       ?>
       
    </header>
    <main class="container my-4">
    <h2>Resultados para: <?= htmlspecialchars($busqueda) ?></h2>
    <div class="row">
        <?php if (count($productos) > 0): ?>
            <?php foreach ($productos as $producto): ?>
                <div class="col-md-3">
                    <a href="descripcionProductos.php?id=<?= htmlspecialchars($producto['id_productos']) ?>" class="product-link">
                        <div class="product-card">
                            <!-- Imagen del producto -->
                            <img src="<?= htmlspecialchars($producto['foto_url']) ?>" class="img-fluid" alt="<?= htmlspecialchars($producto['nombre_producto']) ?>">
                            
                            <!-- Nombre del producto -->
                            <h5 class="mt-2"><?= htmlspecialchars($producto['nombre_producto']) ?></h5>
                            
                            <!-- Precio del producto -->
                            <p class="text-danger">$<?= number_format($producto['precio'], 2) ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No se encontraron resultados.</p>
        <?php endif; ?>
    </div>
</main>
<script src="favoritos.js"></script>

</body>
</html>
