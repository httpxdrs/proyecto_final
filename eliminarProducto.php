<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id_productos'])) {
        $id_producto = htmlspecialchars($_POST['id_productos']);

        // Conexión a la base de datos
        require 'conexionDB.php'; // Asegúrate de configurar tu archivo de conexión.

        // Preparar y ejecutar la consulta DELETE
        $query = "DELETE FROM productos WHERE id_productos = $1";
        $stmt = pg_prepare($conexion, "delete_product", $query);

        if ($stmt && pg_execute($conexion, "delete_product", [$id_producto])) {
            // Redirigir a la misma página después de eliminar
            header("Location: administrador.php?mensaje=Producto eliminado correctamente");
            exit;
        } else {
            // Redirigir con un mensaje de error
            header("Location: productosList.php?mensaje=Error al eliminar el producto");
            exit;
        }

        pg_close($conexion);
    } else {
        // Redirigir si no hay ID proporcionado
        header("Location: productosList.php?mensaje=ID de producto no proporcionado");
        exit;
    }
} else {
    // Redirigir si el método no es POST
    header("Location: productosList.php?mensaje=Método no permitido");
    exit;
}
?>