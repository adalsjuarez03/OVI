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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #343a40;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .container {
            padding: 40px;
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            margin-bottom: 10px;
        }

        a.logout {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #dc3545;
            text-decoration: none;
            font-weight: bold;
        }

        a.logout:hover {
            text-decoration: underline;
        }
    </style>
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
            <a href="#">Ir a Usuarios</a>
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
