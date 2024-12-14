<?php
session_start();
require 'conexionDB.php';

header('Content-Type: application/json'); // Respuesta JSON

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario']['correo'])) {
    echo json_encode(['success' => false, 'error' => 'No autenticado.']);
    exit;
}

$correoUsuario = $_SESSION['usuario']['correo'];

// Consulta para obtener favoritos
$sql = "SELECT f.id_productos, p.nombre_producto, p.precio, p.foto_url 
        FROM favoritos f
        INNER JOIN productos p ON f.id_productos = p.id_productos
        WHERE f.correo = $1
        ORDER BY f.fecha_agregado DESC";

$result = pg_query_params($conexion, $sql, [$correoUsuario]);

if ($result) {
    $favoritos = [];
    while ($row = pg_fetch_assoc($result)) {
        $favoritos[] = $row;
    }
    echo json_encode(['success' => true, 'favoritos' => $favoritos]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al obtener favoritos: ' . pg_last_error($conexion)]);
}

// Cerrar conexión
pg_close($conexion);
?>