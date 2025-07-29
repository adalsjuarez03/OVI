<?php
session_start();
require_once '../Modelo/Conexion.php'; // Ajusta la ruta si es necesario

// Validar si es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion = Conexion::conectar();

    // Verifica que haya sesión activa con nombre y apellido
    if (!isset($_SESSION['nombre']) || !isset($_SESSION['apellido'])) {
        echo json_encode(["success" => false, "error" => "Sesión no válida"]);
        exit;
    }

// 1. Recibir datos
$descripcion = $_POST['descripcion'];
$fecha = date('Y-m-d H:i:s');
$estatus = 'no-asignado';
$turnado = $_SESSION['nombre'] . ' ' . $_SESSION['apellido'];
$acciones = '';

// 2. Insertar sin numero_servicio aún
$sql = "INSERT INTO Servicios (Descripcion, Fecha_solicitud, Estatus, Turnado, Acciones)
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sssss", $descripcion, $fecha, $estatus, $turnado, $acciones);

if ($stmt->execute()) {
    // 3. Obtener ID insertado
    $id_servicio = $conexion->insert_id;
    $numero_servicio = 'SEyT-SISNE-OVIO-' . $id_servicio;

    // 4. Actualizar ese campo
    $update = $conexion->prepare("UPDATE Servicios SET Numero_servicio = ? WHERE Id_servicio = ?");
    $update->bind_param("si", $numero_servicio, $id_servicio);
    $update->execute();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

    $stmt->close();
    $conexion->close();
} else {
    echo json_encode(["success" => false, "error" => "Método inválido"]);
}
