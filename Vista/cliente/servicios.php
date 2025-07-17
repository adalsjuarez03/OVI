<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cliente') {
    header("Location: ../../index.php");
    exit();
}
require_once('../../Modelo/conexion.php');
$pdo = Conexion::conectar();

// Modificar la consulta para asegurarte de que incluya la columna imagen
$servicios = $pdo->query("SELECT s.*, t.Nombre AS Tipo, s.imagen 
                         FROM servicios s 
                         JOIN tiposervicios t ON s.Id_TipoServicios = t.Id_TipoServicios")
                 ->fetchAll(PDO::FETCH_ASSOC);

// Procesar reserva de servicio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservar_servicio'])) {
    $idCliente = $_SESSION['usuario_id'];
    $idServicio = $_POST['id_servicio'];
    $numPersonas = $_POST['num_personas'];
    $estatus = 'pendiente';
    $fecha = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("SELECT Costo FROM servicios WHERE Id_Servicios = ?");
    $stmt->execute([$idServicio]);
    $servicio = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($servicio) {
        $precioTotal = $servicio['Costo'] * $numPersonas;
        $stmt = $pdo->prepare("INSERT INTO reserva (Fecha, Pasajeros, Estatus, Precio, Id_Servicios, Id_Cliente) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$fecha, $numPersonas, $estatus, $precioTotal, $idServicio, $idCliente]);
        echo "<div class='alert alert-success'>¡Servicio reservado exitosamente!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios Turísticos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('clientes_nav.php'); ?>
    
    <div class="container mt-4">
        <h1 class="text-center mb-4">Nuestros Servicios Turísticos</h1>
        
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($servicios as $servicio): ?>
                <div class="col">
                    <div class="card h-100">
                        <?php if(!empty($servicio['imagen']) && file_exists("../../" . $servicio['imagen'])): ?>
                            <img src="../../<?= htmlspecialchars($servicio['imagen']) ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($servicio['Nombre']) ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                Sin imagen
                            </div>
                        <?php endif; ?>
                        
                        <!-- Agregar para depuración -->
                        <?php
                        if(!empty($servicio['imagen'])) {
                            echo "<!-- Ruta de imagen: " . $servicio['imagen'] . " -->";
                            echo "<!-- Ruta completa: " . "../../" . $servicio['imagen'] . " -->";
                        }
                        ?>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($servicio['Nombre']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($servicio['Descripcion']) ?></p>
                            <p class="card-text">
                                <strong>Precio:</strong> $<?= htmlspecialchars($servicio['Costo']) ?><br>
                                <strong>Tipo:</strong> <?= htmlspecialchars($servicio['Tipo']) ?>
                            </p>
                            
                            <form action="servicios.php" method="POST">
                                <input type="hidden" name="id_servicio" value="<?= $servicio['Id_Servicios'] ?>">
                                <div class="mb-3">
                                    <input type="number" 
                                           name="num_personas" 
                                           class="form-control" 
                                           placeholder="Número de personas" 
                                           min="1" 
                                           required>
                                </div>
                                <button type="submit" 
                                        name="reservar_servicio" 
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
