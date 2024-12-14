
<?php
require 'conexionDB.php';

// Consulta para listar usuarios
$sql_usuarios = "
    SELECT 
        u.correo, 
        u.nombre, 
        u.apellidos, 
        u.telefono, 
        u.pais, 
        COUNT(v.id_venta) AS total_compras,
        (
            SELECT c.id_productos
            FROM carritocompras c
            WHERE c.correo = u.correo
            GROUP BY c.id_productos
            ORDER BY SUM(c.cantidad) DESC
            LIMIT 1
        ) AS producto_mas_comprado
    FROM usuarios u
    LEFT JOIN ventas v ON u.correo = v.correo
    WHERE u.estatus = 'usuario'
    GROUP BY u.correo, u.nombre, u.apellidos, u.telefono, u.pais";

$result_usuarios = pg_query($conexion, $sql_usuarios);
if (!$result_usuarios) {
    die("Error en la consulta de usuarios: " . pg_last_error($conexion));
}

// Consulta para el usuario con más compras
$sql_top_usuario = "
    SELECT 
        u.correo, 
        u.nombre, 
        COUNT(v.id_venta) AS total_compras
    FROM usuarios u
    JOIN ventas v ON u.correo = v.correo
    GROUP BY u.correo, u.nombre
    ORDER BY total_compras DESC
    LIMIT 1";

$result_top_usuario = pg_query($conexion, $sql_top_usuario);
if (!$result_top_usuario) {
    die("Error en la consulta del usuario con más compras: " . pg_last_error($conexion));
}
$top_usuario = pg_fetch_assoc($result_top_usuario);

$esAdministrador = true; // Cambiar según la lógica de tu sesión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información de Usuarios</title>
    <link rel="stylesheet" href="Estilo VerUsuarios.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">HuasTecShop</a>
            <div class="barra-busqueda">
                <form class="d-flex" action="resultados_busqueda.php" method="get">
                    <input name="q" class="form-control me-2" type="text" placeholder="Buscar productos..." autocomplete="off" required>
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </form>
            </div>
            <div class="ms-auto d-flex align-items-center">
                <?php if ($esAdministrador): ?>
                    <div class="admin-icon" id="adminIcon" title="Opciones de administrador">&#9776;</div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Menú desplegable -->
    <?php if ($esAdministrador): ?>
        <div class="admin-menu" id="adminMenu">
            <a href="administrador.php">Inicio</a>
            <a href="insertar_producto.php">Insertar Producto</a>
            <a href="#">Editar Productos</a>
            <a href="#">Eliminar Productos</a>
            <a href="VerUsuarios.php">Consultar Usuarios</a>
            <a href="generarReporte.php">Reportes de Ventas</a>
            <a class="dropdown-item" href="cerrarSesion.php">Cerrar sesión</a>
        </div>
    <?php endif; ?>

    <div class="container mt-4">
        <h1>Lista de Usuarios</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Correo</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Teléfono</th>
                    <th>País</th>
                    <th>Total Compras</th>
                    <th>Producto Más Comprado</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($usuario = pg_fetch_assoc($result_usuarios)) { ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['correo']); ?></td>
                    <td><?= htmlspecialchars($usuario['nombre']); ?></td>
                    <td><?= htmlspecialchars($usuario['apellidos']); ?></td>
                    <td><?= htmlspecialchars($usuario['telefono']); ?></td>
                    <td><?= htmlspecialchars($usuario['pais']); ?></td>
                    <td><?= htmlspecialchars($usuario['total_compras']); ?></td>
                    <td><?= htmlspecialchars($usuario['producto_mas_comprado']); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <h2>Usuario con Más Compras</h2>
        <?php if ($top_usuario) { ?>
        <p>El usuario con más compras es <strong><?= htmlspecialchars($top_usuario['nombre']); ?></strong> con <strong><?= htmlspecialchars($top_usuario['total_compras']); ?></strong> compras.</p>
        <div class="text-end mt-4">
            <button type="button" onclick="window.location.href='administrador.php';" class="btn btn-secondary">Regresar</button>
        </div>
        <?php } else { ?>
        <p>No se encontraron datos del usuario con más compras.</p>
        <?php } ?>
    </div>

    <script>
        // Mostrar u ocultar el menú de administrador
        const adminIcon = document.getElementById('adminIcon');
        const adminMenu = document.getElementById('adminMenu');

        adminIcon?.addEventListener('click', () => {
            adminMenu.style.display = adminMenu.style.display === 'block' ? 'none' : 'block';
        });

        document.addEventListener('click', (event) => {
            if (!adminMenu.contains(event.target) && event.target !== adminIcon) {
                adminMenu.style.display = 'none';
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>