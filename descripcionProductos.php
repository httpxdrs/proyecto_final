<?php

require 'conexionDB.php'; // Conexión a la base de datos

// Verificar si el parámetro 'id' está presente en la URL
if (isset($_GET['id'])) {
    $idProducto = $_GET['id'];
} else {
    die("ID de producto no especificado.");
}

// Verificar si el producto está en favoritos
$esFavorito = false;
if (isset($_SESSION['usuario']['correo'])) {
    $correoUsuario = $_SESSION['usuario']['correo'];
    $sqlFavorito = "SELECT 1 FROM favoritos WHERE correo = $1 AND id_productos = $2";
    $resultFavorito = pg_query_params($conexion, $sqlFavorito, [$correoUsuario, $idProducto]);

    if ($resultFavorito && pg_num_rows($resultFavorito) > 0) {
        $esFavorito = true;
    }
}

// Consulta para obtener los datos del producto
try {
    // Asegúrate de que $conexion está definido y conectado correctamente
    if (!$conexion) {
        die("Error de conexión a la base de datos.");
    }

    $sql = "SELECT id_productos, nombre_producto, descripcion, precio, precio_mayoreo, foto_url, marca 
            FROM productos 
            WHERE id_productos = $1";
    $result = pg_query_params($conexion, $sql, [$idProducto]);

    if ($result && pg_num_rows($result) > 0) {
        $producto = pg_fetch_assoc($result); // Obtener los datos del producto
    } else {
        die("Producto no encontrado.");
    }
} catch (Exception $e) {
    die("Error al obtener los datos del producto: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($producto['nombre_producto']); ?> - Detalle</title>
    <link href="Estilo descripcion.css" rel="stylesheet"> <!-- Estilos personalizados -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>

<?php
require 'barraPrincipal.php'; // Barra de navegación o encabezado
?>
<div class="product-detail">
    <div class="product-row">
        <!-- Imagen del producto -->
        <div class="product-left">
            <img src="<?= htmlspecialchars($producto['foto_url']); ?>" alt="<?= htmlspecialchars($producto['nombre_producto']); ?>" class="img-fluid">
        </div>

        <!-- Detalles del producto -->
        <div class="product-right">
            <!-- Título y botón de favoritos -->
            <div class="product-header">
                <h1><?= htmlspecialchars($producto['nombre_producto']); ?></h1>
                <button class="btn-favorito <?php echo $esFavorito ? 'favorito-activo' : ''; ?>" data-id="<?php echo $idProducto; ?>">
                    <i class="fa fa-heart"></i>
                </button>
            </div>

            <!-- Información del producto -->
            <p class="brand"><strong>Marca:</strong> <?= htmlspecialchars($producto['marca']); ?></p>
            <p class="price text-danger">$<?= number_format($producto['precio'], 2); ?></p>
            <?php if (!empty($producto['precio_mayoreo'])): ?>
                <p class="wholesale-price text-secondary">Precio a mayoreo: $<?= number_format($producto['precio_mayoreo'], 2); ?></p>
            <?php endif; ?>
            <hr>
            <p class="description"><?= htmlspecialchars($producto['descripcion']); ?></p>

           <!-- Botones -->
           <div class="product-buttons">
                
                
                <!-- Formulario para agregar al carrito -->
                <form id="form-agregar-carrito" method="POST" action="agregar_carrito.php">
                    <input type="hidden" name="id_producto" value="<?= htmlspecialchars($_GET['id']); ?>">
                    <input type="hidden" name="cantidad" value="1">
                    <button type="submit" class="btn btn-primary">Agregar al carrito</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>