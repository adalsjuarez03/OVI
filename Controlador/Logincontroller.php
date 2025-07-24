<?php
session_start();
require_once '../Modelo/UsuarioModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    // Validar con el modelo
    $usuarioEncontrado = Usuario::validar($usuario, $clave);

    if ($usuarioEncontrado) {
        $_SESSION['usuario'] = $usuarioEncontrado['id_usuario']; 

        // guardar otros datos visibles
        $_SESSION['nombre'] = $usuarioEncontrado['nombre'];
        $_SESSION['apellido'] = $usuarioEncontrado['apellido'];
        $_SESSION['rol'] = $usuarioEncontrado['rol'];

        // Redirigir según el rol
        if ($usuarioEncontrado['rol'] === 'admin') {
            header('Location: ../Vista/Administrador.php');
        } else {
            header('Location: ../Vista/Cliente.php');
        }
    } else {
        // Redirigir con error si no se encuentra
        header('Location: ../Vista/Login.php?error=1');
    }

    exit();
}
