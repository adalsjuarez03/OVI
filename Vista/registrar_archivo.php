<?php
session_start();
require_once '../Modelo/Conexion.php'; 

// Ajustar zona horaria a México
date_default_timezone_set('America/Mexico_City');

// Validar si es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion = Conexion::conectar();

    // Verifica que haya sesión activa
    if (!isset($_SESSION['nombre']) || !isset($_SESSION['apellido']) || !isset($_SESSION['usuario'])) {
        echo json_encode(["success" => false, "error" => "Sesión no válida"]);
        exit;
    }

    // 1. Recibir datos
    $id_usuario = $_SESSION['usuario'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha = date('Y-m-d H:i:s');
    $estatus = 'no-asignado';
    $turnado = $_SESSION['nombre'] . ' ' . $_SESSION['apellido'];
    $acciones = '';
    
    // Variables para archivo
    $archivo_ruta = '';
    $archivo_nombre = '';
    $archivo_subido = false;

    // 2. Manejo del archivo si se subió uno
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['archivo'];
        
        // Validar tipo de archivo
        $tipos_permitidos = ['pdf', 'jpg', 'jpeg', 'png', 'docx', 'doc', 'txt'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, $tipos_permitidos)) {
            echo json_encode(['success' => false, 'error' => 'Tipo de archivo no permitido. Solo se permiten: PDF, JPG, PNG, DOCX, DOC, TXT']);
            exit;
        }
        
        // Validar tamaño (10MB máximo)
        if ($archivo['size'] > 10 * 1024 * 1024) {
            echo json_encode(['success' => false, 'error' => 'El archivo es demasiado grande. Máximo 10MB']);
            exit;
        }
        
        // Crear directorio si no existe
        $directorio_destino = '../Archivos/';
        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }
        
        // Generar nombre único para evitar conflictos
        $archivo_nombre = $archivo['name'];
        $nombre_archivo_unico = date('YmdHis') . '_' . $id_usuario . '_' . $archivo_nombre;
        $archivo_ruta = 'Archivos/' . $nombre_archivo_unico;
        $ruta_completa = $directorio_destino . $nombre_archivo_unico;
        
        // Mover archivo
        if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
            $archivo_subido = true;
            $acciones = 'Archivo adjunto: ' . $archivo_nombre;
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al subir el archivo']);
            exit;
        }
    }

    // 3. Insertar en base de datos incluyendo campos de archivo
    $sql = "INSERT INTO Servicios (id_usuario, Titulo, Descripcion, Fecha_solicitud, Estatus, Turnado, Acciones, Archivo_ruta, Archivo_nombre)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("issssssss", $id_usuario, $titulo, $descripcion, $fecha, $estatus, $turnado, $acciones, $archivo_ruta, $archivo_nombre);

    if ($stmt->execute()) {
        // 4. Obtener ID insertado y actualizar número de servicio
        $id_servicio = $conexion->insert_id;
        $numero_servicio = 'SEyT-SISNE-OVIO-' . $id_servicio;

        $update = $conexion->prepare("UPDATE Servicios SET Numero_servicio = ? WHERE Id_servicio = ?");
        $update->bind_param("si", $numero_servicio, $id_servicio);
        $update->execute();
        $update->close();

        echo json_encode([
            'success' => true, 
            'archivo_subido' => $archivo_subido,
            'mensaje' => $archivo_subido ? 'Solicitud creada con archivo adjunto' : 'Solicitud creada exitosamente'
        ]);
    } else {
        // Si hay error, eliminar el archivo subido si existe
        if ($archivo_subido && file_exists($ruta_completa)) {
            unlink($ruta_completa);
        }
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
    $conexion->close();
} else {
    echo json_encode(["success" => false, "error" => "Método inválido"]);
}
?>