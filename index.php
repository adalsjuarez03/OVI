<?php
session_start(); // Inicia la sesión

require_once("config.php");
require("controlador/IndexController.php");
require("controlador/OfertasController.php");
require("controlador/LoginController.php");
require("controlador/RegistroController.php");

// Verifica el rol y redirige a la página correspondiente
if (isset($_SESSION['Rol'])) {
    if ($_SESSION['Rol'] === 'cliente') {
        header('Location: Vista/cliente/cliente.php'); // Redirige a clientes.php si es cliente
        exit();
    } elseif ($_SESSION['Rol'] === 'administrador') {
        header('Location: Vista/administrador/administrador.php'); // Redirige a administrador.php si es administrador
        exit();
    }
}

// Mantiene la lógica existente si no hay sesión activa o rol definido

elseif (isset($_GET['u'])) {
    $metodo = $_GET['u'];
    if (method_exists('OfertasController', $metodo)) {
        OfertasController::{$metodo}();
    } else {
        echo "El método $metodo no existe en OfertasController.<br>";
    }
} 
elseif (isset($_GET['m'])) {
    $metodo = $_GET['m'];
    if (method_exists('LoginController', $metodo)) {
        LoginController::{$metodo}();
    } else {
        echo "El método $metodo no existe en LoginController.<br>";
        echo "Métodos disponibles en LoginController: ";
        print_r(get_class_methods('LoginController'));
    }
} 
elseif (isset($_GET['i'])) {
    $metodo = $_GET['i'];
    if (method_exists('IndexController', $metodo)) {
        IndexController::{$metodo}();
    } else {
        echo "El método $metodo no existe en IndexController.<br>";
    }
} 
elseif (isset($_GET['action'])) {
    $action = $_GET['action'];
    if (method_exists('LoginController', $action)) {
        LoginController::{$action}();
    } else {
        LoginController::mostrarAcceso();
    }
} 
elseif (isset($_GET['r'])) {
    $metodo = $_GET['r'];
    if (method_exists('RegistroController', $metodo)) {
        RegistroController::{$metodo}();
    } else {
        echo "El método $metodo no existe en RegistroController.<br>";
    }
} 
else {
    IndexController::index();
}
?>