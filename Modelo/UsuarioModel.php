<?php
require_once 'conexion.php'; // Este archivo debe contener la conexión a tu BD
$conexion = Conexion::conectar();

class Usuario {

    public static function validar($correo, $clave) {
        global $conexion;

        // Preparar la consulta SQL
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo = ? AND contrasena = ?");
        $stmt->bind_param("ss", $correo, $clave); // "ss" porque son dos strings
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuarioBD = $resultado->fetch_assoc();

            // Comparar contraseña directamente (sin hash por ahora)
            if ($usuarioBD['contrasena'] === $clave) {
                return $usuarioBD;
            }
        }

        return null;
    }
}

