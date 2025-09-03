<?php
session_start();
require_once '../../Modelo/Conexion.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Verificar que existe el rol y que es un rol válido
if (!isset($_SESSION['rol'])) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Rol no definido']);
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
$id_usuario = $_SESSION['usuario'];
$rol_usuario = $_SESSION['rol'];

// Preparar consulta según el rol del usuario
$conexion = Conexion::conectar();

if ($rol_usuario === 'cliente') {
    // CAMBIO: Los clientes pueden ver cualquier archivo (no solo los suyos)
    $stmt = $conexion->prepare("SELECT Archivo_ruta, Archivo_nombre FROM Servicios WHERE Id_servicio = ? AND Archivo_ruta = ?");
    $stmt->bind_param("is", $id_servicio, $archivo_ruta);
} elseif ($rol_usuario === 'administrador' || $rol_usuario === 'admin') {
    // Los administradores pueden ver cualquier archivo
    $stmt = $conexion->prepare("SELECT Archivo_ruta, Archivo_nombre FROM Servicios WHERE Id_servicio = ? AND Archivo_ruta = ?");
    $stmt->bind_param("is", $id_servicio, $archivo_ruta);
} else {
    // Rol no autorizado
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Rol no autorizado para ver archivos']);
    exit;
}

$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Archivo no encontrado en la base de datos']);
    exit;
}

$row = $resultado->fetch_assoc();
$archivo_completo = '../../' . $row['Archivo_ruta'];
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

// Configurar headers para VISUALIZAR (no descargar)
header('Content-Type: ' . $tipo_mime);
header('Content-Length: ' . $tamaño);
// NO usar Content-Disposition: attachment para que se abra en el navegador
header('Content-Disposition: inline; filename="' . $nombre_original . '"');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Expires: 0');

// Leer y enviar el archivo
readfile($archivo_completo);

// Cerrar conexiones
$stmt->close();
$conexion->close();
exit;
?>