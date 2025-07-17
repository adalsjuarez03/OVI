<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'administrador') {
    header("Location: ../../index.php");
    exit();
}
require_once('../../Modelo/conexion.php');
$pdo = Conexion::conectar();

// Agregar, actualizar y eliminar servicios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['agregar_servicio'])) {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $costo = $_POST['costo'];
        $idTipoServicio = $_POST['id_tipo_servicio'];
        
        // Procesar la imagen
        $imagen = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $directorio_destino = "../../Vista/img/";
            if (!is_dir($directorio_destino)) {
                mkdir($directorio_destino, 0775, true);
            }
            $nombre_archivo = uniqid() . "_" . basename($_FILES['imagen']['name']);
            $ruta_archivo = $directorio_destino . $nombre_archivo;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_archivo)) {
                $imagen = 'Vista/img/' . $nombre_archivo;
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO servicios (Nombre, Descripcion, Costo, Id_TipoServicios, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $costo, $idTipoServicio, $imagen]);
        header("Location: servicios.php");
        exit();
    } elseif (isset($_POST['modificar_servicio'])) {
        $id = $_POST['id_servicio'];
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $costo = $_POST['costo'];
        $idTipoServicio = $_POST['id_tipo_servicio'];
        
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $directorio_destino = "../../Vista/img/";
            if (!is_dir($directorio_destino)) {
                mkdir($directorio_destino, 0775, true);
            }
            $nombre_archivo = uniqid() . "_" . basename($_FILES['imagen']['name']);
            $ruta_archivo = $directorio_destino . $nombre_archivo;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_archivo)) {
                $imagen = 'Vista/img/' . $nombre_archivo;
                $stmt = $pdo->prepare("UPDATE servicios SET Nombre = ?, Descripcion = ?, Costo = ?, Id_TipoServicios = ?, imagen = ? WHERE Id_Servicios = ?");
                $stmt->execute([$nombre, $descripcion, $costo, $idTipoServicio, $imagen, $id]);
            }
        } else {
            $stmt = $pdo->prepare("UPDATE servicios SET Nombre = ?, Descripcion = ?, Costo = ?, Id_TipoServicios = ? WHERE Id_Servicios = ?");
            $stmt->execute([$nombre, $descripcion, $costo, $idTipoServicio, $id]);
        }
        header("Location: servicios.php");
        exit();
    }
}

// Eliminar servicio
if (isset($_GET['eliminar'])) {
    $idServicio = $_GET['eliminar'];
    try {
        $stmt = $pdo->prepare("DELETE FROM servicios WHERE Id_Servicios = ?");
        $stmt->execute([$idServicio]);
        header("Location: servicios.php");
        exit();
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: No se pudo eliminar el servicio.</div>";
    }
}

// Obtener servicio para modificar
$servicioModificar = null;
if (isset($_GET['modificar'])) {
    $id = $_GET['modificar'];
    $stmt = $pdo->prepare("SELECT * FROM servicios WHERE Id_Servicios = ?");
    $stmt->execute([$id]);
    $servicioModificar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener servicios y tipos de servicios
$servicios = $pdo->query("SELECT s.*, t.Nombre AS Tipo FROM servicios s JOIN tiposervicios t ON s.Id_TipoServicios = t.Id_TipoServicios")->fetchAll(PDO::FETCH_ASSOC);
$tiposServicios = $pdo->query("SELECT * FROM tiposervicios")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Servicios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_nav.php'); ?>
<div class="container mt-4">
    <center><h1>Gestión de Servicios</h1></center>
    
    <!-- Tabla de servicios -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Costo</th>
                <th>Tipo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($servicios as $servicio): ?>
                <tr>
                    <td><?= htmlspecialchars($servicio['Id_Servicios']) ?></td>
                    <td>
                        <?php if ($servicio['imagen'] && file_exists("../../" . $servicio['imagen'])): ?>
                            <img src="../../<?= htmlspecialchars($servicio['imagen']) ?>" 
                                 alt="Imagen del servicio" 
                                 style="max-width: 100px;">
                        <?php else: ?>
                            Sin imagen
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($servicio['Nombre']) ?></td>
                    <td><?= htmlspecialchars($servicio['Descripcion']) ?></td>
                    <td><?= htmlspecialchars($servicio['Costo']) ?></td>
                    <td><?= htmlspecialchars($servicio['Tipo']) ?></td>
                    <td>
                        <a href="servicios.php?modificar=<?= $servicio['Id_Servicios'] ?>" 
                           class="btn btn-warning btn-sm">Modificar</a>
                        <a href="servicios.php?eliminar=<?= $servicio['Id_Servicios'] ?>" 
                           class="btn btn-danger btn-sm">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Formulario para agregar/modificar servicio -->
    <center><h2><?= $servicioModificar ? 'Modificar Servicio' : 'Agregar Servicio' ?></h2></center>
    <form action="servicios.php" method="POST" enctype="multipart/form-data">
        <?php if ($servicioModificar): ?>
            <input type="hidden" name="id_servicio" value="<?= htmlspecialchars($servicioModificar['Id_Servicios']) ?>">
        <?php endif; ?>
        
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" 
                   value="<?= htmlspecialchars($servicioModificar['Nombre'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" required><?= htmlspecialchars($servicioModificar['Descripcion'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label for="costo" class="form-label">Costo</label>
            <input type="number" class="form-control" id="costo" name="costo" step="0.01" 
                   value="<?= htmlspecialchars($servicioModificar['Costo'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="id_tipo_servicio" class="form-label">Tipo de Servicio</label>
            <select class="form-control" id="id_tipo_servicio" name="id_tipo_servicio" required>
                <?php foreach ($tiposServicios as $tipo): ?>
                    <option value="<?= $tipo['Id_TipoServicios'] ?>" 
                            <?= isset($servicioModificar['Id_TipoServicios']) && $servicioModificar['Id_TipoServicios'] == $tipo['Id_TipoServicios'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tipo['Nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen del Servicio</label>
            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
            <?php if ($servicioModificar && $servicioModificar['imagen']): ?>
                <small class="form-text text-muted">Dejar vacío para mantener la imagen actual</small>
            <?php endif; ?>
        </div>
        <button type="submit" name="<?= $servicioModificar ? 'modificar_servicio' : 'agregar_servicio' ?>" 
                class="btn btn-primary">
            <?= $servicioModificar ? 'Modificar Servicio' : 'Agregar Servicio' ?>
        </button>
    </form>
    <br>
    <br>
</div>
<?php include('../../Vista/layout/footer.php'); ?>
</body>
</html>

