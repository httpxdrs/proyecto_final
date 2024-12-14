<?php
require 'conexionDB.php'; // Conexión a la base de datos

// Obtener la marca seleccionada de la URL
$marca = $_GET['marca'] ?? null;
if (!$marca) {
    die("Marca no especificada.");
}

// Consulta para obtener los productos de la marca seleccionada
$sql = "SELECT id_productos, nombre_producto, precio, foto_url FROM productos WHERE marca = $1";
$result = pg_query_params($conexion, $sql, [$marca]);

if (!$result) {
    die("Error en la consulta: " . pg_last_error($conexion));
}

// Almacenar los productos obtenidos en un arreglo
$productos = [];
while ($row = pg_fetch_assoc($result)) {
    $productos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos de <?= htmlspecialchars($marca); ?></title>
    <link href="Estilo productos.css" rel="stylesheet">
</head>
<body>
<?php require 'barraPrincipal.php'; ?>
    <header>
        <h1>Productos de <?= htmlspecialchars($marca); ?></h1>
    </header>

    <!-- Productos -->
    <main>
        <?php if (!empty($productos)): ?>
            <?php foreach ($productos as $producto): ?>
                <div class="product-card">
                    <!-- Imagen del producto -->
                    <a href="descripcionProductos.php?id=<?= htmlspecialchars($producto['id_productos']); ?>">
                        <img src="<?= htmlspecialchars($producto['foto_url']); ?>" alt="<?= htmlspecialchars($producto['nombre_producto']); ?>">
                    </a>

                    <!-- Información del producto -->
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="descripcionProductos.php?id=<?= htmlspecialchars($producto['id_productos']); ?>">
                                <?= htmlspecialchars($producto['nombre_producto']); ?>
                            </a>
                        </h5>
                        <p class="card-text">$<?= number_format($producto['precio'], 2); ?></p>
                        <a href="descripcionProductos.php?id=<?= htmlspecialchars($producto['id_productos']); ?>" class="btn btn-primary">Ver más</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay productos disponibles para esta marca.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 HuastecShop</p>
    </footer>

</body>
</html>