<?php
session_start();
session_unset(); // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión

// Redirigir a la pantalla principal
header('Location: Pantalla-Principal.php');
exit;
?>
