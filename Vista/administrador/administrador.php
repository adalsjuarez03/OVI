<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'administrador') {
    header("Location: ../../index.php");
    exit();
}

include('admin_nav.php'); // Incluye la barra de navegación
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css"> <!-- Ruta del CSS -->
</head>
<body>
    <center><h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>.</h1></center>
    <br>

    <h3>Esta es tu área de administración. Gestiona paquetes, servicios y datos de clientes.</h3>
<br>
<br>
<p>Tu tienes aqui el poder de controlar todo de la la pagina web, podras editar, eliminar, actualizar datos
    datos de servicios de todos los clientes y ademas podras ver todos lo datos de cada cliente, como nombres,
    correo electronico, numero de telefono, su CURP.</p>
    <br>
    <br>
<p>Tu aqui tienes todo el poder sobre tu operadora turistica, espero y puedas manejar todo de la manera mas
    segura posible, aqui queda todo en tus manos, animo administrador y buen trabajo 😊</P>
<br>
<br>
<br>
  <!-- Incluir el footer -->
  <?php include('../../Vista/layout/footer.php'); ?>
</body>
</html>
