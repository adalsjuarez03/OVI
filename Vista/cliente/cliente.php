<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header("Location: ../../index.php");
    exit();
}

include('clientes_nav.php'); // Incluye la barra de navegación
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área de Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css"> <!-- Ruta del CSS -->
</head>
<body>
    <center><h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>.</h1></center>
    <br>
    <br>
    <h3>Explora paquetes, servicios y visualiza todas tus reservas.</h3>
    <br>
    <p>EasyTour la mejor opción para turistear en parejas o en familia, contamos con paquetes y 
        servicios increíbles, conoce todo Chiapas y sus maravillas, te aseguramos que no te vas a 
        arrepentir de preferirnos. Cualquier duda o pregunta tenemos todos los datos en la parte 
        de "Contacto".</p>
    <br>
    <p>Que tenga un bonito y agradable experiencia 😊</p>

<br>
<br>
    <!-- Incluir el footer -->
    <?php include('../../Vista/layout/footer.php'); ?>
</body>
</html>
