<?php require_once('Vista/layout/header.php');?>
<br>
<br>
<h1>Acceso de Usuarios</h1>
<br>
<div class="container">
    <form action="index.php?m=validarUsuario" method="POST">
        <div class="form-group">
            <label for="usuario">Usuario:</label>
            <input type="text" class="form-control" id="usuario" name="usuario" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <br>
        <br>
        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
    </form>
</div>
<br>
<br>
<?php require_once('Vista/layout/footer.php');?>
