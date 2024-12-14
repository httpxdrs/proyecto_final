<?php
require 'conexionDB.php';

// Verificar si el parámetro id está en la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID del producto no proporcionado.";
    exit; // Detener ejecución
}


$id_producto = $_GET['id'];

    $conexion = pg_connect("host=$host dbname=$dbname user=$user password=$password");

    if (!$conexion) {
        die('Error de conexión a PostgreSQL: ' . pg_last_error());
    }

    // Obtener la información del producto
    $sql = "SELECT * FROM productos WHERE id_productos = $1";
    $result = pg_query_params($conexion, $sql, array($id_producto));

    if (!$result || pg_num_rows($result) === 0) {
        echo "Producto no encontrado.";
        exit;
    
}
$producto = pg_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_producto = htmlspecialchars(trim($_POST['nombre_producto']));
    $descripcion = htmlspecialchars(trim($_POST['descripcion']));
    $precio = floatval($_POST['precio']);
    $precio_mayoreo = floatval($_POST['precio_mayoreo']);
    $id_categoria = htmlspecialchars(trim($_POST['id_categoria']));
    $marca = htmlspecialchars(trim($_POST['marca']));

    $fotoDestino = $producto['foto_url']; // Foto existente por defecto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fotoTmp = $_FILES['foto']['tmp_name'];
        $fotoNombre = basename($_FILES['foto']['name']);
        $fotoDestino = "Imagenes/" . $fotoNombre;

        if (!move_uploaded_file($fotoTmp, $fotoDestino)) {
            die('Error al subir la nueva foto.');
        }
    }

    $conexion = pg_connect("host=$host dbname=$dbname user=$user password=$password");

    if (!$conexion) {
        die('Error de conexión a PostgreSQL: ' . pg_last_error());
    }

    $sql = "UPDATE productos SET nombre_producto = $1, descripcion = $2, precio = $3, precio_mayoreo = $4, foto_url = $5, id_categoria = $6, marca = $7 WHERE id_productos = $8";
    $result = pg_query_params($conexion, $sql, [
        $nombre_producto, $descripcion, $precio, $precio_mayoreo, $fotoDestino, $id_categoria, $marca, $id_producto
    ]);

    if ($result) {
        header('Location: administrador.php?mensaje=Producto%20actualizado%20correctamente');
        exit;
    } else {
        echo 'Error al actualizar el producto: ' . pg_last_error($conexion);
    }

    pg_close($conexion);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Editar Producto</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Editar Producto</h1>
        <form action="Editar_producto.php?id=<?= $id_producto ?>" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nombre_producto" class="form-label">Nombre del Producto</label>
                <input type="text" id="nombre_producto" name="nombre_producto" class="form-control" value="<?= $producto['nombre_producto'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="form-control" maxlength="170" rows="3" required><?= $producto['descripcion'] ?></textarea>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" id="precio" name="precio" class="form-control" step="0.01" value="<?= $producto['precio'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="precio_mayoreo" class="form-label">Precio de Mayoreo</label>
                <input type="number" id="precio_mayoreo" name="precio_mayoreo" class="form-control" step="0.01" value="<?= $producto['precio_mayoreo'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="foto" class="form-label">Foto del Producto</label>
                <input type="file" id="foto" name="foto" class="form-control" accept="image/*">
                <img src="<?= $producto['foto_url'] ?>" alt="Foto del producto" class="mt-3" style="max-width: 100px;">
            </div>
            <div class="mb-3">
                <label for="id_categoria" class="form-label">ID de la Categoría</label>
                <input type="text" id="id_categoria" name="id_categoria" class="form-control" value="<?= $producto['id_categoria'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="marca" class="form-label">Marca</label>
                <input type="text" id="marca" name="marca" class="form-control" value="<?= $producto['marca'] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>