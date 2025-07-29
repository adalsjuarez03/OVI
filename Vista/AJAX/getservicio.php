<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../Modelo/Conexion.php';
$conn = Conexion::conectar();

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("SELECT * FROM Servicios WHERE Id_servicio = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($row = $resultado->fetch_assoc()) {

        // Formato del número de servicio
        $numero_servicio = 'SEyT-SISNE-OVIO-' . $row['Id_servicio'];

        // Traducir estatus a algo más legible
        $estatus_legible = '';
        switch ($row['Estatus']) {
            case 'asignado':
                $estatus_legible = 'Asignado';
                break;
            case 'no-asignado':
                $estatus_legible = 'No asignado';
                break;
            case 'concluido':
            case 'cancelado':
                $estatus_legible = 'Concluido / Cancelado';
                break;
            default:
                $estatus_legible = 'Sin especificar';
        }

        // Formatear la fecha con hora
        $fecha_formateada = date('d/m/Y H:i', strtotime($row['Fecha_solicitud']));

        echo json_encode([
            'numero_servicio' => $numero_servicio,
            'estatus' => $estatus_legible,
            'turnado' => $row['Turnado'],
            'fecha' => $fecha_formateada,
            'descripcion' => $row['Descripcion']
        ]);
    } else {
        echo json_encode(['error' => 'Servicio no encontrado']);
    }
} else {
    echo json_encode(['error' => 'ID no recibido']);
}
