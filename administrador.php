este es el de administrador.php: <?php
session_start();
require_once 'conexionDB.php';

// Verificar si el usuario inició sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

// Verificar si el usuario es administrador
$esAdministrador = $_SESSION['usuario']['estatus'] === 'administrador';

// Obtener categorías de la base de datos
$categorias = [];
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

// Obtener la categoría seleccionada
$categoriaSeleccionada = isset($_GET['categoria']) ? htmlspecialchars($_GET['categoria']) : null;

// Variable para almacenar los productos
$productos = [];

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

// Consulta para contar productos por categoría
$productosPorCategoria = [];
try {
    $sqlContar = "
        SELECT c.nombre AS nombre_categoria, COUNT(p.id_productos) AS total
        FROM categorias c
        LEFT JOIN productos p ON c.id_categoria = p.id_categoria
        GROUP BY c.nombre
        ORDER BY c.nombre
    ";
    $resultadoContar = pg_query($conexion, $sqlContar);

    if ($resultadoContar) {
        while ($fila = pg_fetch_assoc($resultadoContar)) {
            $productosPorCategoria[$fila['nombre_categoria']] = $fila['total'];
        }
    } else {
        $error = "Error al contar productos: " . pg_last_error($conexion);
    }
} catch (Exception $e) {
    $error = "Error al contar productos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HuasTecShop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Estilo productosadmi.css">
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">HuasTecShop</a>
            <div class="barra-busqueda">
                <form class="d-flex" action="resultados_busqueda.php" method="get">
                    <input name="q" id="busqueda" class="form-control me-2" type="text" placeholder="Buscar productos..." autocomplete="off" required>
                    <button id="buscar-btn" class="btn btn-primary" type="submit">Buscar</button>
                </form>
            </div>
            <div class="ms-auto d-flex align-items-center">
                <?php if ($esAdministrador): ?>
                    <div class="admin-icon" id="adminIcon" title="Opciones de administrador">☰</div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Menú de administrador -->
    <?php if ($esAdministrador): ?>
        <div class="admin-menu" id="adminMenu">
            <a href="#" id="viewProducts">Ver Productos</a>
            <a href="insertar_producto.php">Insertar Producto</a>
            <a href="#">Editar Productos</a>
            <a href="#">Eliminar Productos</a>
            <a href="VerUsuarios.php">Consultar Usuarios</a>
            <a href="generarReporte.php">Reportes de Ventas</a>
            <a href="cerrarSesion.php">Cerrar sesión</a>
        </div>
    <?php endif; ?>

    <div class="container mt-4">
        <h1 id="welcomeMessage" class="welcome-message">Bienvenido a HuasTecShop</h1>

        <div class="main-container">
            <!-- Categorías -->
            <aside>
                <h5>Categorías</h5>
                <ul>
                    <?php foreach ($productosPorCategoria as $categoria => $total): ?>
                        <li>
                            <a href="?categoria=<?= htmlspecialchars($categoria); ?>"><?= htmlspecialchars($categoria); ?></a>
                            <span>(<?= $total; ?> productos)</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>

            <!-- Productos -->
            <div class="products-container">
                <?php if (!empty($productos)): ?>
                    <?php foreach ($productos as $producto): ?>
                        <div class="product-card">
                            <a href="Editar_producto.php?id=<?= htmlspecialchars($producto['id_productos']); ?>">
                                <img src="<?= htmlspecialchars($producto['foto_url']); ?>" alt="<?= htmlspecialchars($producto['nombre_producto']); ?>">
                            </a>
                            <div class="card-body">
                                <h5>
                                    <a href="Editar_producto.php?id=<?= htmlspecialchars($producto['id_productos']); ?>">
                                        <?= htmlspecialchars($producto['nombre_producto']); ?>
                                    </a>
                                </h5>
                                <p>$<?= number_format($producto['precio'], 2); ?></p>
                                <a href="Editar_producto.php?id=<?= htmlspecialchars($producto['id_productos']); ?>" class="btn btn-primary">Ver más</a>
                                <form action="eliminarProducto.php" method="POST" onsubmit="return confirmarEliminacion();">
                                    <input type="hidden" name="id_productos" value="<?= htmlspecialchars($producto['id_productos']); ?>">
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay productos disponibles.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
<script>
        // Mostrar u ocultar el menú de administrador
        const adminIcon = document.getElementById('adminIcon');
        const adminMenu = document.getElementById('adminMenu');
        const viewProducts = document.getElementById('viewProducts');
        const productsList = document.getElementById('productsList');
        const welcomeMessage = document.getElementById('welcomeMessage');

        adminIcon.addEventListener('click', () => {
            adminMenu.style.display = adminMenu.style.display === 'block' ? 'none' : 'block';
        });

        // Mostrar los productos al hacer clic en "Ver Productos"
        viewProducts.addEventListener('click', (event) => {
            event.preventDefault(); // Evita la navegación del enlace
            productsList.style.display = 'block';
            welcomeMessage.style.display = 'none'; // Ocultar el mensaje de bienvenida
        });

        // Cerrar el menú si se hace clic fuera de él
        document.addEventListener('click', (event) => {
            if (!adminMenu.contains(event.target) && event.target !== adminIcon) {
                adminMenu.style.display = 'none';
            }
        });
    </script>
     <script src="eliminarProducto.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>