<?php
session_start();
require_once '../../Modelo/Conexion.php'; // Ajusta la ruta si es necesario

// Ajustar zona horaria a México
date_default_timezone_set('America/Mexico_City');

// Validar si es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion = Conexion::conectar();

    // Verifica que haya sesión activa con nombre, apellido y usuario
    if (!isset($_SESSION['nombre']) || !isset($_SESSION['apellido']) || !isset($_SESSION['usuario'])) {
        echo json_encode(["success" => false, "error" => "Sesión no válida"]);
        exit;
    }

    // 1. Recibir datos
    $id_usuario   = $_SESSION['usuario']; // ID del admin logueado
    $titulo       = $_POST['titulo'];      // Nuevo campo
    $descripcion  = $_POST['descripcion'];
    $fecha        = date('Y-m-d H:i:s');   // Ahora con zona horaria correcta

    // Diferencia con cliente: aquí va como asignado
    $estatus = 'asignado';  
    $turnado = $_SESSION['nombre'] . ' ' . $_SESSION['apellido'];
    
    // 2. Manejo de archivo
    $archivo_ruta = '';
    $archivo_nombre = '';
    
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        // Validar tipo de archivo
        $tipos_permitidos = ['application/pdf', 'image/jpeg', 'image/png', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $tipo_archivo = $_FILES['archivo']['type'];
        
        if (!in_array($tipo_archivo, $tipos_permitidos)) {
            echo json_encode(["success" => false, "error" => "Tipo de archivo no permitido"]);
            exit;
        }
        
        // Validar tamaño (máximo 10MB)
        if ($_FILES['archivo']['size'] > 10 * 1024 * 1024) {
            echo json_encode(["success" => false, "error" => "El archivo es demasiado grande (máximo 10MB)"]);
            exit;
        }
        
        // Crear directorio si no existe
        $directorio = '../../Archivos/';
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        
        // Generar nombre único para el archivo
        $extension = pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION);
        $archivo_nombre = date('Y-m-d_H-i-s') . '_' . uniqid() . '.' . $extension;
        $archivo_ruta = $directorio . $archivo_nombre;
        
        // Mover archivo
        if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $archivo_ruta)) {
            echo json_encode(["success" => false, "error" => "Error al subir el archivo"]);
            exit;
        }
        
        // Guardar ruta relativa para la base de datos
        $archivo_ruta = 'Archivos/' . $archivo_nombre;
    }
    
    $acciones = $archivo_ruta ? "Archivo adjunto: " . $_FILES['archivo']['name'] : '';

    // 3. Insertar incluyendo titulo y archivo
    $sql = "INSERT INTO Servicios (id_usuario, Titulo, Descripcion, Fecha_solicitud, Estatus, Turnado, Acciones, Archivo_ruta, Archivo_nombre)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $archivo_original = $archivo_ruta ? $_FILES['archivo']['name'] : '';
    $stmt->bind_param("issssssss", $id_usuario, $titulo, $descripcion, $fecha, $estatus, $turnado, $acciones, $archivo_ruta, $archivo_original);

    if ($stmt->execute()) {
        // 4. Obtener ID insertado
        $id_servicio = $conexion->insert_id;
        $numero_servicio = 'SEyT-SISNE-OVIO-' . $id_servicio;

        // 5. Actualizar ese campo
        $update = $conexion->prepare("UPDATE Servicios SET Numero_servicio = ? WHERE Id_servicio = ?");
        $update->bind_param("si", $numero_servicio, $id_servicio);
        $update->execute();

        echo json_encode(['success' => true, 'archivo_subido' => !empty($archivo_ruta)]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
    $conexion->close();
} else {
    echo json_encode(["success" => false, "error" => "Método inválido"]);
}
?>