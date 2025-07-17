<?php
class Conexion extends PDO {
    private $hostBD = 'localhost';
    private $nombreBD = 'operadora_turistica';
    private $usuarioBD = 'root';
    private $passwordBD = '';

    public function __construct() {
        try {
            parent::__construct(
                'mysql:host=' . $this->hostBD . ';dbname=' . $this->nombreBD . ';charset=utf8',
                $this->usuarioBD,
                $this->passwordBD,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
        } catch (Exception $e) {
            exit("Error de conexión: " . $e->getMessage());
        }
    }

    // Método estático para obtener la conexión
    public static function conectar() {
        return new self();
    }
}
?>
