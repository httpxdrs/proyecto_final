<?php
session_start();

// Configuración de la conexión a PostgreSQL
require 'conexionDB.php';

// Variable para almacenar errores
$error = "";

// Verifica si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && isset($_POST['contrasena'])) {
        // Obtener los datos del formulario
        $id = trim($_POST['id']);
        $contrasena = trim($_POST['contrasena']);

        // Validar que los campos no estén vacíos
        if (empty($id) || empty($contrasena)) {
            $error = "Por favor, complete todos los campos.";
        } elseif (!filter_var($id, FILTER_VALIDATE_EMAIL) && !preg_match('/^\d{10}$/', $id)) {
            // Validar que el ID sea un correo electrónico o un número de teléfono
            $error = "Por favor, ingrese un correo electrónico válido o un número de teléfono (10 dígitos).";
        } else {
            // Establecer la conexión a PostgreSQL
            $conexion = pg_connect("host=$host dbname=$dbname user=$user password=$password");

            // Verificar si la conexión fue exitosa
            if (!$conexion) {
                die('Error de conexión a PostgreSQL: ' . pg_last_error());
            }

            // Consulta para verificar el usuario
            $sql = "SELECT correo, nombre, telefono, contrasena, estatus FROM usuarios WHERE correo = $1 OR telefono = $1";
            $result = pg_query_params($conexion, $sql, [$id]);

            if (!$result) {
                die('Error al ejecutar la consulta: ' . pg_last_error());
            }

            // Verificar si se encontró el usuario
            if (pg_num_rows($result) === 1) {
                $usuario = pg_fetch_assoc($result);

                // Verificar la contraseña usando MD5
                if (md5($contrasena) === $usuario['contrasena']) {
                    // Redirigir según el estatus
                    $_SESSION['correo'] = $usuario['correo'];
                    $_SESSION['usuario'] = [
                        'correo' => $usuario['correo'],
                        'nombre' => $usuario['nombre'],
                        'estatus' => $usuario['estatus']
                    ];

                    if ($usuario['estatus'] === 'administrador') {
                        header('Location: administrador.php');
                    } else {
                        header('Location: Pantalla-Principal.php');
                    }
                    exit;
                } else {
                    $error = "Contraseña incorrecta.";
                }
            } else {
                $error = "Usuario no encontrado.";
            }

            // Liberar resultado y cerrar la conexión
            pg_free_result($result);
            pg_close($conexion);
        }
    } else {
        $error = "Por favor ingrese el correo y la contraseña.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Inicio de Sesión - HuasTecShop</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Estilo login.css"/>
</head>
<body>
    <div class="formulario">
        <h1>Iniciar Sesión</h1>
        <form method="post" action="login.php">
            <div class="username">
                <input type="text" id="id" name="id" required>
                <label for="id">Correo Electrónico o Teléfono</label>
            </div>
            <div class="username">
                <input type="password" id="contrasena" name="contrasena" required>
                <label for="contrasena">Contraseña</label>
            </div>
            <div href="recuperarContra.php" class="recordar">¿Olvidó su contraseña?</div>
            <input type="submit" value="Iniciar Sesión">
            <div class="registrarse">
                ¿No tienes cuenta? <a href="registro.php" class="text-decoration-none">Regístrate aquí</a>.
            </div>
        </form>

        <?php if (!empty($error)): ?>
            <!-- Mostrar ventana emergente con el error -->
            <script>
                alert("<?= htmlspecialchars($error); ?>");
            </script>
        <?php endif; ?>
    </div>

    <script src="E-shop.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
