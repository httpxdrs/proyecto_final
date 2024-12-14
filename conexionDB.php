<?php
// Configuración de conexión
$host = "us-west-2.db.thenile.dev";
$port = "5432";
$dbname = "huastecshop";
$user = "019383b3-d650-777d-9851-b89cd5ab3023";
$password = "9b372cb5-726f-4691-ad5f-c0f5a84dcf07";

// String de conexión para pg_connect
$connection_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

// Intentar conectar a la base de datos
$conexion = pg_connect($connection_string);

// Iniciar la sesión solo si no está ya iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar conexión a la base de datos
if (!$conexion) {
    die("Error al conectar a la base de datos: " . pg_last_error());
}
?>