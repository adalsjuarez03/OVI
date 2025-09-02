<?php
require_once '../../Modelo/Conexion.php'; // Ajusta si tu ruta es distinta
$conn = Conexion::conectar();

$sql = "SELECT MAX(id_servicio) AS ultimo_id FROM servicios";
$resultado = $conn->query($sql);

$siguiente_id = 1;
if ($resultado && $fila = $resultado->fetch_assoc()) {
    $siguiente_id = $fila['ultimo_id'] + 1;
}

echo $siguiente_id;
$conn->close();
?>
