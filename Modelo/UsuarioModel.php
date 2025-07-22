<?php
require_once 'conexion.php'; // Este archivo debe contener la conexión a tu BD
$conexion = Conexion::conectar();
var_dump($conexion); // Esto te mostrará si la conexión es válida


class Usuario {

    public static function validar($usuario, $clave) {
        global $conexion;

        // Preparar la consulta SQL para obtener al usuario
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuarioBD = $resultado->fetch_assoc();

            // Comparar contraseña sin hash (por ahora)
            if ($usuarioBD['contrasena'] === $clave) {
                return $usuarioBD;
            }
        }

        return null;
    }
}
