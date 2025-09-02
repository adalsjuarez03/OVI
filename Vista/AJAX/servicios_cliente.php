<?php
session_start();
require_once '../../Modelo/Conexion.php';
$conn = Conexion::conectar();

header('Content-Type: application/json');

// Obtenemos todos los servicios del cliente actual (si necesitas filtrar por cliente, puedes agregar un WHERE)
$result = $conn->query("SELECT Id_servicio, Estatus, Numero_servicio, Descripcion, Turnado, Fecha_solicitud FROM Servicios ORDER BY Fecha_solicitud DESC");

$servicios = [];
while ($row = $result->fetch_assoc()) {
    $servicios[] = [
        'id' => $row['Id_servicio'],
        'estatus' => $row['Estatus'],
        'numero' => $row['Numero_servicio'],
        'titutlo' => $row['titulo'],
        'descripcion' => $row['Descripcion'],
        'turnado' => $row['Turnado'],
        'fecha' => $row['Fecha_solicitud']
    ];
}

echo json_encode($servicios);
?>
