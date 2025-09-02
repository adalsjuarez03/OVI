<?php
require_once '../Modelo/Conexion.php';
require '../librerias/fpdf.php';

if (!isset($_GET['id'])) {
    die("ID de usuario no especificado.");
}

$id_usuario = intval($_GET['id']);
$conn = Conexion::conectar();

// Obtener datos del usuario
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
$stmt->close();

// Obtener servicios del usuario
$servicios = [];
$stmt = $conn->prepare("SELECT * FROM servicios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$servicios_resultado = $stmt->get_result();
while ($fila = $servicios_resultado->fetch_assoc()) {
    $servicios[] = $fila;
}
$stmt->close();

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();

// Agregar logo (ajusta la ruta y tamaño)
//$pdf->Image('../imagenes/logo.png', 10, 8, 33); 

// Encabezado con color de fondo
$pdf->SetFillColor(50, 100, 150); // Azul oscuro
$pdf->SetTextColor(255, 255, 255); // Blanco
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 15, 'REPORTE DE SERVICIOS', 0, 1, 'C', true);

$pdf->Ln(5);

// Datos del usuario con color y fuente normal
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(40, 10, 'Nombre:', 0, 0);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, $usuario['nombre'] . ' ' . $usuario['apellido'], 0, 1);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(40, 10, 'Correo:', 0, 0);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, $usuario['correo'], 0, 1);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(40, 10, 'Telefono:', 0, 0);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, $usuario['telefono'], 0, 1);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(40, 10, 'Rol:', 0, 0);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, $usuario['rol'], 0, 1);

$pdf->Ln(10);

// Servicios con encabezado de color
$pdf->SetFillColor(200, 220, 255); // Azul claro
$pdf->SetTextColor(0, 0, 80);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Servicios:', 0, 1, 'L', true);

$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(0, 0, 0);

if (empty($servicios)) {
    $pdf->Cell(0, 10, 'Este usuario no tiene servicios registrados.', 0, 1);
} else {
    foreach ($servicios as $s) {
        // Fondo para cada servicio
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(0, 8, "ID Servicio: " . $s['Id_servicio'], 1, 1, 'L', true);
        $pdf->MultiCell(0, 7, "Descripcion: " . $s['Descripcion'], 1, 'L', true);
        $pdf->Cell(0, 8, "Estatus: " . $s['Estatus'], 1, 1, 'L', true);
        $pdf->Ln(5);
    }
}

$pdf->Output('I', 'Reporte_Usuario.pdf');
