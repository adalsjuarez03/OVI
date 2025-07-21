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
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<style>
    * {
        font-family: 'Inter', sans-serif;
    }
</style>

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

            <!-- Contenedor Kanban -->
<div class="kanban-container" ondrop="drop(event)" ondragover="allowDrop(event)">
    <!-- Tarjeta ejemplo en columna CONCLUIDO -->
<div class="kanban-card" id="task-1" draggable="true" ondragstart="drag(event)" ondragover="allowDrop(event)" ondrop="drop(event)">
    <div class="card-header">
        <span class="badge green">CONCLUIDO</span>
        <span class="dots" onclick="toggleMenu(this)">⋮</span>
        <ul class="dropdown">
            <li>👁 Ver</li>
            <li>✏️ Editar</li>
            <li>🗑 Eliminar</li>
        </ul>
    </div>
    <h3>SEyT-UI-001</h3>
    <p>Ejemplo de solicitud de servicio.</p>
    <div class="kanban-footer">
        <strong>CAROLINA</strong>
        <button class="btn chat">💬</button>
    </div>
</div>
<div class="kanban-card" id="task-2" draggable="true" ondragstart="drag(event)" ondragover="allowDrop(event)" ondrop="drop(event)">
    <div class="card-header">
        <span class="badge green">CONCLUIDO</span>
        <span class="dots" onclick="toggleMenu(this)">⋮</span>
        <ul class="dropdown">
            <li>👁 Ver</li>
            <li>✏️ Editar</li>
            <li>🗑 Eliminar</li>
        </ul>
    </div>
    <h3>SEyT-UI-001</h3>
    <p>Ejemplo de solicitud de servicio.</p>
    <div class="kanban-footer">
        <strong>Marcos</strong>
        <button class="btn chat">💬</button>
    </div>
</div>

<div class="drop-zone" ondragover="allowDrop(event)" ondrop="dropAtEnd(event)"></div>
</div>
    <!-- Puedes duplicar más columnas como EN PROCESO, NUEVO, etc -->
</div>

        </section>
    </main>
</div>
<!-- Script para el menú desplegable del perfil -->
<script src="./js/script.js"></script>

</body>
</html>
