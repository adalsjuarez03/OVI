<?php
require_once '../Modelo/Conexion.php';
$conn = Conexion::conectar();

// Registrar
if (isset($_POST['registrar'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $rol = $_POST['rol'];
    $contrasena = $_POST['contrasena'];

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, correo, telefono, rol, contrasena) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nombre, $apellido, $correo, $telefono, $rol, $contrasena);
    $stmt->execute();
    $stmt->close();
}

// Editar
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $rol = $_POST['rol'];

    $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, telefono = ?, rol = ? WHERE id_usuario = ?");
    $stmt->bind_param("sssssi", $nombre, $apellido, $correo, $telefono, $rol, $id);
    $stmt->execute();
    $stmt->close();
}

// Eliminar
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conn->query("DELETE FROM usuarios WHERE id_usuario=$id");
}

$resultado = $conn->query("SELECT * FROM usuarios");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="./CSS/usuario.css">
</head>
<body>

<a href="Administrador.php" class="btn-volver">← Regresar a Usuarios</a>
<h2>Gestión de Usuarios</h2>

<!-- Botón para abrir modal -->
<button class="btn-abrir-modal" onclick="document.getElementById('modalRegistrar').showModal()">Registrar nuevo usuario</button>

<!-- MODAL REGISTRAR -->
<dialog id="modalRegistrar">
    <h3>Registrar Usuario</h3>
    <form method="post">
        <label>Nombre</label>
        <input type="text" name="nombre" required>

        <label>Apellido</label>
        <input type="text" name="apellido" required>

        <label>Correo</label>
        <input type="email" name="correo" required>

        <label>Teléfono</label>
        <input type="text" name="telefono" required>

        <label>Rol</label>
        <select name="rol" required>
            <option value="Cliente">Cliente</option>
            <option value="admin">Administrador</option>
        </select>

        <label>Contraseña</label>
        <input type="text" name="contrasena" required>

        <div class="modal-actions">
            <button type="button" class="cancelar" onclick="document.getElementById('modalRegistrar').close()">Cancelar</button>
            <button type="submit" name="registrar" class="guardar">Registrar</button>
        </div>
    </form>
</dialog>

<!-- TABLA -->
<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $resultado->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['nombre'] . ' ' . $row['apellido'] ?></td>
                <td><?= $row['correo'] ?></td>
                <td><?= $row['telefono'] ?></td>
                <td><?= $row['rol'] ?></td>
                <td>
                    <div class="acciones">
                        <a class="descargar" href="generar_pdf.php?id=<?= $row['id_usuario'] ?>" target="_blank">
    <i class="fas fa-download"></i>
</a>

                        <button class="editar" onclick='abrirEditar(<?= json_encode($row) ?>)'><i class="fas fa-pen"></i></button>
                        <button class="eliminar" onclick='abrirEliminar(<?= $row["id_usuario"] ?>)'><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<!-- MODAL EDITAR -->
<dialog id="modalEditar">
    <h3>Editar Usuario</h3>
    <form method="post">
        <input type="hidden" name="id" id="edit-id">

        <label>Nombre</label>
        <input type="text" name="nombre" id="edit-nombre" required>

        <label>Apellido</label>
        <input type="text" name="apellido" id="edit-apellido" required>

        <label>Correo</label>
        <input type="email" name="correo" id="edit-correo" required>

        <label>Teléfono</label>
        <input type="text" name="telefono" id="edit-telefono" required>

        <label>Rol</label>
        <select name="rol" id="edit-rol" required>
            <option value="Cliente">Cliente</option>
            <option value="admin">Administrador</option>
        </select>

        <div class="modal-actions">
            <button type="button" class="cancelar" onclick="cerrarEditar()">Cancelar</button>
            <button type="submit" name="editar" class="guardar">Guardar</button>
        </div>
    </form>
</dialog>

<!-- MODAL ELIMINAR -->
<dialog id="modalEliminar">
    <h3>¿Eliminar usuario?</h3>
    <form method="get">
        <input type="hidden" name="eliminar" id="delete-id">
        <div class="modal-actions">
            <button type="button" class="cancelar" onclick="cerrarEliminar()">Cancelar</button>
            <button type="submit" class="guardar">Eliminar</button>
        </div>
    </form>
</dialog>

<script src="./js/usuario.js"></script>

</body>
</html>
