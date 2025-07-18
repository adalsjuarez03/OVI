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
            <h1 class="form-title">SISTEMA OVI OFICINA VIRTUAL INFORMATICA</h1>
            
            <form method="POST" action="index.php?action=iniciarSesion">
                <div class="form-input">
                    <label for="correo">Usuario o Correo Electrónico</label>
                    <input type="email" name="correo" id="correo" required>
                    <p class="form-hint">Por favor, proporciona un nombre de usuario o un correo</p>
                </div>
                
                <div class="form-input">
                    <label for="contraseña">Contraseña</label>
                    <input type="password" name="contraseña" id="contraseña" required>
                </div>
                
                <div class="divider"></div>
                
                <button type="submit" class="login-btn">Acceder</button>
                
                <div class="forgot-link">
                    <a href="#">Restablecer contraseña</a>
                </div>
            </form>
            
            <div class="footer-text">
                Sistema de Administración de Información Económica de Chiapas © 2025
            </div>
        </div>
    </div>
</div>

</body>

</html>