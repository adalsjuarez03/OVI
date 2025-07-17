<?php
session_start();
if (isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] === 'admin') {
        header('Location: homeAdmin.php');
    } else {
        header('Location: homeCliente.php');
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../CSS/estilo.css">
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <form action="../Controlador/LoginController.php" method="POST">
        <input type="text" name="usuario" placeholder="Usuario" required><br>
        <input type="password" name="clave" placeholder="Contraseña" required><br>
        <button type="submit">Entrar</button>
    </form>

    <?php if (isset($_GET['error'])): ?>
        <p style="color:red;">Usuario o contraseña incorrectos</p>
    <?php endif; ?>
</body>
</html>
