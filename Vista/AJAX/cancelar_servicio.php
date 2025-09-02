<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    http_response_code(403);
    exit("Acceso no autorizado");
}

require_once '../../Modelo/Conexion.php';
$conn = Conexion::conectar();

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("UPDATE Servicios SET Estatus = 'cancelado' WHERE Id_servicio = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "error" => "ID no recibido"]);
}
?>
