<?php
session_start();
require_once '../../Modelo/Conexion.php';
$conn = Conexion::conectar();

header('Content-Type: application/json');

// Obtener datos POST
$id_servicio = $_POST['id_servicio'] ?? null;
$mensaje = trim($_POST['mensaje'] ?? '');
$emisor = $_POST['emisor'] ?? ($_SESSION['rol'] ?? null);

// Validar entrada
if (!$id_servicio || empty($mensaje) || empty($emisor)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Datos incompletos',
        'debug' => [
            'id_servicio' => $id_servicio,
            'mensaje' => $mensaje,
            'emisor' => $emisor
        ]
    ]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO Mensajes (Id_servicio, Emisor, Mensaje) VALUES (?, ?, ?)");
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error en prepare()',
        'mysqli_error' => $conn->error
    ]);
    exit;
}

$stmt->bind_param("iss", $id_servicio, $emisor, $mensaje);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al ejecutar',
        'mysqli_error' => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
