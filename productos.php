<?php

require 'conexionDB.php'; // Archivo que contiene la configuración de conexión a PostgreSQL

// Inicializamos la variable de categorías
$categorias = [];
$error = "";

// Obtener las categorías para el sidebar
try {
    $sqlCategorias = "SELECT id_categoria, nombre FROM categorias";
    $resultCategorias = pg_query($conexion, $sqlCategorias);

    if ($resultCategorias) {
        while ($row = pg_fetch_assoc($resultCategorias)) {
            $categorias[] = $row;
        }
    } else {
        $error = "Error al obtener categorías: " . pg_last_error($conexion);
    }
} catch (Exception $e) {
    $error = "Error al obtener categorías: " . $e->getMessage();
}

// Obtener la categoría seleccionada desde el parámetro GET
$categoriaSeleccionada = isset($_GET['categoria']) ? $_GET['categoria'] : null;

// Variable para almacenar los productos
$productos = [];

// Obtener los productos según la categoría seleccionada
if ($categoriaSeleccionada) {
    try {
        // Consulta SQL con jerarquía de categorías usando CTE recursivos
        $sql = "
            WITH RECURSIVE categorias_recursivas AS (
                SELECT id_categoria
                FROM categorias
                WHERE nombre = $1
                UNION ALL
                SELECT c.id_categoria
                FROM categorias c
                INNER JOIN categorias_recursivas cr
                ON c.categoria_padre = cr.id_categoria
            )
            SELECT 
                p.id_productos, 
                p.nombre_producto, 
                p.descripcion, 
                p.precio, 
                p.foto_url 
            FROM 
                productos p
            JOIN 
                categorias_recursivas cr
            ON 
                p.id_categoria = cr.id_categoria;
        ";

        // Preparar y ejecutar la consulta
        $result = pg_query_params($conexion, $sql, [$categoriaSeleccionada]);

        if ($result) {
            // Obtener los productos
            while ($row = pg_fetch_assoc($result)) {
                $productos[] = $row;
            }
        } else {
            $error = "Error en la consulta: " . pg_last_error($conexion);
        }
    } catch (Exception $e) {
        $error = "Error al obtener productos: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - HuasTecShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="Estilo productos.css" rel="stylesheet">
</head>
<body>
    <?php require 'barraPrincipal.php'; ?>

    <div class="container" style="padding-left: 60px">
        <!-- Barra lateral (Categorías) -->
        <aside>
            <h5>Género</h5>
            <ul>
                <li><a href="?categoria=mujer">Mujer</a></li>
                <li><a href="?categoria=hombre">Hombre</a></li>
                <li><a href="?categoria=ninos">Niños</a></li>
                <li><a href="?categoria=ninas">Niñas</a></li>
                <li><a href="?categoria=bebes">Bebes</a></li>
                <li><a href="?categoria=cocina">Cocina</a></li>
                <li><a href="?categoria=Dormitorio">Dormitorio</a></li>
                <li><a href="?categoria=Mascotas">Mascotas</a></li>
                <li><a href="?categoria=SuperMercado">SuperMercado</a></li>
                <li><a href="?categoria=Muebles">Muebles</a></li>
                <li><a href="?categoria=celulares">Celulares</a></li>
                <li><a href="?categoria=tabletas">Tabletas</a></li>
                <li><a href="?categoria=computadoras">Computadoras</a></li>
                <li><a href="?categoria=audifonos">Audifonos</a></li>
                <li><a href="?categoria=televisiones">Televisiones</a></li>
                <li><a href="?categoria=relojes">Relojes</a></li>
            </ul>
        </aside>

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
        <p>No hay productos disponibles para esta categoría.</p>
    <?php endif; ?>
</main>
<script src="favoritos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>