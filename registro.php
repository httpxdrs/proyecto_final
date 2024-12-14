<?php
require 'conexionDB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario con validación básica
    $correo = htmlspecialchars(trim($_POST['correo']));
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $apellidos = htmlspecialchars(trim($_POST['apellidos']));
    $telefono = htmlspecialchars(trim($_POST['telefono']));
    $calle = htmlspecialchars(trim($_POST['calle']));
    $colonia = htmlspecialchars(trim($_POST['colonia']));
    $num_exterior = htmlspecialchars(trim($_POST['num_exterior']));
    $num_interior = htmlspecialchars(trim($_POST['num_interior']));
    $codigo_postal = htmlspecialchars(trim($_POST['codigo_postal']));
    $municipio = htmlspecialchars(trim($_POST['municipio']));
    $estado = htmlspecialchars(trim($_POST['estado']));
    $pais = htmlspecialchars(trim($_POST['pais']));
    $fecha_nac = trim($_POST['fecha_nac']); // No necesitas htmlspecialchars si es una fecha.
    
    // Capturar la contraseña
    $contrasena = trim($_POST['contrasena']); // Asegúrate de capturar este valor
    $contrasenaEncriptada = md5($contrasena); // Encriptar la contraseña

    // Verificar que se aceptaron los términos y condiciones
    if (!isset($_POST['acepto'])) {
        die("Debes aceptar los términos y condiciones para registrarte.");
    }

    // Validaciones adicionales
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        die("El correo ingresado no es válido.");
    }

    // Validar campos de texto
    $campos_texto = [$nombre, $apellidos, $colonia, $municipio, $estado, $pais];
    foreach ($campos_texto as $campo) {
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $campo)) {
            die("El campo '$campo' solo debe contener letras.");
        }
    }

    // Validar campos numéricos
    $campos_numericos = [$telefono, $num_exterior, $num_interior, $codigo_postal];
    foreach ($campos_numericos as $campo) {
        if (!preg_match('/^\d+$/', $campo)) {
            die("El campo '$campo' solo debe contener números.");
        }
    }

    // Insertar los datos en la base de datos
    $sql = "INSERT INTO usuarios (correo, nombre, apellidos, telefono, calle, colonia, num_exterior, num_interior, codigo_postal, municipio, estado, pais, fecha_nac, contrasena, estatus) 
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, 'usuario')";

    $params = [
        $correo, $nombre, $apellidos, $telefono, $calle, $colonia, 
        $num_exterior, $num_interior, $codigo_postal, $municipio, 
        $estado, $pais, $fecha_nac, $contrasenaEncriptada // Aquí usas la contraseña encriptada
    ];

    $result = pg_query_params($conexion, $sql, $params);

    if ($result) {
        echo "Usuario registrado exitosamente.";
        header('Location: login.php');
    } else {
        echo "Error al registrar el usuario: " . pg_last_error($conexion);
    }

    pg_close($conexion);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="Estilo registro.css"/>
    <script>
        function validarFormulario(event) {
            const camposTexto = document.querySelectorAll('[data-type="text-only"]');
            const camposNumeros = document.querySelectorAll('[data-type="number-only"]');

            for (const campo of camposTexto) {
                if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(campo.value)) {
                    alert("El campo " + campo.name + " solo debe contener letras.");
                    campo.focus();
                    event.preventDefault();
                    return false;
                }
            }

            for (const campo of camposNumeros) {
                if (!/^\d+$/.test(campo.value)) {
                    alert("El campo " + campo.name + " solo debe contener números.");
                    campo.focus();
                    event.preventDefault();
                    return false;
                }
            }
        }
    </script>
</head>
<body>
    <form action="registro.php" method="POST" onsubmit="validarFormulario(event)">
        <section class="form-register">
        <h4>Crear una cuenta</h4>
        <input class="controls" type="text" name="nombre" id="nombre" placeholder="Ingrese su nombre" required data-type="text-only">
        <input class="controls" type="text" name="apellidos" id="apellidos" placeholder="Ingrese su apellido" required data-type="text-only">
        <input class="controls" type="email" name="correo" id="correo" placeholder="Ingrese su correo" required>
        <input class="controls" type="text" name="telefono" id="telefono" placeholder="Ingrese su teléfono" required data-type="number-only">

        <!-- Dirección en pares -->
        <label>Dirección</label>
        <div style="display: flex; gap: 10px;">
            <input class="controls" type="text" name="calle" id="calle" placeholder="Calle" required>
            <input class="controls" type="text" name="colonia" id="colonia" placeholder="Colonia" required data-type="text-only">
        </div>
        <div style="display: flex; gap: 10px;">
            <input class="controls" type="text" name="num_exterior" id="num_exterior" placeholder="Núm. exterior" required data-type="number-only">
            <input class="controls" type="text" name="num_interior" id="num_interior" placeholder="Núm. interior (opcional)" data-type="number-only">
        </div>
        <div style="display: flex; gap: 10px;">
            <input class="controls" type="text" name="codigo_postal" id="codigo_postal" placeholder="C.P." required data-type="number-only">
            <input class="controls" type="text" name="municipio" id="municipio" placeholder="Municipio" required data-type="text-only">
        </div>
        <div style="display: flex; gap: 10px;">
            <input class="controls" type="text" name="estado" id="estado" placeholder="Estado" required data-type="text-only">
            <input class="controls" type="text" name="pais" id="pais" placeholder="País" required data-type="text-only">
        </div>

        <label for="fecha_nac" class="label-fecha">Fecha de nacimiento</label>
        <input class="controls fecha" type="date" name="fecha_nac" id="fecha_nac" required>
        <input class="controls" type="password" name="contrasena" id="contraseña" placeholder="Ingrese su contraseña" required>
      
        <div class="checkbox-container">
            <input type="checkbox" name="acepto" id="acepto">
            <label for="acepto">Estoy de acuerdo con <a href="#">Términos y Condiciones</a></label>
        </div>

        <button class="botons" type="submit">Registrarse</button>
        <p>¿Ya tienes cuenta?<a href="login.php">Iniciar Sesión</a></p>
        </section>
    </form>
</body>
</html>
