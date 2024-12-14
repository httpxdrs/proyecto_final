<?php
require 'conexionDB.php'; // Asegúrate de que la conexión está configurada correctamente

try {
    // Consulta SQL para obtener todos los productos
    $sql = "SELECT * FROM productos";
    
    // Ejecutar la consulta directamente con pg_query
    $result = pg_query($conexion, $sql);
    
    if (!$result) {
        throw new Exception("Error al ejecutar la consulta: " . pg_last_error($conexion));
    }

    // Procesar los resultados
    $productos = [];
    while ($row = pg_fetch_assoc($result)) {
        $productos[] = $row;
    }

    // Liberar el recurso de resultados
    pg_free_result($result);

    // Retornar los productos al archivo principal
    return $productos;

} catch (Exception $e) {
    echo "Error al obtener los productos: " . $e->getMessage();
    return [];
}
?>
