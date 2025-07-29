<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: Login.php');
    exit();
}

require_once '../Modelo/Conexion.php';
$conn = Conexion::conectar();

$nombreCliente = isset($_SESSION['nombre']) ? $_SESSION['nombre'] . ' ' . $_SESSION['apellido'] : 'Usuario';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel Cliente</title>
    <link rel="stylesheet" href="./CSS/cliente.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body,
        button,
        input,
        textarea,
        select {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>

<body>

    <div class="layout">
        <aside class="sidebar" id="sidebar">
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

        <main class="main-content">
            <header class="top-bar">
                <button id="toggleSidebar" class="menu-toggle">☰</button>
                <div style="float: right;">
                    Bienvenid@ <?php echo strtoupper(htmlspecialchars($nombreCliente)); ?>
                </div>
            </header>

            <section class="content">
    <div class="section-header">
    <h2>Servicios solicitados <?php echo strtoupper(htmlspecialchars($nombreCliente)); ?></h2>
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
                    <div class="kanban-column" id="concluido-col">
                        <h3>✅ Concluido / ❌ Cancelado</h3>
                        <div class="kanban-list">
                            <?php
                            $result = $conn->query("SELECT * FROM Servicios WHERE Estatus = 'concluido' OR Estatus = 'cancelado' ORDER BY Fecha_solicitud DESC");
                            while ($row = $result->fetch_assoc()) {
                                $badgeClass = strtolower($row['Estatus']);
                                echo '<div class="kanban-card" data-status="' . $badgeClass . '" data-id="' . $row['Id_servicio'] . '">';
                                echo '<div class="card-header">';
                                echo '<div class="left">';
                                echo '<span class="badge ' . $badgeClass . '">' . strtoupper($row['Estatus']) . '</span>';
                                echo '<small class="created">📅 ' . date('d/m/Y', strtotime($row['Fecha_solicitud'])) . '</small>';
                                echo '</div>';
                                echo '<span class="dots" onclick="toggleMenu(this)">⋮</span>';
                                echo '<ul class="dropdown">';
                                echo '<li onclick="verDetalle(this)">👁 Ver</li>';
                                echo '<li>✏️ Editar</li>';
                                echo '<li>❌ Cancelar</li>';
                                echo '</ul>';
                                echo '</div>';
                                echo '<div class="card-body">';
                                echo '<div class="tags"><span class="tag">#' . htmlspecialchars($row['Numero_servicio']) . '</span></div>';
                                echo '<h4 class="titulo-servicio">' . substr(htmlspecialchars($row['Descripcion']), 0, 30) . '...</h4>';
                                echo '<p class="descripcion">' . substr(htmlspecialchars($row['Descripcion']), 0, 60) . '...</p>';
                                echo '</div>';
                                echo '<div class="kanban-footer">';
                                echo '<div class="asignado">' . htmlspecialchars($row['Turnado']) . '</div>';
                                echo '<button class="btn chat">💬</button>';
                                echo '</div></div>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="kanban-column" id="asignado-col">
                        <h3>🛠 Asignado</h3>
                        <div class="kanban-list">
                            <?php
                            $result = $conn->query("SELECT * FROM Servicios WHERE Estatus = 'asignado' ORDER BY Fecha_solicitud DESC");
                            while ($row = $result->fetch_assoc()) {
                                echo '<div class="kanban-card" data-status="asignado" data-id="' . $row['Id_servicio'] . '">';
                                echo '<div class="card-header">';
                                echo '<div class="left">';
                                echo '<span class="badge asignado">ASIGNADO</span>';
                                echo '<small class="created">📅 ' . date('d/m/Y', strtotime($row['Fecha_solicitud'])) . '</small>';
                                echo '</div>';
                                echo '<span class="dots" onclick="toggleMenu(this)">⋮</span>';
                                echo '<ul class="dropdown">';
                                echo '<li onclick="verDetalle(this)">👁 Ver</li>';
                                echo '<li>✏️ Editar</li>';
                                echo '<li>❌ Cancelar</li>';
                                echo '</ul>';
                                echo '</div>';
                                echo '<div class="card-body">';
                                echo '<div class="tags"><span class="tag">#' . htmlspecialchars($row['Numero_servicio']) . '</span></div>';
                                echo '<h4 class="titulo-servicio">' . substr(htmlspecialchars($row['Descripcion']), 0, 30) . '...</h4>';
                                echo '<p class="descripcion">' . substr(htmlspecialchars($row['Descripcion']), 0, 60) . '...</p>';
                                echo '</div>';
                                echo '<div class="kanban-footer">';
                                echo '<div class="asignado">' . htmlspecialchars($row['Turnado']) . '</div>';
                                echo '<button class="btn chat">💬</button>';
                                echo '</div></div>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="kanban-column" id="no-asignado-col">
                        <h3>🕓 No asignado</h3>
                        <div class="kanban-list">
                            <?php
                            $result = $conn->query("SELECT * FROM Servicios WHERE Estatus = 'no-asignado' ORDER BY Fecha_solicitud DESC");
                            while ($row = $result->fetch_assoc()) {
                                echo '<div class="kanban-card" data-status="no-asignado" data-id="' . $row['Id_servicio'] . '">';
                                echo '<div class="card-header">';
                                echo '<div class="left">';
                                echo '<span class="badge no-asignado">NO ASIGNADO</span>';
                                echo '<small class="created">📅 ' . date('d/m/Y', strtotime($row['Fecha_solicitud'])) . '</small>';
                                echo '</div>';
                                echo '<span class="dots" onclick="toggleMenu(this)">⋮</span>';
                                echo '<ul class="dropdown">';
                                echo '<li onclick="verDetalle(this)">👁 Ver</li>';
                                echo '<li>✏️ Editar</li>';
                                echo '<li>❌ Cancelar</li>';
                                echo '</ul>';
                                echo '</div>';
                                echo '<div class="card-body">';
                                echo '<div class="tags"><span class="tag">#' . htmlspecialchars($row['Numero_servicio']) . '</span></div>';
                                echo '<h4 class="titulo-servicio">' . substr(htmlspecialchars($row['Descripcion']), 0, 30) . '...</h4>';
                                echo '<p class="descripcion">' . substr(htmlspecialchars($row['Descripcion']), 0, 60) . '...</p>';
                                echo '</div>';
                                echo '<div class="kanban-footer">';
                                echo '<div class="asignado">' . htmlspecialchars($row['Turnado']) . '</div>';
                                echo '<button class="btn chat">💬</button>';
                                echo '</div></div>';
                            }
                            ?>
                        </div>
                    </div>
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
                        <div class="form-title">SEyT/UI/AST/AyST-01</div>
                        <div class="form-subtitle">SECRETARIA DE ECONOMÍA Y TRABAJO<br>UNIDAD DE INFORMÁTICA<br>ÁREA DE SOPORTE TÉCNICO</div>
                    </div>
                    <img src="https://ovi.economiaytrabajo.chiapas.gob.mx/static/LOGO.png" alt="Logo">
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
                    <button type="button" class="close-btn" id="cerrarModal">Cerrar</button>
                    <button type="submit" class="submit-btn">Enviar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de detalle -->
    <div id="detalleModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="cerrarModalDetalle()">×</span>
            <h3 id="detalleTitulo"></h3>
            <p><strong>Estatus:</strong> <span id="detalleEstatus"></span></p>
            <p><strong>Turnado a:</strong> <span id="detalleTurnado"></span></p>
            <p><strong>Fecha de solicitud:</strong> <span id="detalleFecha"></span></p>
            <p><strong>Descripción completa:</strong></p>
            <p id="detalleDescripcion" style="white-space: pre-wrap;"></p>
        </div>
        <div class="modal-footer">
      <button class="btn close-btn" onclick="cerrarModalDetalle()">Cerrar</button>
    </div>
    </div>
<!-- Modal Editar Servicio -->
<div id="editarModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="cerrarModalEditar()">×</span>
    <h3>Editar Descripción del Servicio</h3>
    <form id="editarForm">
      <input type="hidden" id="editarIdServicio" name="id_servicio">
      <div class="form-group">
        <label for="nuevaDescripcion">Nueva descripción:</label>
        <textarea id="nuevaDescripcion" name="descripcion" required></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="close-btn" onclick="cerrarModalEditar()">Cancelar</button>
        <button type="submit" class="submit-btn">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>

    <script src="./js/cliente.js"></script>

</body>

</html>