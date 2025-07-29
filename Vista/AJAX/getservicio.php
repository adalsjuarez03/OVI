<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ CORREGIDO: ruta correcta al archivo de conexión
require_once '../../Modelo/Conexion.php';

$conn = Conexion::conectar();

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("SELECT * FROM Servicios WHERE Id_servicio = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($row = $resultado->fetch_assoc()) {
        // ✅ Devolver los datos en JSON para mostrar en el modal
        echo json_encode([
            'estatus' => $row['Estatus'],
            'turnado' => $row['Turnado'],
            'fecha' => date('d/m/Y', strtotime($row['Fecha_solicitud'])),
            'descripcion' => $row['Descripcion']
        ]);
    } else {
        echo json_encode(['error' => 'Servicio no encontrado']);
    }
} else {
    echo json_encode(['error' => 'ID no recibido']);
}
?>
