<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header("Location: ../../index.php");
    exit();
}

require_once('../../Modelo/conexion.php');
$pdo = Conexion::conectar();

// Obtener las reservas del cliente actual (paquetes y servicios)
$idCliente = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("
    SELECT 
        r.Id_Reserva,
        r.Fecha,
        r.Pasajeros,
        r.Precio,
        r.Estatus,
        p.Nombre AS Paquete,
        s.Nombre AS Servicio
    FROM reserva r
    LEFT JOIN paquete p ON r.Id_Paquete = p.Id_Paquete
    LEFT JOIN servicios s ON r.Id_Servicios = s.Id_Servicios
    WHERE r.Id_Cliente = ?
    ORDER BY r.Fecha DESC
");
$stmt->execute([$idCliente]);
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('clientes_nav.php'); ?>
<div class="container mt-4">
    <h1>Mis Reservas</h1>

    <!-- Tabla de reservas -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID Reserva</th>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Nombre</th>
                <th>Pasajeros</th>
                <th>Precio</th>
                <th>Estatus</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservas as $reserva): ?>
                <tr>
                    <td><?= htmlspecialchars($reserva['Id_Reserva']) ?></td>
                    <td><?= htmlspecialchars($reserva['Fecha']) ?></td>
                    <td><?= $reserva['Paquete'] ? 'Paquete' : 'Servicio' ?></td>
                    <td><?= htmlspecialchars($reserva['Paquete'] ?? $reserva['Servicio']) ?></td>
                    <td><?= htmlspecialchars($reserva['Pasajeros']) ?></td>
                    <td><?= htmlspecialchars($reserva['Precio']) ?></td>
                    <td><?= htmlspecialchars($reserva['Estatus']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <br>
</div>
 <!-- Incluir el footer -->
 <?php include('../../Vista/layout/footer.php'); ?>
</body>
</html>