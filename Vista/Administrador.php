<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
  header('Location: Login.php');
  exit();
}

require_once '../Modelo/Conexion.php';
$conexion = Conexion::conectar();

$usuario_id = $_SESSION['usuario'] ?? null;
if ($usuario_id) {
  $stmt = $conexion->prepare("SELECT nombre, apellido FROM usuarios WHERE id_usuario = ?");
  $stmt->bind_param("i", $usuario_id);
  $stmt->execute();
  $stmt->bind_result($nombre, $apellido);
  $stmt->fetch();
  $stmt->close();

  $nombreAdministrador = strtoupper($nombre . ' ' . $apellido);
} else {
  $nombreAdministrador = 'Usuario';
}

// Consulta incluyendo el comentario del administrador
$consulta = $conexion->prepare("
    SELECT Id_servicio, Estatus, Numero_servicio, Titulo, Descripcion, Turnado, Fecha_solicitud, Comentario_conclusion 
    FROM Servicios 
    ORDER BY Fecha_solicitud DESC
");
$consulta->execute();
$consulta->store_result();
$consulta->bind_result($id, $estatus, $numero, $titulo, $descripcion, $turnado, $fecha, $comentario);

$servicios = [];

while ($consulta->fetch()) {
  $servicios[] = [
    'Id_servicio' => $id,
    'Estatus' => $estatus,
    'Numero_servicio' => $numero,
    'Titulo' => $titulo,
    'Descripcion' => $descripcion,
    'Turnado' => $turnado,
    'Fecha_solicitud' => $fecha,
    'Comentario' => $comentario // agregado
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
            <div class="dropdown-filtro">
              <button class="btn filter" onclick="toggleFiltroMenu()">🎯 Filtros</button>
              <ul class="filtro-menu" id="filtroMenu">
                <li onclick="filtrarColumna('todas')">🔄 Mostrar todas</li>
                <li onclick="filtrarColumna('no-asignado')">🕓 No asignado</li>
                <li onclick="filtrarColumna('asignado')">🛠 Asignado</li>
                <li onclick="filtrarColumna('concluido')">✅ Concluido / ❌ Cancelado</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="kanban-container">
          <?php foreach ($servicios as $servicio): ?>
            <div class="kanban-card <?php echo strtolower($servicio['Estatus']); ?>"
              data-status="<?php echo strtolower($servicio['Estatus']); ?>"
              id="servicio-<?php echo $servicio['Id_servicio']; ?>"
              draggable="true"
              ondragstart="drag(event)">
              
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

                  <?php if (strtolower($servicio['Estatus']) === 'no-asignado'): ?>
                    <li onclick="asignarServicio(<?php echo $servicio['Id_servicio']; ?>)">✅ Asignar</li>
                    <li onclick="concluirServicio(<?php echo $servicio['Id_servicio']; ?>)">✔️ Concluir</li>
                  <?php elseif (strtolower($servicio['Estatus']) === 'asignado'): ?>
                    <li onclick="concluirServicio(<?php echo $servicio['Id_servicio']; ?>)">✔️ Concluir</li>
                  <?php endif; ?>
                </ul>
              </div>

              <div class="card-body">
                <div class="tags">
                  <span class="tag">#<?php echo htmlspecialchars($servicio['Numero_servicio']); ?></span>
                </div>
                <h4 class="titulo-servicio"><?php echo htmlspecialchars($servicio['Titulo']); ?></h4>
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

  <!-- Modal Nueva Solicitud -->
  <div id="nuevaSolicitudModal" class="modal">
    <div class="modal-content">
      <form id="solicitudForm" enctype="multipart/form-data">
        <div class="modal-header">
          <div>
            <div class="form-title">SEyT-SISNE-OVI-<span id="idServicio"></span></div>
            <div class="form-subtitle">SECRETARIA DE ECONOMÍA Y TRABAJO<br>UNIDAD DE INFORMÁTICA<br>ÁREA DE SOPORTE TÉCNICO</div>
          </div>
          <img src="https://ovi.economiaytrabajo.chiapas.gob.mx/static/LOGO.png" alt="Logo">
        </div>

        <div class="form-group">
          <label for="titulo">Título del servicio solicitado</label>
          <p>Escriba un título breve y claro para identificar la solicitud</p>
          <input type="text" id="titulo" name="titulo" required>
        </div>

        <div class="form-group">
          <label for="descripcion">Descripción de servicio solicitado</label>
          <p>Por favor, de una descripción de su problema</p>
          <textarea id="descripcion" name="descripcion" required></textarea>
        </div>

        <div class="file-upload">
          <input type="file" id="archivo" name="archivo">
          <label for="archivo">Pulse aquí para subir un archivo</label>
          <span id="fileName" class="file-name"></span>
        </div>

        <div class="modal-footer">
          <button type="button" class="submit-btn" id="cerrarModal">Cerrar</button>
          <button type="submit" class="submit-btn">Enviar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Detalle -->
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
          <strong>📅 Fecha de solicitud:</strong>
          <span class="info-value" id="detalleFecha"></span>
        </div>

        <div class="info-group">
          <span class="info-label"><strong>📝 Descripción:</strong></span>
          <div class="descripcion-detalle" id="detalleDescripcion" style="max-height: 300px; overflow-y: auto; white-space: pre-wrap; border: 1px solid #ccc; padding: 10px;"></div>
        </div>

        <div class="info-group">
          <span class="info-label"><strong>💬 Comentario Admin:</strong></span>
          <div class="comentario-detalle" id="detalleComentario" style="max-height: 150px; overflow-y: auto; white-space: pre-wrap; border: 1px solid #ccc; padding: 10px;"></div>
        </div>
      </div>

      <div class="modal-footer">
        <button class="submit-btn" onclick="cerrarModalDetalle()">Cerrar</button>
      </div>
    </div>
  </div>

  <!-- Modal de Chat -->
  <div id="chatModal" class="modal">
    <div class="modal-content chat-modal">
      <span class="close-btn" onclick="cerrarChatModal()">×</span>
      <h3>💬 Chat con Cliente</h3>
      <div id="chatMensajes" class="chat-mensajes"></div>
      <form id="formChat">
        <input type="hidden" id="chatIdServicio">
        <textarea id="mensajeChat" placeholder="Escribe tu respuesta..." required></textarea>
        <button type="submit" class="submit-btn">Enviar</button>
      </form>
    </div>
  </div>

  <!-- Modal Concluir Servicio -->
  <div id="concluirModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="cerrarConcluirModal()">×</span>
      <h3>Concluir Servicio</h3>
      <p>Escribe un comentario o sugerencia antes de concluir el servicio:</p>
      <textarea id="comentarioConcluir" placeholder="Comentario..." style="width: 100%; height: 100px;"></textarea>
      <div class="modal-footer">
        <button class="btn submit-btn" onclick="cerrarConcluirModal()">Cancelar</button>
        <button class="btn submit-btn" id="enviarConcluirBtn">Enviar y Concluir</button>
      </div>
    </div>
  </div>

  <script src="./js/script.js"></script>
</body>
</html>
  
