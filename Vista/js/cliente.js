document.addEventListener("DOMContentLoaded", function () {
    // Mover tarjetas a sus columnas según data-status
    const cards = document.querySelectorAll(".kanban-card");
    cards.forEach(card => {
        const status = card.getAttribute("data-status");
        if (status === "no-asignado") {
            document.querySelector("#no-asignado-col .kanban-list").appendChild(card);
        } else if (status === "asignado") {
            document.querySelector("#asignado-col .kanban-list").appendChild(card);
        } else if (status === "concluido" || status === "cancelado") {
            document.querySelector("#concluido-col .kanban-list").appendChild(card);
        }
    });

    // Mostrar/ocultar menú de tres puntos
    document.addEventListener("click", function (e) {
        if (!e.target.matches(".dots")) {
            document.querySelectorAll(".dropdown").forEach(el => el.style.display = "none");
        }

        // Funcionalidad para "❌ Cancelar"
        if (e.target.textContent.trim() === "❌ Cancelar") {
            const card = e.target.closest(".kanban-card");
            const id = card.getAttribute("data-id");

            if (confirm("¿Estás seguro de que deseas cancelar este servicio?")) {
                fetch("AJAX/cancelar_servicio.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "id=" + encodeURIComponent(id),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cambiar visualmente la tarjeta usando la función
                        actualizarEstadoTarjeta(card, "cancelado");

                        // Mover la tarjeta a la columna correspondiente
                        document.querySelector("#concluido-col .kanban-list").appendChild(card);
                    } else {
                        alert("Error al cancelar: " + (data.error || "Desconocido"));
                    }
                })
                .catch(err => {
                    alert("Error de red: " + err.message);
                });
            }
        }
    });

    document.getElementById('toggleSidebar').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });

    const toggle = document.getElementById("togglePerfil");
    const item = toggle.parentElement;
    toggle.addEventListener("click", function (e) {
        e.preventDefault();
        item.classList.toggle("open");
    });

    // Modal nueva solicitud
    const modal = document.getElementById("nuevaSolicitudModal");
    const idSpan = document.getElementById("idServicio"); // span donde se mostrará el ID

    document.getElementById("nuevaSolicitudBtn").addEventListener("click", () => {
        fetch("ajax/obtener_siguiente_id.php") 
            .then(response => response.text())
            .then(data => {
                idSpan.textContent = data;
                modal.style.display = "block";
            })
            .catch(error => {
                console.error("Error al obtener el ID:", error);
                idSpan.textContent = "??";
                modal.style.display = "block";
            });
    });
    
    document.getElementById("cerrarModal").addEventListener("click", () => modal.style.display = "none");

    window.addEventListener("click", function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });

    const fileInput = document.getElementById("archivo");
    const fileName = document.getElementById("fileName");
    fileInput.addEventListener("change", function () {
        fileName.textContent = this.files.length > 0 ? this.files[0].name : "";
    });

    // Enviar solicitud con AJAX
    document.getElementById("solicitudForm").addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        // Validar que el título no esté vacío
        const titulo = document.getElementById("titulo").value.trim();
        if (!titulo) {
            alert("Por favor, ingrese un título para la solicitud");
            return;
        }

        fetch("registrar_archivo.php", {
            method: "POST",
            body: formData,
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Solicitud enviada con éxito!");
                location.reload();
            } else {
                alert("Error al registrar: " + (data.error || "Error desconocido"));
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Ocurrió un error al enviar la solicitud.");
        });

        modal.style.display = "none";
        this.reset();
        fileName.textContent = "";
    });

    // Botón ✏️ Editar
    document.querySelectorAll('.dropdown li:nth-child(2)').forEach(el => {
        el.addEventListener('click', function () {
            const card = el.closest('.kanban-card');
            const idServicio = card.getAttribute('data-id');

            fetch('./AJAX/getservicio.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + encodeURIComponent(idServicio)
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert('Error: ' + data.error);
                    return;
                }

                document.getElementById('editarIdServicio').value = idServicio;
                document.getElementById('nuevaDescripcion').value = data.descripcion;
                document.getElementById('editarModal').style.display = 'block';
            })
            .catch(err => {
                console.error('Error al cargar descripción completa:', err);
                alert('Ocurrió un error al obtener la descripción');
            });
        });
    });

    // Enviar cambios edición
    document.getElementById('editarForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('./AJAX/editar_servicio.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('Descripción actualizada con éxito');
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Error desconocido'));
            }
        })
        .catch(err => {
            console.error('Error al editar:', err);
            alert('Ocurrió un error');
        });
    });

    // Botón de chat
    document.querySelectorAll(".kanban-card .chat").forEach(btn => {
        btn.addEventListener("click", function () {
            const card = this.closest(".kanban-card");
            const idServicio = card.getAttribute("data-id");
            abrirChatModal(idServicio);
        });
    });

    // Enviar mensaje chat
    document.getElementById("formChat").addEventListener("submit", async function (e) {
        e.preventDefault();
        const idServicio = document.getElementById("chatIdServicio").value;
        const mensaje = document.getElementById("mensajeChat").value;

        const res = await fetch("AJAX/guardar_mensaje.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id_servicio=${idServicio}&mensaje=${encodeURIComponent(mensaje)}`
        });

        if (res.ok) {
            document.getElementById("mensajeChat").value = "";
            cargarMensajes(idServicio);
        }
    });
});

// Función para actualizar el estado visual de las tarjetas
function actualizarEstadoTarjeta(card, nuevoEstado) {
    // Remover todas las clases de estado existentes
    card.classList.remove("concluido", "asignado", "cancelado", "no-asignado");
    
    // Añadir la nueva clase de estado
    card.classList.add(nuevoEstado);
    
    // Actualizar el atributo data-status
    card.setAttribute("data-status", nuevoEstado);
    
    // Actualizar el badge
    const badge = card.querySelector(".badge");
    if (badge) {
        badge.textContent = nuevoEstado.toUpperCase();
        badge.className = "badge " + nuevoEstado;
    }
}

// Función para cancelar servicio desde el menú desplegable
function cancelarServicio(elemento) {
    const card = elemento.closest('.kanban-card');
    const id = card.getAttribute('data-id');

    if (confirm("¿Estás seguro de que deseas cancelar este servicio?")) {
        fetch("AJAX/cancelar_servicio.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id=" + encodeURIComponent(id),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cambiar visualmente la tarjeta usando la función
                actualizarEstadoTarjeta(card, "cancelado");

                // Mover la tarjeta a la columna correspondiente
                document.querySelector("#concluido-col .kanban-list").appendChild(card);
                
                // Ocultar el menú desplegable
                card.querySelector('.dropdown').style.display = 'none';
            } else {
                alert("Error al cancelar: " + (data.error || "Desconocido"));
            }
        })
        .catch(err => {
            alert("Error de red: " + err.message);
        });
    }
}

// Funciones varias
function cerrarModalEditar() {
    document.getElementById('editarModal').style.display = 'none';
}

function toggleMenu(element) {
    const menu = element.nextElementSibling;
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
    document.querySelectorAll(".dropdown").forEach(el => {
        if (el !== menu) el.style.display = "none";
    });
}

function verDetalle(elemento) {
    const card = elemento.closest('.kanban-card');
    const idServicio = card.getAttribute('data-id');

    fetch('./AJAX/getservicio.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'id=' + encodeURIComponent(idServicio)
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert('Error: ' + data.error);
            return;
        }
        document.getElementById('detalleTitulo').textContent = data.numero_servicio;
        document.getElementById('detalleTituloServicio').textContent = data.Titulo; // NUEVO: mostrar título
        document.getElementById('detalleEstatus').textContent = data.estatus;
        document.getElementById('detalleTurnado').textContent = data.turnado;
        document.getElementById('detalleFecha').textContent = data.fecha;
        document.getElementById('detalleDescripcion').textContent = data.descripcion;
        document.getElementById('detalleComentario').textContent = data.comentario || 'Sin comentarios'; // Comentario de conclusión

        document.getElementById('detalleModal').style.display = 'block';
    });
}

function cerrarModalDetalle() {
    document.getElementById('detalleModal').style.display = 'none';
}

function toggleFiltroMenu() {
    const menu = document.getElementById("filtroMenu");
    menu.style.display = (menu.style.display === "none" || menu.style.display === "") ? "block" : "none";
}

window.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown-filtro')) {
        document.getElementById("filtroMenu").style.display = "none";
    }
});

function filtrarColumna(tipo) { 
    const columnas = { 
        'concluido': document.getElementById('concluido-col'), 
        'asignado': document.getElementById('asignado-col'), 
        'no-asignado': document.getElementById('no-asignado-col'), 
    }; 
    
    for (let key in columnas) {
         columnas[key].style.display = (tipo === 'todas' || tipo === key) ? 
    'block' : 'none'; 
 } 
    document.getElementById("filtroMenu").style.display = "none"; 
}

let intervaloChat = null;

function abrirChatModal(idServicio) {
    document.getElementById("chatIdServicio").value = idServicio;
    document.getElementById("chatModal").style.display = "block";
    cargarMensajes(idServicio);

    // Evita duplicar intervalos si el usuario abre el mismo chat varias veces
    if (intervaloChat) clearInterval(intervaloChat);

    intervaloChat = setInterval(() => {
        cargarMensajes(idServicio);
    }, 3000); // Cada 3 segundos se actualiza el chat
}

function cerrarChatModal() {
    document.getElementById("chatModal").style.display = "none";

    // Detiene el polling cuando se cierra el modal
    if (intervaloChat) {
        clearInterval(intervaloChat);
        intervaloChat = null;
    }
}

async function cargarMensajes(idServicio) {
    const res = await fetch(`AJAX/obtener_mensaje.php?id_servicio=${idServicio}`);
    const mensajes = await res.json();

    const contenedor = document.getElementById("chatMensajes");
    contenedor.innerHTML = "";

    mensajes.forEach(m => {
        const div = document.createElement("div");
        div.className = `chat-mensaje ${m.emisor}`;
        div.innerText = `${m.emisor === 'cliente' ? 'Tú' : 'Admin'}: ${m.mensaje}`;
        contenedor.appendChild(div);
    });

    contenedor.scrollTop = contenedor.scrollHeight;
}

document.addEventListener("DOMContentLoaded", function () {
  actualizarServicios(); // Cargar al inicio
  setInterval(actualizarServicios, 10000); // Cada 10 segundos
});

function actualizarServicios() {
  fetch('AJAX/servicios_cliente.php')
    .then(response => response.json())
    .then(servicios => {
      // Limpiar columnas
      document.querySelector('#no-asignado-col .kanban-list').innerHTML = '';
      document.querySelector('#asignado-col .kanban-list').innerHTML = '';
      document.querySelector('#concluido-col .kanban-list').innerHTML = '';

      servicios.forEach(servicio => {
        const tarjeta = crearTarjeta(servicio);
        if (servicio.estatus === 'no-asignado') {
          document.querySelector('#no-asignado-col .kanban-list').appendChild(tarjeta);
        } else if (servicio.estatus === 'asignado') {
          document.querySelector('#asignado-col .kanban-list').appendChild(tarjeta);
        } else if (servicio.estatus === 'concluido' || servicio.estatus === 'cancelado') {
          document.querySelector('#concluido-col .kanban-list').appendChild(tarjeta);
        }
      });
    });
}

function crearTarjeta(servicio) {
  const div = document.createElement('div');
  div.className = `kanban-card ${servicio.estatus}`;
  div.setAttribute('data-status', servicio.estatus);
  div.setAttribute('data-id', servicio.id);

  const fecha = new Date(servicio.fecha).toLocaleDateString('es-MX');

  // Construir opciones del menú dinámicamente
  let opciones = `<li onclick="verDetalle(this)">👁 Ver</li>`;
  if (servicio.estatus === "no-asignado" || servicio.estatus === "asignado") {
    opciones += `
      <li onclick="editarDescripcion(this)">✏️ Editar</li>
      <li onclick="cancelarServicio(this)">❌ Cancelar</li>
    `;
  }

  div.innerHTML = `
  <div class="card-header">
    <div class="left">
      <span class="badge ${servicio.estatus}">${servicio.estatus.toUpperCase()}</span>
      <small class="created">📅 ${fecha}</small>
    </div>
    <span class="dots" onclick="toggleMenu(this)">⋮</span>
    <ul class="dropdown">
      ${opciones}
    </ul>
  </div>
  <div class="card-body">
    <div class="tags"><span class="tag">#${servicio.numero}</span></div>
    <h4 class="titulo-servicio">${servicio.titulo || '(Sin título)'}</h4>
    <p class="descripcion">${servicio.descripcion || '(Sin descripción)'}</p>
  </div>
  <div class="kanban-footer">
    <div class="asignado">${servicio.turnado}</div>
    <button class="btn chat">💬</button>
  </div>
`;

  // Chat
  div.querySelector('.chat').addEventListener('click', () => {
    abrirChatModal(servicio.id);
  });

  return div;
}

function editarDescripcion(elemento) {
    const card = elemento.closest('.kanban-card');
    const idServicio = card.getAttribute('data-id');

    fetch('./AJAX/getservicio.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'id=' + encodeURIComponent(idServicio)
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            alert('Error: ' + data.error);
            return;
        }

        document.getElementById('editarIdServicio').value = idServicio;
        document.getElementById('nuevaDescripcion').value = data.descripcion;
        document.getElementById('editarModal').style.display = 'block';
    })
    .catch(err => {
        console.error('Error al cargar descripción completa:', err);
        alert('Ocurrió un error al obtener la descripción');
    });
}

// JavaScript para manejar la funcionalidad de subida de archivos
document.addEventListener('DOMContentLoaded', function() {
    const fileUploadLabel = document.getElementById('fileUploadLabel');
    const fileUploadContainer = document.getElementById('fileUploadContainer');
    const browseFilesBtn = document.getElementById('browseFilesBtn');
    const fileInput = document.getElementById('archivo');
    const fileList = document.getElementById('fileList');
    const successMessage = document.getElementById('successMessage');
    
    // Mostrar el contenedor de carga al hacer clic en el label
    fileUploadLabel.addEventListener('click', function(e) {
        e.preventDefault();
        fileUploadContainer.classList.add('active');
    });
    
    // Permitir hacer clic en el botón de "Seleccionar archivos"
    browseFilesBtn.addEventListener('click', function() {
        fileInput.click();
    });
    
    // Manejar la selección de archivos
    fileInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });
    
    // Funcionalidad de arrastrar y soltar
    fileUploadContainer.addEventListener('dragover', function(e) {
        e.preventDefault();
        fileUploadContainer.classList.add('drag-over');
    });
    
    fileUploadContainer.addEventListener('dragleave', function() {
        fileUploadContainer.classList.remove('drag-over');
    });
    
    fileUploadContainer.addEventListener('drop', function(e) {
        e.preventDefault();
        fileUploadContainer.classList.remove('drag-over');
        
        if (e.dataTransfer.files.length) {
            handleFiles(e.dataTransfer.files);
            // También actualizamos el input file original
            fileInput.files = e.dataTransfer.files;
        }
    });
    
    // Función para manejar los archivos seleccionados
    function handleFiles(files) {
        fileList.innerHTML = '';
        
        if (files.length > 0) {
            successMessage.style.display = 'block';
            
            // Mostrar cada archivo en la lista
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                
                const fileName = document.createElement('span');
                fileName.className = 'file-name';
                fileName.textContent = file.name;
                
                const fileRemove = document.createElement('span');
                fileRemove.className = 'file-remove';
                fileRemove.textContent = 'X';
                fileRemove.addEventListener('click', function() {
                    fileItem.remove();
                    // Limpiar el input file
                    fileInput.value = '';
                    if (fileList.children.length === 0) {
                        successMessage.style.display = 'none';
                    }
                });
                
                fileItem.appendChild(fileName);
                fileItem.appendChild(fileRemove);
                fileList.appendChild(fileItem);
            }
        } else {
            successMessage.style.display = 'none';
        }
    }
});