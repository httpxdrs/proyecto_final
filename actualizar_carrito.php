<?php
session_start();
require 'conexionDB.php'; // Conexión a la base de datos

// Verificar si el usuario está autenticado
$correo_usuario = $_SESSION['usuario']['correo'] ?? null;
if (!$correo_usuario) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit;
}

// Obtener datos del request
$data = json_decode(file_get_contents('php://input'), true);
$accion = $data['accion'] ?? null;
$idProducto = $data['idProducto'] ?? null;

// Validar datos
if (!$accion || !$idProducto) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

// Procesar la acción
switch ($accion) {
    case 'sumar':
        $sql = "UPDATE carritocompras SET cantidad = cantidad + 1, subtotal = cantidad * precio_unitario 
                WHERE correo = $1 AND id_productos = $2";
        $params = [$correo_usuario, $idProducto];
        break;

    case 'restar':
        $sql = "UPDATE carritocompras SET cantidad = GREATEST(cantidad - 1, 1), subtotal = cantidad * precio_unitario 
                WHERE correo = $1 AND id_productos = $2";
        $params = [$correo_usuario, $idProducto];
        break;

    case 'eliminar':
        $sql = "DELETE FROM carritocompras WHERE correo = $1 AND id_productos = $2";
        $params = [$correo_usuario, $idProducto];
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
        exit;
}

// Ejecutar la consulta
$result = pg_query_params($conexion, $sql, $params);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Acción realizada correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . pg_last_error($conexion)]);
}