<?php
session_start();
require_once '../Modelo/UsuarioModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    $usuarioEncontrado = Usuario::validar($usuario, $clave);

    if ($usuarioEncontrado) {
        $_SESSION['rol'] = $usuarioEncontrado['rol'];

        if ($usuarioEncontrado['rol'] === 'admin') {
            header('Location: ../Vista/Administrador.php');
        } else {
            header('Location: ../Vista/Cliente.php');
        }
    } else {
        header('Location: ../Vista/Login.php?error=1');
    }
    exit();
}
