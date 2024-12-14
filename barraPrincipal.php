<?php
 // Inicia la sesión si no está iniciada
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HuasTecShop</title>
    <link rel="stylesheet" href="Estilo E-Shop.css"> <!-- Ruta correcta al CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="favoritos.js" defer></script>

</head>
<body>
<header class="barra-superior">
    <div class="container-fluid d-flex align-items-center justify-content-between py-2">
        <div class="d-flex align-items-center">
            <a href="Pantalla-Principal.php">
                <img controls width="100" src="Imagenes/Sticker_Circular_Gracias_por_tu_Compra_Simple_Blanco_y_Rosa__1_-removebg-preview.png" alt="Logo" class="logo" style="margin-right: 10px;">
                <span class="text-bold">HuasTecShop</span>
            </a>
        </div>
        
        <div class="barra-busqueda">
            <form class="d-flex" action="resultados_busqueda.php" method="get">
                <input name="q" id="busqueda" class="form-control me-2" type="text" placeholder="Buscar productos..." autocomplete="off" required>
                <button id="buscar-btn" class="btn btn-primary" type="submit">Buscar</button>
            </form>
        </div>
    </div>
</header>

<nav class="barra-inferior">
    <div class="container d-flex align-items-center justify-content-between">
        <ul class="menu d-flex list-unstyled mb-0 ms-auto">
            <?php if (isset($_SESSION['usuario'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="abrirFavoritos">Favoritos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="carrito.php">Carrito</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="compras.php">Mis Compras</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Hola, <?= htmlspecialchars($_SESSION['usuario']['nombre']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="mi_perfil.php">Mi perfil</a></li>
                        <li><a class="dropdown-item" href="cerrarSesion.php">Cerrar sesión</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a class="nav-link" href="login.php">Mi Perfil</a></li>
                <li><a class="nav-link" href="login.php">Favoritos</a></li>
                <li><a class="nav-link" href="login.php">Carrito</a></li>
                <li><a class="nav-link" href="login.php">Mis Compras</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<!-- Modal de favoritos -->
<div id="favoritosModal" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mis Favoritos</h5>
                <button type="button" class="btn-close" id="cerrarFavoritos"></button>
            </div>
            <div class="modal-body">
                <ul id="favoritos-lista">
                    <li><p>Cargando favoritos...</p></li>
                </ul>
            </div>
        </div>
    </div>
</div>
