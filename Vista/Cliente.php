<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: Login.php');
    exit();
}

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
                        <button class="btn filter">Filtros</button>
                    </div>
                </div>

                <div class="kanban-container">
                    <div class="kanban-column" id="concluido-col">
                        <h3>✅ Concluido / ❌ Cancelado</h3>
                        <div class="kanban-list"></div>
                    </div>
                    <div class="kanban-column" id="asignado-col">
                        <h3>🛠 Asignado</h3>
                        <div class="kanban-list"></div>
                    </div>
                    <div class="kanban-column" id="no-asignado-col">
                        <h3>🕓 No asignado</h3>
                        <div class="kanban-list"></div>
                    </div>
                </div>

                <!-- Tarjetas (se asignan por JS) -->
                <div id="tarjetasOcultas" style="display: none;">
                    <div class="kanban-card" data-status="cancelado">
                        <div class="card-header">
                            <div class="left">
                                <span class="badge cancelado">CANCELADO</span>
                                <small class="created">📅 03/04/2025</small>
                            </div>
                            <span class="dots" onclick="toggleMenu(this)">⋮</span>
                            <ul class="dropdown">
                                <li onclick="verDetalle()">👁 Ver</li>
                                <li>✏️ Editar</li>
                                <li>❌ Cancelar</li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tags"><span class="tag">#SEyT-UI-160</span></div>
                            <h4 class="titulo-servicio">Generar sesión Zoom</h4>
                            <p class="descripcion">Solicitud para generar una sesión de videoconferencia...</p>
                        </div>
                        <div class="kanban-footer">
                            <div class="asignado">DIAZ TORAL CARLOS ARTURO</div>
                            <button class="btn chat">💬</button>
                        </div>
                    </div>

                    <div class="kanban-card" data-status="asignado">
                        <div class="card-header">
                            <div class="left">
                                <span class="badge asignado">ASIGNADO</span>
                                <small class="created">📅 07/04/2025</small>
                            </div>
                            <span class="dots" onclick="toggleMenu(this)">⋮</span>
                            <ul class="dropdown">
                                <li onclick="verDetalle()">👁 Ver</li>
                                <li>✏️ Editar</li>
                                <li>❌ Cancelar</li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tags"><span class="tag">#SEyT-UI-189</span></div>
                            <h4 class="titulo-servicio">Solicitud revisión técnica</h4>
                            <p class="descripcion">Programación de visita técnica por interrupciones en internet...</p>
                        </div>
                        <div class="kanban-footer">
                            <div class="asignado">NIGENDA BLANCO JOSE ALEJANDRO</div>
                            <button class="btn chat">💬</button>
                        </div>
                    </div>

                    <div class="kanban-card" data-status="concluido">
                        <div class="card-header">
                            <div class="left">
                                <span class="badge concluido">CONCLUIDO</span>
                                <small class="created">📅 03/04/2025</small>
                            </div>
                            <span class="dots" onclick="toggleMenu(this)">⋮</span>
                            <ul class="dropdown">
                                <li onclick="verDetalle()">👁 Ver</li>
                                <li>✏️ Editar</li>
                                <li>❌ Cancelar</li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tags"><span class="tag">#SEyT-UI-001</span></div>
                            <h4 class="titulo-servicio">Nuevo diseño de panel</h4>
                            <p class="descripcion">Lorem ipsum dolor sit amet consectetur elit...</p>
                        </div>
                        <div class="kanban-footer">
                            <div class="asignado">CARLOS</div>
                            <button class="btn chat">💬</button>
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
    </div>

    <script src="./js/cliente.js"></script>

</body>

</html>