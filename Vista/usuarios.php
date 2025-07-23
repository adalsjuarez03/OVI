<?php
require_once '../Modelo/Conexion.php'; // Ajusta esta ruta según donde tengas conexion.php
$conn = Conexion::conectar();

// Agregar usuario
if (isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $telefono = $_POST['telefono'];
    $rol = $_POST['rol'];

    $stmt = $conn->prepare("INSERT INTO Usuarios (Nombre, Apellido, Correo, Contrasena, Telefono, Rol) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nombre, $apellido, $correo, $contrasena, $telefono, $rol);
    $stmt->execute();
    $stmt->close();
    header("Location: usuarios.php");
    exit();
}

// Editar usuario
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $rol = $_POST['rol'];

    $stmt = $conn->prepare("UPDATE Usuarios SET Nombre=?, Apellido=?, Correo=?, Telefono=?, Rol=? WHERE Id_usuario=?");
    $stmt->bind_param("sssssi", $nombre, $apellido, $correo, $telefono, $rol, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: usuarios.php");
    exit();
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conn->query("DELETE FROM Usuarios WHERE Id_usuario=$id");
    header("Location: usuarios.php");
    exit();
}

// Obtener todos los usuarios
$result = $conn->query("SELECT * FROM Usuarios");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f8f8f8; }
        h1, h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; background-color: #fff; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        form { margin-top: 20px; }
        input, select { padding: 8px; margin: 5px 0; width: calc(100% - 20px); box-sizing: border-box; }
        button { padding: 8px 12px; background-color: #3498db; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #2980b9; }
        .acciones form { display: inline-block; }
        .acciones a { color: red; text-decoration: none; margin-left: 10px; }
        .acciones a:hover { text-decoration: underline; }
        .form-inline input, .form-inline select { width: auto; margin-right: 5px; }
        .form-container { background: #fff; padding: 15px; border: 1px solid #ccc; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Gestión de Usuarios</h1>

    <!-- Formulario para agregar nuevo usuario -->
    <div class="form-container">
        <h2>Agregar Usuario</h2>
        <form method="post">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="text" name="apellido" placeholder="Apellido" required>
            <input type="email" name="correo" placeholder="Correo" required>
            <input type="password" name="contrasena" placeholder="Contraseña" required>
            <input type="text" name="telefono" placeholder="Teléfono" required>
            <select name="rol" required>
                <option value="Cliente">Cliente</option>
                <option value="Administrador">Administrador</option>
            </select>
            <button type="submit" name="agregar">Agregar</button>
        </form>
    </div>

    <!-- Tabla de usuarios -->
    <h2>Lista de Usuarios</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Nombre</th><th>Apellido</th><th>Correo</th><th>Teléfono</th><th>Rol</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_usuario'] ?></td>
                        <td><?= $row['nombre'] ?></td>
                        <td><?= $row['apellido'] ?></td>
                        <td><?= $row['correo'] ?></td>
                        <td><?= $row['telefono'] ?></td>
                        <td><?= $row['rol'] ?></td>
                        <td class="acciones">
                            <form method="post" class="form-inline">
                                <input type="hidden" name="id" value="<?= $row['id_usuario'] ?>">
                                <input type="text" name="nombre" value="<?= $row['nombre'] ?>" required>
                                <input type="text" name="apellido" value="<?= $row['apellido'] ?>" required>
                                <input type="email" name="correo" value="<?= $row['correo'] ?>" required>
                                <input type="text" name="telefono" value="<?= $row['telefono'] ?>" required>
                                <select name="rol" required>
                                    <option value="Cliente" <?= $row['rol'] == 'Cliente' ? 'selected' : '' ?>>Cliente</option>
                                    <option value="Administrador" <?= $row['rol'] == 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                                </select>
                                <button type="submit" name="editar">Guardar</button>
                            </form>
                            <a href="usuarios.php?eliminar=<?= $row['id_usuario'] ?>" onclick="return confirm('¿Eliminar este usuario?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">No hay usuarios registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
