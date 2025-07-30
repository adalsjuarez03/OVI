<?php
require_once '../../Modelo/Conexion.php';
$conexion = Conexion::conectar();

if (!isset($_POST['id_servicio'])) {
    echo json_encode(['error' => 'ID de servicio no proporcionado']);
    exit;
}

$id = intval($_POST['id_servicio']);
$nuevo_estatus = 'concluido';

$stmt = $conexion->prepare("UPDATE Servicios SET Estatus = ? WHERE Id_servicio = ?");
$stmt->bind_param("si", $nuevo_estatus, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'No se pudo actualizar el estatus']);
}
?>
