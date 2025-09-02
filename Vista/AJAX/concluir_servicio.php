<?php
require_once '../../Modelo/Conexion.php';
$conexion = Conexion::conectar();

if (!isset($_POST['id_servicio'])) {
    echo json_encode(['error' => 'ID de servicio no proporcionado']);
    exit;
}

$id = intval($_POST['id_servicio']);
$comentario = $_POST['comentario'] ?? ''; // Recibir comentario
$nuevo_estatus = 'concluido';

// Actualizar estatus y guardar comentario
$stmt = $conexion->prepare("UPDATE Servicios SET Estatus = ?, Comentario_conclusion = ? WHERE Id_servicio = ?");
$stmt->bind_param("ssi", $nuevo_estatus, $comentario, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'No se pudo actualizar el estatus']);
}
?>
