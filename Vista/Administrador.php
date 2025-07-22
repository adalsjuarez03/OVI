<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: Login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>
    <link rel="stylesheet" href="./CSS/diseno.css">
</head>
<body>

<header>
    <h1>Panel del Administrador</h1>
</header>

<div class="container">
    <div class="dashboard">
        <div class="card">
            <h3>Gestión de Usuarios</h3>
            <p>Ver, agregar o eliminar usuarios del sistema.</p>
            <a href="../admin/usuarios.php">Ir a Usuarios</a>
        </div>

        <div class="card">
            <h3>Eventos</h3>
            <p>Administrar eventos disponibles y programados.</p>
            <a href="#">Ver Eventos</a>
        </div>

        <div class="card">
            <h3>Reportes</h3>
            <p>Generar reportes de actividad y estadísticas.</p>
            <a href="#">Ver Reportes</a>
        </div>

        <div class="card">
            <h3>Configuración</h3>
            <p>Ajustar parámetros del sistema.</p>
            <a href="#">Ir a Configuración</a>
        </div>
    </div>

    <a class="logout" href="Logout.php">Cerrar sesión</a>
</div>

</body>
</html>
