<?php
class Conexion {
    public static function conectar() {
        $host = "localhost";
        $usuario = "root";
        $password = "";
        $base_datos = "ovi";

        $conexion = new mysqli($host, $usuario, $password, $base_datos);

        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        return $conexion;
    }
}
