<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'administrador') {
    header("Location: ../../index.php");
    exit();
}
require_once('../../Modelo/conexion.php');
$pdo = Conexion::conectar();

// Manejar acciones (Agregar, Actualizar, Eliminar clientes)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['agregar_cliente'])) {
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $telefono = $_POST['telefono'];
        $correo = $_POST['correo'];
        $curp = $_POST['curp'];
        $contraseña = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);
        $rol = $_POST['rol'];

        $stmt = $pdo->prepare("INSERT INTO cliente (Nombre, Apellido, Telefono, Correo, CURP, Contraseña, Rol) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$nombre, $apellido, $telefono, $correo, $curp, $contraseña, $rol])) {
            echo "<div class='alert alert-success'>Cliente agregado exitosamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al agregar el cliente.</div>";
        }
    } elseif (isset($_POST['modificar_cliente'])) {
        $idCliente = $_POST['id_cliente'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $telefono = $_POST['telefono'];
        $correo = $_POST['correo'];
        $curp = $_POST['curp'];
        $rol = $_POST['rol'];

        // Si se proporciona una nueva contraseña, actualizarla
        if (!empty($_POST['contraseña'])) {
            $contraseña = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE cliente SET Nombre = ?, Apellido = ?, Telefono = ?, Correo = ?, CURP = ?, Contraseña = ?, Rol = ? WHERE Id_Cliente = ?");
            $stmt->execute([$nombre, $apellido, $telefono, $correo, $curp, $contraseña, $rol, $idCliente]);
        } else {
            $stmt = $pdo->prepare("UPDATE cliente SET Nombre = ?, Apellido = ?, Telefono = ?, Correo = ?, CURP = ?, Rol = ? WHERE Id_Cliente = ?");
            $stmt->execute([$nombre, $apellido, $telefono, $correo, $curp, $rol, $idCliente]);
        }
        
        header("Location: datosCliente.php");
        exit();
    }
}

// Eliminar cliente
if (isset($_GET['eliminar'])) {
    $idCliente = $_GET['eliminar'];
    try {
        $stmt = $pdo->prepare("DELETE FROM cliente WHERE Id_Cliente = ?");
        $stmt->execute([$idCliente]);
        header("Location: datosCliente.php");
        exit();
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: No se puede eliminar el cliente porque tiene reservas asociadas.</div>";
    }
}

// Obtener cliente para modificar
$clienteModificar = null;
if (isset($_GET['modificar'])) {
    $idCliente = $_GET['modificar'];
    $stmt = $pdo->prepare("SELECT * FROM cliente WHERE Id_Cliente = ?");
    $stmt->execute([$idCliente]);
    $clienteModificar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener todos los clientes
$clientes = $pdo->query("SELECT * FROM cliente ORDER BY Nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_nav.php'); ?>
<div class="container mt-4">
    <center><h1>Gestión de Clientes</h1></center>

    <!-- Tabla de clientes -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>CURP</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?= htmlspecialchars($cliente['Id_Cliente']) ?></td>
                    <td><?= htmlspecialchars($cliente['Nombre']) ?></td>
                    <td><?= htmlspecialchars($cliente['Apellido']) ?></td>
                    <td><?= htmlspecialchars($cliente['Telefono']) ?></td>
                    <td><?= htmlspecialchars($cliente['Correo']) ?></td>
                    <td><?= htmlspecialchars($cliente['CURP']) ?></td>
                    <td><?= htmlspecialchars($cliente['Rol']) ?></td>
                    <td>
                        <a href="datosCliente.php?modificar=<?= $cliente['Id_Cliente'] ?>" 
                           class="btn btn-warning btn-sm">Modificar</a>
                        <a href="datosCliente.php?eliminar=<?= $cliente['Id_Cliente'] ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('¿Está seguro de eliminar este cliente?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Formulario para agregar/modificar cliente -->
    <div class="mt-4">
        <center><h2><?= $clienteModificar ? 'Modificar Cliente' : 'Agregar Cliente' ?></h2></center>
        <form action="datosCliente.php" method="POST">
            <?php if ($clienteModificar): ?>
                <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($clienteModificar['Id_Cliente']) ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" 
                       value="<?= htmlspecialchars($clienteModificar['Nombre'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" 
                       value="<?= htmlspecialchars($clienteModificar['Apellido'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" 
                       value="<?= htmlspecialchars($clienteModificar['Telefono'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label for="correo" class="form-label">Correo</label>
                <input type="email" class="form-control" id="correo" name="correo" 
                       value="<?= htmlspecialchars($clienteModificar['Correo'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label for="curp" class="form-label">CURP</label>
                <input type="text" class="form-control" id="curp" name="curp" 
                       value="<?= htmlspecialchars($clienteModificar['CURP'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label for="contraseña" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="contraseña" name="contraseña" 
                       <?= $clienteModificar ? '' : 'required' ?>>
                <?php if ($clienteModificar): ?>
                    <small class="form-text text-muted">Dejar en blanco para mantener la contraseña actual</small>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="rol" class="form-label">Rol</label>
                <select class="form-control" id="rol" name="rol" required>
                    <option value="cliente" <?= isset($clienteModificar['Rol']) && $clienteModificar['Rol'] === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                    <option value="administrador" <?= isset($clienteModificar['Rol']) && $clienteModificar['Rol'] === 'administrador' ? 'selected' : '' ?>>Administrador</option>
                </select>
            </div>

            <button type="submit" name="<?= $clienteModificar ? 'modificar_cliente' : 'agregar_cliente' ?>" 
                    class="btn btn-primary">
                <?= $clienteModificar ? 'Guardar Cambios' : 'Agregar Cliente' ?>
            </button>
            <?php if ($clienteModificar): ?>
                <a href="datosCliente.php" class="btn btn-secondary">Cancelar</a>
            <?php endif; ?>
        </form>
    </div>
</div>
<br>
<br>
<?php include('../../Vista/layout/footer.php'); ?>
</body>
</html>
