<?php
session_start();

// Redirigir si ya hay sesión activa
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
    <link rel="stylesheet" href="./CSS/style.css">
</head>

<body>

<div class="login-container">
    <div class="login-box">
        <!-- Sección del logo -->
        <div class="logo-section">
            <img src="https://ovi.economiaytrabajo.chiapas.gob.mx/static/LOGO_SECRETARI%CC%81A_2.png" alt="Logo Secretaría">
        </div>
        
        <!-- Sección del formulario -->
        <div class="form-section">
            <h1 class="form-title">SISTEMA OVI OFICINA VIRTUAL INFORMÁTICA</h1>
            
            <!-- FORMULARIO -->
            <form method="POST" action="../Controlador/LoginController.php">
                <div class="form-input">
                    <label for="usuario">Usuario o Correo Electrónico</label>
                    <input type="text" name="usuario" id="usuario" required>
                    <p class="form-hint">Por favor, proporciona tu usuario o correo electrónico</p>
                </div>
                
                <div class="form-input">
                    <label for="clave">Contraseña</label>
                    <input type="password" name="clave" id="clave" required>
                </div>
                
                <div class="divider"></div>
                
                <button type="submit" class="login-btn">Acceder</button>
                
                <div class="forgot-link">
                    <a href="#">Restablecer contraseña</a>
                </div>
            </form>

            <!-- MENSAJE DE ERROR -->
            <?php if (isset($_GET['error'])): ?>
                <p style="color:red; margin-top:10px;">Usuario o contraseña incorrectos</p>
            <?php endif; ?>
            
            <div class="footer-text">
                Sistema de Administración de Información Económica de Chiapas © 2025
            </div>
        </div>
    </div>
</div>

</body>
</html>
