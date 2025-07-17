<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Cliente</title>
</head>
<body>
    <h1>Bienvenido, Cliente</h1>
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>
