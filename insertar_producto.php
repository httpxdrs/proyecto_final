<?php
require 'conexionDB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $id_producto = htmlspecialchars(trim($_POST['id_producto']));
    $nombre_producto = htmlspecialchars(trim($_POST['nombre_producto']));
    $descripcion = htmlspecialchars(trim($_POST['descripcion']));
    $precio = floatval($_POST['precio']);
    $precio_mayoreo = floatval($_POST['precio_mayoreo']);
    $id_categoria = htmlspecialchars(trim($_POST['id_categoria']));
    $marca = htmlspecialchars(trim($_POST['marca']));

    // Manejar la subida de la foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fotoTmp = $_FILES['foto']['tmp_name'];
        $fotoNombre = basename($_FILES['foto']['name']);
        $fotoDestino = "Imagenes/" . $fotoNombre; // Asegúrate de que la carpeta "uploads" exista y tenga permisos de escritura

        // Mover la foto al destino
        if (!move_uploaded_file($fotoTmp, $fotoDestino)) {
            die('Error al subir la foto.');
        }
    } else {
        die('No se pudo cargar la foto.');
    }

    // Insertar en la base de datos
    $conexion = pg_connect("host=$host dbname=$dbname user=$user password=$password");

    if (!$conexion) {
        die('Error de conexión a PostgreSQL: ' . pg_last_error());
    }

    $sql = "INSERT INTO productos (id_productos, nombre_producto, descripcion, precio, precio_mayoreo, foto_url, id_categoria, marca)
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";
    $result = pg_query_params($conexion, $sql, [
        $id_producto, $nombre_producto, $descripcion, $precio, $precio_mayoreo, $fotoDestino, $id_categoria, $marca
    ]);

    if ($result) {
        echo "Producto agregado correctamente.";
    } else {
        echo "Error al insertar el producto: " . pg_last_error($conexion);
    }

    pg_close($conexion);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Agregar Producto - Administrador</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="Estilo insertar_productos.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Agregar Nuevo Producto</h1>
        <form action="insertar_producto.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="id_producto" class="form-label">ID del Producto</label>
                <input type="text" id="id_producto" name="id_producto" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="nombre_producto" class="form-label">Nombre del Producto</label>
                <input type="text" id="nombre_producto" name="nombre_producto" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="form-control" maxlength="170" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" id="precio" name="precio" class="form-control" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="precio_mayoreo" class="form-label">Precio de Mayoreo</label>
                <input type="number" id="precio_mayoreo" name="precio_mayoreo" class="form-control" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="foto" class="form-label">Foto del Producto</label>
                <input type="file" id="foto" name="foto" class="form-control" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label for="id_categoria" class="form-label">ID de la Categoría</label>
                <input type="text" id="id_categoria" name="id_categoria" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="marca" class="form-label">Marca</label>
                <input type="text" id="marca" name="marca" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Agregar Producto</button>
            
        </form>
    </div>
</body>
</html>