<?php
session_start();
if (isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] === 'admin') {
        header('Location: Administrador.php');
    } else {
        header('Location: Cliente.php');
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../CSS/style.css">
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
