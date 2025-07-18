<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: Login.php');
    exit();
}

$nombreCliente = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Cliente';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Cliente</title>
      <link rel="stylesheet" href="./CSS/style.css">
</head>
<body>

<div class="layout">
    <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="https://ovi.economiaytrabajo.chiapas.gob.mx/static/LOGO.png" alt="Icono de Oficina Virtual" style="vertical-align: middle; margin-right: 5px; height: 30px;">Oficina Virtual
            </div>
            <nav>
                <ul>
                    <li class="has-submenu">
                        <a href="#" id="togglePerfil">👨🏼‍💻 Mi perfil</a>
                        <ul class="submenu" id="submenuPerfil">
                            <li><a href="edit_profile.php">✏️ Editar</a></li>
                            <li><a href="misdocumentos.php">📄 Mis documentos</a></li>
                        </ul>
                    </li>
                    <li class="menu-separator"><a href="Cliente.php">📋 Servicios</a></li>
                    <li><a href="Logout.php">🚪 Salir</a></li>
                </ul>
            </nav>
        </aside>

    <!-- Contenido principal -->
    <main class="main-content">
        <header class="top-bar">
            <div style="float: right;">
                Bienvenid@ <?php echo strtoupper(htmlspecialchars($nombreCliente)); ?>
            </div>
        </header>

        <section class="content">
            <div class="section-header">
                <h2>Servicios solicitados <?php echo strtoupper(htmlspecialchars($nombreCliente)); ?></h2>
                <div class="actions">
                    <button class="btn new">+ Nueva solicitud</button>
                    <button class="btn filter">Filtros</button>
                </div>
            </div>

            <!-- Tabla de ejemplo -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Estatus</th>
                            <th>Núm. servicio</th>
                            <th>Descripción</th>
                            <th>Turnado</th>
                            <th>Fecha</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><span class="badge green">CONCLUIDO</span></td>
                            <td>SEyT-UI-001</td>
                            <td>Ejemplo de solicitud de servicio.</td>
                            <td><strong>CAROLINA</strong></td>
                            <td>17/07/2025</td>
                            <td><button class="btn chat">💬</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>


<script>
    // PARA DESPLEGABLE DE MI PERFIL
document.addEventListener("DOMContentLoaded", function() {
    const toggle = document.getElementById("togglePerfil");
    const item = toggle.parentElement;

    toggle.addEventListener("click", function(e) {
        e.preventDefault();
        item.classList.toggle("open");
    });
});
</script>


</body>
</html>
