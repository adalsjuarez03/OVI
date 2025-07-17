<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'administrador') {
    header("Location: ../../index.php");
    exit();
}
require_once('../../Modelo/conexion.php');
$pdo = Conexion::conectar();

// Agregar paquete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['agregar_paquete'])) {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $costo = $_POST['costo'];
        
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
        
        $stmt = $pdo->prepare("INSERT INTO paquete (Nombre, Descripcion, Costo, imagen) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $costo, $imagen]);
        header("Location: paquetes.php");
        exit();
    } elseif (isset($_POST['modificar_paquete'])) {
        $id = $_POST['id_paquete'];
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $costo = $_POST['costo'];
        
        // Procesar la imagen si se subió una nueva
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $directorio_destino = "../../Vista/img/";
            if (!is_dir($directorio_destino)) {
                mkdir($directorio_destino, 0775, true);
            }
            $nombre_archivo = uniqid() . "_" . basename($_FILES['imagen']['name']);
            $ruta_archivo = $directorio_destino . $nombre_archivo;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_archivo)) {
                $imagen = 'Vista/img/' . $nombre_archivo;
                $stmt = $pdo->prepare("UPDATE paquete SET Nombre = ?, Descripcion = ?, Costo = ?, imagen = ? WHERE Id_Paquete = ?");
                $stmt->execute([$nombre, $descripcion, $costo, $imagen, $id]);
            }
        } else {
            $stmt = $pdo->prepare("UPDATE paquete SET Nombre = ?, Descripcion = ?, Costo = ? WHERE Id_Paquete = ?");
            $stmt->execute([$nombre, $descripcion, $costo, $id]);
        }
        
        header("Location: paquetes.php");
        exit();
    }
}

// Eliminar paquete
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $stmt = $pdo->prepare("DELETE FROM paquete WHERE Id_Paquete = ?");
    $stmt->execute([$id]);
    header("Location: paquetes.php");
    exit();
}

// Obtener paquete para modificar
$paqueteModificar = null;
if (isset($_GET['modificar'])) {
    $id = $_GET['modificar'];
    $stmt = $pdo->prepare("SELECT * FROM paquete WHERE Id_Paquete = ?");
    $stmt->execute([$id]);
    $paqueteModificar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener paquetes
$stmt = $pdo->query("SELECT * FROM paquete");
$paquetes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Paquetes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_nav.php'); ?>
<div class="container mt-4">
    <center><h1>Gestión de Paquetes</h1></center>
    
    <!-- Tabla de paquetes -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Costo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($paquetes as $paquete): ?>
                <tr>
                    <td><?= htmlspecialchars($paquete['Id_Paquete']) ?></td>
                    <td>
                        <?php if ($paquete['imagen'] && file_exists("../../" . $paquete['imagen'])): ?>
                            <img src="../../<?= htmlspecialchars($paquete['imagen']) ?>" 
                                 alt="Imagen del paquete" 
                                 style="max-width: 100px;">
                        <?php else: ?>
                            Sin imagen
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($paquete['Nombre']) ?></td>
                    <td><?= htmlspecialchars($paquete['Descripcion']) ?></td>
                    <td><?= htmlspecialchars($paquete['Costo']) ?></td>
                    <td>
                        <a href="paquetes.php?modificar=<?= $paquete['Id_Paquete'] ?>" 
                           class="btn btn-warning btn-sm">Modificar</a>
                        <a href="paquetes.php?eliminar=<?= $paquete['Id_Paquete'] ?>" 
                           class="btn btn-danger btn-sm">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Formulario para agregar/modificar paquete -->
    <center><h2><?= $paqueteModificar ? 'Modificar Paquete' : 'Agregar Paquete' ?></h2></center>
    <form action="paquetes.php" method="POST" enctype="multipart/form-data" class="mt-4">
        <?php if ($paqueteModificar): ?>
            <input type="hidden" name="id_paquete" value="<?= htmlspecialchars($paqueteModificar['Id_Paquete']) ?>">
        <?php endif; ?>
        
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Paquete</label>
            <input type="text" class="form-control" id="nombre" name="nombre" 
                   value="<?= htmlspecialchars($paqueteModificar['Nombre'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?= htmlspecialchars($paqueteModificar['Descripcion'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label for="costo" class="form-label">Costo</label>
            <input type="number" class="form-control" id="costo" name="costo" step="0.01" 
                   value="<?= htmlspecialchars($paqueteModificar['Costo'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen del Paquete</label>
            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
            <?php if ($paqueteModificar && $paqueteModificar['imagen']): ?>
                <small class="form-text text-muted">Dejar vacío para mantener la imagen actual</small>
            <?php endif; ?>
        </div>
        <button type="submit" name="<?= $paqueteModificar ? 'modificar_paquete' : 'agregar_paquete' ?>" 
                class="btn btn-primary">
            <?= $paqueteModificar ? 'Modificar Paquete' : 'Agregar Paquete' ?>
        </button>
    </form>
</div>
</body>
</html>
