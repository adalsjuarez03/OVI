<?php
require_once '../../Modelo/Conexion.php';
$conn = Conexion::conectar();

$id_servicio = $_GET['id_servicio'] ?? null;

if (!$id_servicio) {
    http_response_code(400);
    echo json_encode(["error" => "ID no válido"]);
    exit();
}

$stmt = $conn->prepare("SELECT Emisor AS emisor, Mensaje AS mensaje, Fecha FROM Mensajes WHERE Id_servicio = ? ORDER BY Fecha ASC");
$stmt->bind_param("i", $id_servicio);
$stmt->execute();
$result = $stmt->get_result();

$mensajes = [];
while ($row = $result->fetch_assoc()) {
    $mensajes[] = $row;
}

header('Content-Type: application/json');
echo json_encode($mensajes);
