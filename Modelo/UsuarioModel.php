<?php
class Usuario {
    public static function obtenerUsuarios() {
        return [
            ['usuario' => 'admin', 'clave' => 'admin123', 'rol' => 'admin'],
            ['usuario' => 'cliente1@gmail.com', 'clave' => 'cliente123', 'rol' => 'cliente']
        ];
    }

    public static function validar($usuario, $clave) {
        $usuarios = self::obtenerUsuarios();
        foreach ($usuarios as $u) {
            if ($u['usuario'] === $usuario && $u['clave'] === $clave) {
                return $u;
            }
        }
        return null;
    }
}
