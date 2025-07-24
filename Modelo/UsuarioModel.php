<?php
require_once 'conexion.php'; // Este archivo debe contener la conexión a tu BD
$conexion = Conexion::conectar();

class Usuario {

    public static function validar($correo, $clave) {
    global $conexion;

    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuarioBD = $resultado->fetch_assoc();

        // Verificar la contraseña encriptada
       if ($clave === $usuarioBD['contrasena']) {
    return $usuarioBD;
}

    }

    return null;
}
}
