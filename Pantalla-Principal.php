<?php
session_start();
require_once 'conexionDB.php';

// Incluye el archivo que devuelve los productos
$productos = require 'productosRelacionados.php';

$favoritos = [];
if (isset($_SESSION['usuario'])) {
    $idUsuario = $_SESSION['usuario']['correo'];
    $sql = "SELECT p.id_productos, p.nombre_producto, p.precio, p.foto_url 
            FROM favoritos f 
            JOIN productos p ON f.id_productos = p.id_productos 
            WHERE f.correo = $1";
    $result = pg_query_params($conexion, $sql, [$idUsuario]);

    if ($result) {
        $favoritos = pg_fetch_all($result);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HuasTecShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Estilo E-Shop.css">
    <script src="E-shop.js"></script>
</head>
<body>
    <?php
    require 'barraPrincipal.php';
    ?>

    <!-- Menú lateral visible solo para administradores -->
    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['estatus'] === 'administrador'): ?>
        <nav class="menu-lateral">
            <button id="menu-toggle">☰</button>
            <ul id="menu">
                <li><a href="gestion_productos.php">Gestión de Productos</a></li>
                <li><a href="gestion_usuarios.php">Gestión de Usuarios</a></li>
                <li><a href="reportes.php">Reportes</a></li>
            </ul>
        </nav>
    <?php endif; ?>

    <!-- Carrusel -->
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="4"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="5"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="Imagenes/imagenNueva.png" class="d-block w-100" alt="Imagen 1">
            </div>
            <div class="carousel-item">
                <img src="Imagenes/image 4.png" class="d-block w-100" alt="Imagen 2">
            </div>
            <div class="carousel-item">
                <img src="Imagenes/image.png" class="d-block w-100" alt="Imagen 3">
            </div>
            <div class="carousel-item">
                <img src="Imagenes/imagen 2.png" class="d-block w-100" alt="Imagen 4">
            </div>
            <div class="carousel-item">
                <img src="Imagenes/imagen 8.png" class="d-block w-100" alt="Imagen 5">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>

    <!-- Inspirados en tus favoritos -->
    <div class="productos-relacionados">
        <h3>Relacionado con tus visitas</h3>
        <div class="product-slider">
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $producto): ?>
                    <a href="descripcionProductos.php?id=<?= htmlspecialchars($producto['id_productos']) ?>" class="product-link">
                        <div class="product-item">
                            <img src="<?= htmlspecialchars($producto['foto_url']); ?>" alt="<?= htmlspecialchars($producto['nombre_producto']); ?>" class="img-fluid">
                            <p class="product-name"><?= htmlspecialchars($producto['nombre_producto']); ?></p>
                            <p class="product-price">
                                $<?= number_format($producto['precio'], 2); ?>
                                <?php if (!empty($producto['precio_original'])): ?>
                                    <span class="price-original">$<?= number_format($producto['precio_original'], 2); ?></span>
                                <?php endif; ?>
                            </p>
                            <p class="text-success">40% OFF</p>
                            <p class="product-extra">Envío gratis</p>
                        </div>


                        
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay productos disponibles.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="categorias-tendencia" style="background-color: white;">
    <div class="container py-5 text-center text-white">
        <h2 class="mb-4">Categorías en Tendencia</h2>
        <div class="row justify-content-center">
            <!-- Categoría Hogar -->
            <div class="col-md-3">
                <a href="productos.php?categoria=Tecnologia" class="text-decoration-none">
                    <div class="category-card">
                        <div class="content">
                            <h3 class="text-light">Tecnologia</h3>
                            <p class="text-light">Hasta 40% OFF</p>
                        </div>
                    </div>
                </a>
            </div>
            <!-- Categoría Moda -->
            <div class="col-md-3">
                <a href="productos.php?categoria=moda" class="text-decoration-none">
                    <div class="category-card">
                        <div class="content">
                            <h3 class="text-light">Moda</h3>
                            <p class="text-light">Hasta 40% OFF</p>
                        </div>
                    </div>
                </a>
            </div>
            <!-- Categoría Cocina -->
            <div class="col-md-3">
                <a href="productos.php?categoria=hogar" class="text-decoration-none">
                    <div class="category-card">
                        <div class="content">
                            <h3 class="text-light">Hogar</h3>
                            <p class="text-light">Hasta 30% OFF</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
<!--Marcas-->
<section class="official-stores">
    <h2>TIENDAS OFICIALES EN TECNOLOGÍA </h2>

    <div class="brand-container">
        <div class="brand">
        <a href="marcas.php?marca=Samsung" class="text-decoration-none">
            <img src="Imagenes/Samsung.png" alt="Samsung">
            <p>Hasta 30% OFF</p>
            </div>
        </a>
        
        <div class="brand">
        <a href="marcas.php?marca=Sony" class="text-decoration-none">
            <img src="Imagenes/Sony.jpg" alt="Sony">
            <p>Hasta 20% OFF</p>
        </div>
        </a>
        <div class="brand">
        <a href="marcas.php?marca=LG" class="text-decoration-none">
            <img src="Imagenes/LG.png" alt="LG">
            
            <p>Hasta 25% OFF</p>
        </div>
        </a>
        <div class="brand">
        <a href="marcas.php?marca=Apple" class="text-decoration-none">
            <img src="Imagenes/Apple.png" alt="Apple">
            <p>Hasta 15% OFF</p>
        </div>
        </a>
        <div class="brand">
        <a href="marcas.php?marca=Xiaomi" class="text-decoration-none">
            <img src="Imagenes/Xiaomi.png" alt="Xiaomi">
            <p>Hasta 35% OFF</p>
        </div>
        </a>
        <div class="brand">
        <a href="marcas.php?marca=Microsoft" class="text-decoration-none">
            <img src="Imagenes/Microsoft.png" alt="Microsoft">
            <p>Hasta 40% OFF</p>
        </div>
        </a>
        <div class="brand">
        <a href="marcas.php?marca=Lenovo" class="text-decoration-none">
            <img src="Imagenes/Lenovo.png" alt="Lenovo">
            <p>Hasta 30% OFF</p>
        </div>
        </a>
        <div class="brand">
        <a href="marcas.php?marca=Huawei" class="text-decoration-none">
            <img src="Imagenes/Huawei.jpg" alt="Huawei">
            <p>Hasta 25% OFF</p>
        </div>
    </div>
    </a>
</section>
          
</body>