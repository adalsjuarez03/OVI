<?php
require_once '../../Modelo/Conexion.php';
$conn = Conexion::conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_servicio'] ?? null;
    $descripcion = $_POST['descripcion'] ?? '';

    if (!$id || trim($descripcion) === '') {
        echo json_encode(['error' => 'Datos incompletos']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE Servicios SET Descripcion = ? WHERE Id_servicio = ?");
    $stmt->bind_param("si", $descripcion, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'No se pudo actualizar']);
    }

    $stmt->close();
    $conn->close();
}
