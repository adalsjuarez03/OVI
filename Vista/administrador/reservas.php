<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'administrador') {
    header("Location: ../../index.php");
    exit();
}
require_once('../../Modelo/conexion.php');
$pdo = Conexion::conectar();

// Eliminar reserva
if (isset($_GET['eliminar'])) {
    $idReserva = $_GET['eliminar'];
    $stmt = $pdo->prepare("DELETE FROM reserva WHERE Id_Reserva = ?");
    $stmt->execute([$idReserva]);
    header("Location: reservas.php");
    exit();
}

// Modificar reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modificar_reserva'])) {
    $idReserva = $_POST['id_reserva'];
    $fecha = $_POST['fecha'];
    $pasajeros = $_POST['pasajeros'];
    $estatus = $_POST['estatus'];
    $precio = $_POST['precio'];
    $idPaquete = $_POST['id_paquete'] ?? null;
    $idServicio = $_POST['id_servicio'] ?? null;

    $stmt = $pdo->prepare("UPDATE reserva SET Fecha = ?, Pasajeros = ?, Estatus = ?, Precio = ?, Id_Paquete = ?, Id_Servicios = ? WHERE Id_Reserva = ?");
    $stmt->execute([$fecha, $pasajeros, $estatus, $precio, $idPaquete, $idServicio, $idReserva]);
    header("Location: reservas.php");
    exit();
}

// Obtener reserva para modificar
$reservaModificar = null;
if (isset($_GET['modificar'])) {
    $idReserva = $_GET['modificar'];
    $stmt = $pdo->prepare("SELECT * FROM reserva WHERE Id_Reserva = ?");
    $stmt->execute([$idReserva]);
    $reservaModificar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener todas las reservas con información relacionada
$stmt = $pdo->query("SELECT 
    r.Id_Reserva,
    r.Fecha,
    r.Pasajeros,
    r.Estatus,
    r.Precio,
    c.Nombre AS Cliente,
    c.Id_Cliente,
    p.Nombre AS Paquete,
    p.Id_Paquete,
    s.Nombre AS Servicio,
    s.Id_Servicios
FROM reserva r
JOIN cliente c ON r.Id_Cliente = c.Id_Cliente
LEFT JOIN paquete p ON r.Id_Paquete = p.Id_Paquete
LEFT JOIN servicios s ON r.Id_Servicios = s.Id_Servicios
ORDER BY r.Fecha DESC");
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener listas para los selectores
$paquetes = $pdo->query("SELECT Id_Paquete, Nombre FROM paquete")->fetchAll(PDO::FETCH_ASSOC);
$servicios = $pdo->query("SELECT Id_Servicios, Nombre FROM servicios")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_nav.php'); ?>
<div class="container mt-4">
    <center><h1>Gestión de Reservas</h1></center>

    <!-- Tabla de reservas -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Pasajeros</th>
                <th>Paquete</th>
                <th>Servicio</th>
                <th>Precio</th>
                <th>Estatus</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservas as $reserva): ?>
                <tr>
                    <td><?= htmlspecialchars($reserva['Id_Reserva']) ?></td>
                    <td><?= htmlspecialchars($reserva['Cliente']) ?></td>
                    <td><?= htmlspecialchars($reserva['Fecha']) ?></td>
                    <td><?= htmlspecialchars($reserva['Pasajeros']) ?></td>
                    <td><?= htmlspecialchars($reserva['Paquete'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($reserva['Servicio'] ?? 'N/A') ?></td>
                    <td>$<?= htmlspecialchars($reserva['Precio']) ?></td>
                    <td><?= htmlspecialchars($reserva['Estatus']) ?></td>
                    <td>
                        <a href="reservas.php?modificar=<?= $reserva['Id_Reserva'] ?>" 
                           class="btn btn-warning btn-sm">Modificar</a>
                        <a href="reservas.php?eliminar=<?= $reserva['Id_Reserva'] ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('¿Está seguro de eliminar esta reserva?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Formulario para modificar reserva -->
    <?php if ($reservaModificar): ?>
        <div class="mt-4">
            <center><h2>Modificar Reserva</h2></center>
            <form action="reservas.php" method="POST">
                <input type="hidden" name="id_reserva" value="<?= htmlspecialchars($reservaModificar['Id_Reserva']) ?>">
                
                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input type="datetime-local" class="form-control" id="fecha" name="fecha" 
                           value="<?= date('Y-m-d\TH:i', strtotime($reservaModificar['Fecha'])) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="pasajeros" class="form-label">Número de Pasajeros</label>
                    <input type="number" class="form-control" id="pasajeros" name="pasajeros" 
                           value="<?= htmlspecialchars($reservaModificar['Pasajeros']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="precio" class="form-label">Precio</label>
                    <input type="number" class="form-control" id="precio" name="precio" step="0.01"
                           value="<?= htmlspecialchars($reservaModificar['Precio']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="id_paquete" class="form-label">Paquete</label>
                    <select class="form-control" id="id_paquete" name="id_paquete">
                        <option value="">Sin paquete</option>
                        <?php foreach ($paquetes as $paquete): ?>
                            <option value="<?= $paquete['Id_Paquete'] ?>" 
                                    <?= $reservaModificar['Id_Paquete'] == $paquete['Id_Paquete'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($paquete['Nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="id_servicio" class="form-label">Servicio</label>
                    <select class="form-control" id="id_servicio" name="id_servicio">
                        <option value="">Sin servicio</option>
                        <?php foreach ($servicios as $servicio): ?>
                            <option value="<?= $servicio['Id_Servicios'] ?>" 
                                    <?= $reservaModificar['Id_Servicios'] == $servicio['Id_Servicios'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($servicio['Nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="estatus" class="form-label">Estatus</label>
                    <select class="form-control" id="estatus" name="estatus" required>
                        <option value="pendiente" <?= $reservaModificar['Estatus'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="confirmada" <?= $reservaModificar['Estatus'] == 'confirmada' ? 'selected' : '' ?>>Confirmada</option>
                        <option value="cancelada" <?= $reservaModificar['Estatus'] == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                    </select>
                </div>

                <button type="submit" name="modificar_reserva" class="btn btn-primary">Guardar Cambios</button>
                <a href="reservas.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    <?php endif; ?>
</div>
<br>
<br>
<?php include('../../Vista/layout/footer.php'); ?>
</body>
</html>
