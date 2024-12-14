<?php
session_start();
require 'conexionDB.php';

header('Content-Type: application/json'); // Respuesta JSON

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario']['correo'])) {
    echo json_encode(['success' => false, 'error' => 'No autenticado. Inicia sesión para continuar.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $idProducto = $data['idProducto'] ?? null;
    $correoUsuario = $_SESSION['usuario']['correo'];

    if ($idProducto) {
        // Eliminar de la tabla favoritos
        $sql = "DELETE FROM favoritos WHERE correo = $1 AND id_productos = $2";
        $result = pg_query_params($conexion, $sql, [$correoUsuario, $idProducto]);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => pg_last_error($conexion)]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'ID de producto no proporcionado.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
}
?>