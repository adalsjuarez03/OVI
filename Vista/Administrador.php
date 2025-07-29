<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: Login.php');
    exit();
}

require_once '../Modelo/Conexion.php';
$conexion = Conexion::conectar();

$nombreAdministrador = isset($_SESSION['nombre']) ? $_SESSION['nombre'] . ' ' . $_SESSION['apellido'] : 'Usuario';

$consulta = $conexion->prepare("SELECT Id_servicio, Estatus, Numero_servicio, Descripcion, Turnado, Fecha_solicitud FROM Servicios ORDER BY Fecha_solicitud DESC");
$consulta->execute();
$consulta->store_result();
$consulta->bind_result($id, $estatus, $numero, $descripcion, $turnado, $fecha);
$servicios = [];

while ($consulta->fetch()) {
    $servicios[] = [
        'Id_servicio' => $id,
        'Estatus' => $estatus,
        'Numero_servicio' => $numero,
        'Descripcion' => $descripcion,
        'Turnado' => $turnado,
        'Fecha_solicitud' => $fecha
    ];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Administrador</title>
  <link rel="stylesheet" href="./CSS/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
<div class="layout">
  <aside class="sidebar" id="sidebar">
    <div class="logo">
      <img src="https://ovi.economiaytrabajo.chiapas.gob.mx/static/LOGO.png" alt="Logo" style="vertical-align: middle; margin-right: 5px; height: 30px;">Oficina Virtual
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
        <li class="menu-separator"><a href="usuarios.php">👥 Gestión de Usuarios</a></li>
        <li class="menu-separator"><a href="Logout.php">🚪 Salir</a></li>
      </ul>
    </nav>
  </aside>

  <main class="main-content">
    <header class="top-bar">
      <button id="toggleSidebar" class="menu-toggle">☰</button>
      <div style="float: right;">
        Bienvenid@ <?php echo strtoupper(htmlspecialchars($nombreAdministrador)); ?>
      </div>
    </header>

    <section class="content">
      <div class="section-header">
        <h2>Servicios solicitados <?php echo strtoupper(htmlspecialchars($nombreAdministrador)); ?></h2>
        <div class="actions">
          <button class="btn new" id="nuevaSolicitudBtn">+ Nueva solicitud</button>
          <button class="btn filter">Filtros</button>
        </div>
      </div>

      <!-- Kanban -->
      <div class="kanban-container">
        <?php foreach ($servicios as $servicio): ?>
          <div class="kanban-card <?php echo strtolower($servicio['Estatus']); ?>" id="servicio-<?php echo $servicio['Id_servicio']; ?>" draggable="true" ondragstart="drag(event)">
            <div class="card-header">
              <div class="left">
                <span class="badge <?php echo strtolower($servicio['Estatus']); ?>">
                  <?php echo strtoupper($servicio['Estatus']); ?>
                </span>
                <small class="created">📅 <?php echo date("d/m/Y", strtotime($servicio['Fecha_solicitud'])); ?></small>
              </div>
              <span class="dots" onclick="toggleMenu(this)">⋮</span>
              <ul class="dropdown">
                <li onclick="verDetalle(<?php echo $servicio['Id_servicio']; ?>)">👁 Ver</li>
                <li>✏️ Editar</li>
                <li>🗑 Eliminar</li>
              </ul>
            </div>

            <div class="card-body">
              <div class="tags">
                <span class="tag">#<?php echo htmlspecialchars($servicio['Numero_servicio']); ?></span>
              </div>
              <h4 class="titulo-servicio"><?php echo htmlspecialchars($servicio['Numero_servicio']); ?></h4>
              <p class="descripcion"><?php echo htmlspecialchars(mb_strimwidth($servicio['Descripcion'], 0, 100, '...')); ?></p>
            </div>

            <div class="kanban-footer">
              <div class="asignado"><?php echo htmlspecialchars($servicio['Turnado']); ?></div>
              <button class="btn chat">💬</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </main>
</div>

<!-- Modal Detalle -->
<!-- Modal Detalle Mejorado -->
<div id="detalleModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="cerrarModalDetalle()">×</span>
    <div class="modal-header">
      <h2 id="detalleTitulo" class="titulo-modal"></h2>
    </div>

    <div class="modal-body">
      <div class="info-group">
        <span class="info-label"><strong>🟢 Estatus:</strong></span>
        <span class="info-value" id="detalleEstatus"></span>
      </div>

      <div class="info-group">
        <span class="info-label"><strong>👨‍💼 Turnado a:</strong></span>
        <span class="info-value" id="detalleTurnado"></span>
      </div>

      <div class="info-group">
      </div><strong>📅 Fecha de solicitud:</strong></span>
        <span class="info-value" id="detalleFecha"></span>
      </div>

      <div class="info-group">
        <span class="info-label"><strong>📝 Descripción:</strong></span>
        <div class="descripcion-detalle" id="detalleDescripcion"></div>
      </div>
    </div>

    <div class="modal-footer">
      <button class="btn close-btn" onclick="cerrarModalDetalle()">Cerrar</button>
    </div>
  </div>
</div>


<script src="./js/script.js"></script>
</body>
</html>
