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
        // Verificar si el producto ya está en favoritos
        $sqlCheck = "SELECT * FROM favoritos WHERE correo = $1 AND id_productos = $2";
        $resultCheck = pg_query_params($conexion, $sqlCheck, [$correoUsuario, $idProducto]);

        if (pg_num_rows($resultCheck) > 0) {
            echo json_encode(['success' => false, 'error' => 'El producto ya está en favoritos.']);
        } else {
            // Insertar en la tabla favoritos
            $sqlInsert = "INSERT INTO favoritos (correo, id_productos, fecha_agregado) VALUES ($1, $2, NOW())";
            $resultInsert = pg_query_params($conexion, $sqlInsert, [$correoUsuario, $idProducto]);

            if ($resultInsert) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => pg_last_error($conexion)]);
            }
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'ID de producto no proporcionado.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
}
?>