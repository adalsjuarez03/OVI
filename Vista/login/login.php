<?php require_once('Vista/layout/header.php');?>

<br>
<br>
<br>
<br>
<br>
<center><h1>Bienvenidos</h1></center>
<br>
<form method="POST" action="index.php?action=iniciarSesion">
    <label for="correo">Correo:</label>
    <input type="email" name="correo" id="correo" required>
    <label for="contraseña">Contraseña:</label>
    <input type="password" name="contraseña" id="contraseña" required>
    <br>
    <br>
    <br>
    <button type="submit">Iniciar Sesión</button>
</form>
<br>
<br>
<br>
<p>¿No tienes cuenta? <a href="index.php?action=registrarUsuario" class="custom-link">Regístrate aquí</a></p>

<br>
<br>

<?php require_once('Vista/layout/footer.php');?>
