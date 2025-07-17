<?php require_once('Vista/layout/header.php');?>
<br>
<br>
<br>
<br>
<br>
<br>
<center><h1>Registrate gratis y conoce todos los paquetes</h1></center>
<br>
<br>
<br>
<form method="POST" action="index.php?action=registrarUsuario">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" id="nombre" required>

    <label for="apellido">Apellido:</label>
    <input type="text" name="apellido" id="apellido" required>

    <label for="telefono">Teléfono:</label>
    <input type="text" name="telefono" id="telefono" required>

    <label for="correo">Correo:</label>
    <input type="email" name="correo" id="correo" required>
    <br>
    <br>
    <br>
    <br>

    <label for="curp">CURP:</label>
    <input type="text" name="curp" id="curp" required>

    <label for="contraseña">Contraseña:</label>
    <input type="password" name="contraseña" id="contraseña" required>
    <br>
    <br>
    <br>
    <br>

    <button type="submit">Registrarse</button>
</form>
<br>
<br>
<br>

<?php if (!empty($mensaje)): ?>
    <p><?php echo $mensaje; ?></p>
<?php endif; ?>

<?php require_once('Vista/layout/footer.php');?>
