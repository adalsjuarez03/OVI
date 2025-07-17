<?php
session_start();
require_once '../Modelo/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    $usuarioEncontrado = Usuario::validar($usuario, $clave);

    if ($usuarioEncontrado) {
        $_SESSION['rol'] = $usuarioEncontrado['rol'];

        if ($usuarioEncontrado['rol'] === 'admin') {
            header('Location: ../Vista/homeAdmin.php');
        } else {
            header('Location: ../Vista/homeCliente.php');
        }
    } else {
        header('Location: ../Vista/login.php?error=1');
    }
    exit();
}
