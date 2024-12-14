<?php
require 'conexionDB.php';

if (isset($_GET['q'])) {
    $q = $_GET['q'];

    // Consulta para buscar productos que coincidan con la búsqueda
    $sql = "SELECT nombre_producto FROM productos WHERE nombre_producto LIKE :q LIMIT 10";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':q' => "%$q%"]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver resultados como JSON
    echo json_encode($resultados);
}
?>
