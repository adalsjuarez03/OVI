<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header("Location: ../../index.php");
    exit();
}
require_once('../../Modelo/conexion.php');
$pdo = Conexion::conectar();

// Obtener paquetes
$stmt = $pdo->query("SELECT * FROM paquete");
$paquetes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Registrar reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservar'])) {
    $idCliente = $_SESSION['usuario_id'];
    $idPaquete = $_POST['id_paquete'];
    $numPersonas = $_POST['num_personas'];
    $estatus = 'pendiente';
    $fecha = date('Y-m-d H:i:s');

    $stmtPaquete = $pdo->prepare("SELECT Costo FROM paquete WHERE Id_Paquete = ?");
    $stmtPaquete->execute([$idPaquete]);
    $paquete = $stmtPaquete->fetch(PDO::FETCH_ASSOC);

    if ($paquete) {
        $precioTotal = $paquete['Costo'] * $numPersonas;
        $stmt = $pdo->prepare("INSERT INTO reserva (Fecha, Pasajeros, Estatus, Precio, Id_Paquete, Id_Cliente) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$fecha, $numPersonas, $estatus, $precioTotal, $idPaquete, $idCliente]);
        echo "<div class='alert alert-success'>¡Reserva realizada exitosamente!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paquetes Turísticos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('clientes_nav.php'); ?>
    
    <div class="container mt-4">
        <h1 class="text-center mb-4">Nuestros Paquetes Turísticos</h1>
        
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($paquetes as $paquete): ?>
                <div class="col">
                    <div class="card h-100">
                        <?php if($paquete['imagen']): ?>
                            <img src="../../<?= htmlspecialchars($paquete['imagen']) ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($paquete['Nombre']) ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                Sin imagen
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($paquete['Nombre']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($paquete['Descripcion']) ?></p>
                            <p class="card-text">
                                <strong>Precio:</strong> $<?= htmlspecialchars($paquete['Costo']) ?>
                            </p>
                            
                            <form action="paquetes.php" method="POST">
                                <input type="hidden" name="id_paquete" value="<?= $paquete['Id_Paquete'] ?>">
                                <div class="mb-3">
                                    <input type="number" 
                                           name="num_personas" 
                                           class="form-control" 
                                           placeholder="Número de personas" 
                                           min="1" 
                                           required>
                                </div>
                                <button type="submit" 
                                        name="reservar" 
                                        class="btn btn-primary w-100">Reservar Ahora</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <br>
    <br>

    <?php include('../../Vista/layout/footer.php'); ?>
</body>
</html>
