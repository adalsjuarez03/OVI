<?php
require_once 'Modelo/usuarioModel.php';

class LoginController {
    public static function mostrarAcceso() {
        include 'Vista/login/login.php';
    }

    public static function iniciarSesion() {
        $correo = isset($_POST['correo']) ? $_POST['correo'] : null;
        $contraseña = isset($_POST['contraseña']) ? $_POST['contraseña'] : null;
    
        error_log("Intento de inicio de sesión - Correo: " . $correo);
    
        if ($correo && $contraseña) {
            try {
                $usuarioModel = new UsuarioModel();
                $usuario = $usuarioModel->autenticarUsuario($correo, $contraseña);
               
                if ($usuario) {
                    $_SESSION['usuario_id'] = $usuario['Id_Cliente'];
                    $_SESSION['usuario_nombre'] = $usuario['Nombre'];
                    $_SESSION['usuario_rol'] = $usuario['Rol'];
    
                    error_log("Sesión iniciada - ID: " . $_SESSION['usuario_id'] . 
                             " Nombre: " . $_SESSION['usuario_nombre'] . 
                             " Rol: " . $_SESSION['usuario_rol']);
    
                    if ($_SESSION['usuario_rol'] === 'administrador') {
                        error_log("Redirigiendo a administrador");
                        header("Location: Vista/administrador/administrador.php");
                        exit();
                    } else {
                        error_log("Redirigiendo a cliente");
                        header("Location: Vista/cliente/cliente.php");
                        exit();
                    }
                } else {
                    error_log("Autenticación fallida para correo: " . $correo);
                    echo "Correo o contraseña incorrectos.";
                }
            } catch (Exception $e) {
                error_log("Error en inicio de sesión: " . $e->getMessage());
                echo "Error: " . $e->getMessage();
            }
        }
    }
    
    

    public static function registrarUsuario() {
        $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
        $apellido = isset($_POST['apellido']) ? $_POST['apellido'] : null;
        $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : null;
        $correo = isset($_POST['correo']) ? $_POST['correo'] : null;
        $curp = isset($_POST['curp']) ? $_POST['curp'] : null;
        $contraseña = isset($_POST['contraseña']) ? $_POST['contraseña'] : null;
        $mensaje = "";

        if ($nombre && $apellido && $telefono && $correo && $curp && $contraseña) {
            $usuarioModel = new UsuarioModel();
            $resultado = $usuarioModel->registrarUsuario($nombre, $apellido, $telefono, $correo, $curp, $contraseña);
            
            if ($resultado) {
                $mensaje = "Registro exitoso. Redirigiendo al inicio...";
                header("Refresh: 3; url=index.php"); 
            } else {
                $mensaje = "Error en el registro. Inténtalo de nuevo.";
            }
        } else {
            $mensaje = "Datos incompletos para el registro.";
        }
        include 'Vista/login/registro.php';
    }
}
?>
