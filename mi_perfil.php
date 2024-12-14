<?php
session_start();
// Validar sesión
if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['correo'])) {
    header("Location: login.html");
    exit;
}
// Configuración de la conexión
$host = "us-west-2.db.thenile.dev";
$port = "5432";
$dbname = "huastecshop";
$user = "019383b3-d650-777d-9851-b89cd5ab3023";
$password = "9b372cb5-726f-4691-ad5f-c0f5a84dcf07";
try {
    // Crear conexión PDO
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    $conexion = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Obtener el correo del usuario de la sesión
    $correo = $_SESSION['usuario']['correo'];

    // Consultar datos del usuario
    $sql = "SELECT nombre, apellidos, telefono, calle, colonia, num_exterior, 
                   num_interior, codigo_postal, municipio, estado, pais, fecha_nac
            FROM usuarios WHERE correo = :correo";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':correo', $correo);
    $stmt->execute();

    // Verificar si el usuario existe
    if ($stmt->rowCount() > 0) {
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "Usuario no encontrado.";
        exit;
    }
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil de Usuario</title>
  <link rel="stylesheet" href="Estilo mi_perfil.css">
</head>
<body>
  <div class="container">
    <h1>Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?> <?php echo htmlspecialchars($usuario['apellidos']); ?></h1>
    <p><strong>Correo:</strong> <?php echo htmlspecialchars($correo); ?></p>
    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($usuario['telefono']); ?></p>
    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($usuario['calle']); ?>, 
      <?php echo htmlspecialchars($usuario['colonia']); ?>, 
      No. Ext: <?php echo htmlspecialchars($usuario['num_exterior']); ?>, 
      No. Int: <?php echo htmlspecialchars($usuario['num_interior']); ?></p>
    <p><strong>Código Postal:</strong> <?php echo htmlspecialchars($usuario['codigo_postal']); ?></p>
    <p><strong>Municipio:</strong> <?php echo htmlspecialchars($usuario['municipio']); ?></p>
    <p><strong>Estado:</strong> <?php echo htmlspecialchars($usuario['estado']); ?></p>
    <p><strong>País:</strong> <?php echo htmlspecialchars($usuario['pais']); ?></p>
    <p><strong>Fecha de Nacimiento:</strong> <?php echo htmlspecialchars($usuario['fecha_nac']); ?></p>


     <!-- Botón de regreso a la página principal -->
     <div class="boton-regreso">
      <a href="Pantalla-Principal.php" class="btn-regresar">Regresar al inicio</a>
    </div>
  </div>

  </div>
</body>
</html>
