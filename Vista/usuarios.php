<?php
require_once '../Modelo/Conexion.php';
$conn = Conexion::conectar();
// Conexion

// Registrar
if (isset($_POST['registrar'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $rol = $_POST['rol'];
  
}

// Editar
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $rol = $_POST['rol'];
    
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

    <!-- Íconos Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f7fa;
            padding: 30px;
            color: #333;
        }

        h2 {
            margin-bottom: 20px;
        }

        .form-container, table {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 30px;
        }

        input, select {
            padding: 10px;
            width: 100%;
            margin-top: 6px;
            margin-bottom: 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }

        label {
            font-weight: bold;
            font-size: 13px;
        }

        button, .icon-button {
            border: none;
            cursor: pointer;
            padding: 10px 16px;
            font-size: 14px;
            border-radius: 8px;
        }

        .guardar {
            background-color: #4e73df;
            color: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #f1f3f9;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            color: #555;
        }

        td {
            padding: 14px 12px;
            border-bottom: 1px solid #eee;
        }

        .acciones {
            display: flex;
            gap: 10px;
            justify-content: start;
        }

        .acciones button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .descargar {
            background: #e8fce8;
            color: #2ecc71;
        }

        .editar {
            background: #e6f0ff;
            color: #2980b9;
        }

        .eliminar {
            background: #ffe6e6;
            color: #e74c3c;
        }

        dialog {
            padding: 20px;
            border: none;
            border-radius: 12px;
            width: 400px;
            max-width: 90%;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .modal-actions button {
            padding: 8px 14px;
            border-radius: 6px;
        }

        .modal-actions .guardar {
            background-color: #4e73df;
            color: white;
        }

        .modal-actions .cancelar {
            background-color: #ccc;
            color: #333;
        }
    </style>
</head>
<body>

    <h2>Gestión de Usuarios</h2>

    <!-- FORMULARIO REGISTRO -->
    <div class="form-container">
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
                <option value="Administrador">Administrador</option>
            </select>

            <button class="guardar" type="submit" name="registrar">Registrar</button>
        </form>
    </div>

    <!-- TABLA USUARIOS -->
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
                    <td><?= $row['nombre'] . " " . $row['apellido'] ?></td>
                    <td><?= $row['correo'] ?></td>
                    <td><?= $row['telefono'] ?></td>
                    <td><?= $row['rol'] ?></td>
                    <td>
                        <div class="acciones">
                            <button class="descargar" title="Descargar"><i class="fas fa-download"></i></button>
                            <button class="editar" onclick="abrirEditar(<?= htmlspecialchars(json_encode($row)) ?>)" title="Editar"><i class="fas fa-pen"></i></button>
                            <button class="eliminar" onclick="abrirEliminar(<?= $row['id_usuario'] ?>)" title="Eliminar"><i class="fas fa-trash"></i></button>
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

            <label for="edit-nombre">Nombre</label>
            <input type="text" name="nombre" id="edit-nombre" required>

            <label for="edit-apellido">Apellido</label>
            <input type="text" name="apellido" id="edit-apellido" required>

            <label for="edit-correo">Correo</label>
            <input type="email" name="correo" id="edit-correo" required>

            <label for="edit-telefono">Teléfono</label>
            <input type="text" name="telefono" id="edit-telefono" required>

            <label for="edit-rol">Rol</label>
            <select name="rol" id="edit-rol" required>
                <option value="Cliente">Cliente</option>
                <option value="Administrador">Administrador</option>
            </select>

            <div class="modal-actions">
                <button type="button" class="cancelar" onclick="cerrarEditar()">Cancelar</button>
                <button type="submit" class="guardar" name="editar">Guardar</button>
            </div>
        </form>
    </dialog>

    <!-- MODAL ELIMINAR -->
    <dialog id="modalEliminar">
        <h3>¿Estás seguro de eliminar este usuario?</h3>
        <form method="get">
            <input type="hidden" name="eliminar" id="delete-id">
            <div class="modal-actions">
                <button type="button" class="cancelar" onclick="cerrarEliminar()">Cancelar</button>
                <button type="submit" class="guardar">Eliminar</button>
            </div>
        </form>
    </dialog>

    <script>
        function abrirEditar(datos) {
            document.getElementById('edit-id').value = datos.id_usuario;
            document.getElementById('edit-nombre').value = datos.nombre;
            document.getElementById('edit-apellido').value = datos.apellido;
            document.getElementById('edit-correo').value = datos.correo;
            document.getElementById('edit-telefono').value = datos.telefono;
            document.getElementById('edit-rol').value = datos.rol;
            document.getElementById('modalEditar').showModal();
        }

        function cerrarEditar() {
            document.getElementById('modalEditar').close();
        }

        function abrirEliminar(id) {
            document.getElementById('delete-id').value = id;
            document.getElementById('modalEliminar').showModal();
        }

        function cerrarEliminar() {
            document.getElementById('modalEliminar').close();
        }
    </script>

</body>
</html>
