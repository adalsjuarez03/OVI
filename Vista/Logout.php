<?php
session_start();       // Inicia la sesión actual
session_destroy();     // Elimina todos los datos de la sesión
header('Location: Login.php');  // Redirige al login
exit();
