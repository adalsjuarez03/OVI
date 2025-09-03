<?php
session_start();
require_once '../../Modelo/Conexion.php';
$conn = Conexion::conectar();

header('Content-Type: application/json');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

$id_usuario = $_SESSION['usuario'];

// CAMBIO: Obtener TODOS los servicios (no solo del cliente actual)
// Esto permite que los clientes vean tanto sus servicios como los que crea el admin
$stmt = $conn->prepare("SELECT Id_servicio, Estatus, Numero_servicio, Titulo, Descripcion, Turnado, Fecha_solicitud, Archivo_ruta, Archivo_nombre FROM Servicios ORDER BY Fecha_solicitud DESC");
$stmt->execute();
$result = $stmt->get_result();

$servicios = [];
while ($row = $result->fetch_assoc()) {
    $servicios[] = [
        'id' => $row['Id_servicio'],
        'estatus' => $row['Estatus'],
        'numero' => $row['Numero_servicio'],
        'titulo' => $row['Titulo'],
        'descripcion' => $row['Descripcion'],
        'turnado' => $row['Turnado'],
        'fecha' => $row['Fecha_solicitud'],
        'archivo_ruta' => $row['Archivo_ruta'],
        'archivo_nombre' => $row['Archivo_nombre']
    ];
}

$stmt->close();
echo json_encode($servicios);
?>