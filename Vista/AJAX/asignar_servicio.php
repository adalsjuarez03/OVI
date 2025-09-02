<?php
session_start();
require_once '../../Modelo/Conexion.php';
date_default_timezone_set('America/Mexico_City');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion = Conexion::conectar();

    // Validar sesión activa
    if (!isset($_SESSION['nombre'], $_SESSION['apellido'])) {
        echo json_encode(["success" => false, "error" => "Sesión no válida"]);
        exit;
    }

    // Validar parámetro
    if (!isset($_POST['id_servicio'])) {
        echo json_encode(["success" => false, "error" => "ID no recibido"]);
        exit;
    }

    $id_servicio = intval($_POST['id_servicio']);
    $turnado     = strtoupper($_SESSION['nombre'] . ' ' . $_SESSION['apellido']);
    $estatus     = 'asignado';

    // Actualizar servicio
    $sql = "UPDATE Servicios SET Estatus = ?, Turnado = ? WHERE Id_servicio = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssi", $estatus, $turnado, $id_servicio);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
    $conexion->close();
} else {
    echo json_encode(["success" => false, "error" => "Método inválido"]);
}
