<?php
session_start();
require_once '../Modelo/Conexion.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

if (!isset($_GET['archivo']) || !isset($_GET['id'])) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Parámetros faltantes']);
    exit;
}

$archivo_ruta = $_GET['archivo'];
$id_servicio = intval($_GET['id']);

// Verificar que el archivo existe en la base de datos
$conexion = Conexion::conectar();
$stmt = $conexion->prepare("SELECT Archivo_ruta, Archivo_nombre FROM Servicios WHERE Id_servicio = ? AND Archivo_ruta = ?");
$stmt->bind_param("is", $id_servicio, $archivo_ruta);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Archivo no encontrado en la base de datos']);
    exit;
}

$row = $resultado->fetch_assoc();
$archivo_completo = '../' . $row['Archivo_ruta'];
$nombre_original = $row['Archivo_nombre'];

// Verificar que el archivo existe físicamente
if (!file_exists($archivo_completo)) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Archivo no encontrado en el servidor']);
    exit;
}

// Obtener información del archivo
$tipo_mime = mime_content_type($archivo_completo);
$tamaño = filesize($archivo_completo);

// Configurar headers para la descarga
header('Content-Type: ' . $tipo_mime);
header('Content-Length: ' . $tamaño);
header('Content-Disposition: attachment; filename="' . $nombre_original . '"');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Expires: 0');

// Leer y enviar el archivo
readfile($archivo_completo);
exit;
?>