<?php
require_once '../Modelo/Conexion.php';
session_start();

if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo "No autorizado.";
    exit();
}

$descripcion = $_POST['descripcion'] ?? '';
$archivo = $_FILES['archivo'] ?? null;

if (empty($descripcion)) {
    http_response_code(400);
    echo "La descripción es obligatoria.";
    exit();
}

try {
    $conexion = Conexion::conectar();

    // Contar servicios existentes para generar número único
    $stmt = $conexion->query("SELECT COUNT(*) as total FROM Servicios");
    $row = $stmt->fetch_assoc();
    $contador = $row['total'] + 1;
    $numeroServicio = sprintf("SEyT-UI-%04d", $contador);

    // Manejo del archivo (si se carga uno)
    $rutaArchivo = "";
    if ($archivo && $archivo['error'] === UPLOAD_ERR_OK) {
        $directorioDestino = '../Archivos/';
        if (!file_exists($directorioDestino)) {
            mkdir($directorioDestino, 0777, true);
        }

        $nombreArchivo = time() . "_" . basename($archivo['name']);
        $rutaArchivo = $directorioDestino . $nombreArchivo;
        move_uploaded_file($archivo['tmp_name'], $rutaArchivo);
    }

    // Insertar en base de datos
    $stmt = $conexion->prepare("INSERT INTO Servicios (Estatus, Numero_servicio, Descripcion, Turnado, Fecha_solicitud, Acciones) VALUES (?, ?, ?, ?, NOW(), ?)");
    $estatus = "no-asignado";
    $turnado = "Sin asignar";
    $acciones = $rutaArchivo !== "" ? "Archivo adjunto: $nombreArchivo" : "";

    $stmt->bind_param("sssss", $estatus, $numeroServicio, $descripcion, $turnado, $acciones);
    $stmt->execute();

    echo "ok";
} catch (Exception $e) {
    http_response_code(500);
    echo "Error al guardar: " . $e->getMessage();
}
?>
