// ========== MENÚ DESPLEGABLE PERFIL ==========
document.addEventListener("DOMContentLoaded", function () {
  const toggle = document.getElementById("togglePerfil");
  if (toggle) {
    const item = toggle.parentElement;
    toggle.addEventListener("click", function (e) {
      e.preventDefault();
      item.classList.toggle("open");
    });
  }
});

// ========== MENÚ DE 3 PUNTOS (⋮) ==========
function toggleMenu(element) {
  const menu = element.nextElementSibling;
  menu.style.display = (menu.style.display === "block") ? "none" : "block";

  document.querySelectorAll(".dropdown").forEach(el => {
    if (el !== menu) el.style.display = "none";
  });
}

document.addEventListener("click", function (e) {
  if (!e.target.matches(".dots")) {
    document.querySelectorAll(".dropdown").forEach(el => el.style.display = "none");
  }
});

// ========== FUNCIONES PARA ARCHIVOS ==========
function abrirArchivo(rutaArchivo, nombreArchivo) {
  if (!rutaArchivo) {
    alert('No hay archivo adjunto para este servicio.');
    return;
  }
  const url = '../' + rutaArchivo;
  window.open(url, '_blank');
}

function descargarArchivo(rutaArchivo, nombreArchivo) {
  if (!rutaArchivo) {
    alert('No hay archivo para descargar.');
    return;
  }
  const url = '../' + rutaArchivo;
  const a = document.createElement('a');
  a.href = url;
  a.download = nombreArchivo || 'archivo';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
}

// ========== DRAG AND DROP ==========
function allowDrop(ev) { ev.preventDefault(); }
function drag(ev) { ev.dataTransfer.setData("text", ev.target.id); }
function drop(ev) {
  ev.preventDefault();
  const data = ev.dataTransfer.getData("text");
  const dragged = document.getElementById(data);
  const target = ev.target.closest(".kanban-card");

  if (!dragged || dragged === target) return;

  if (target) {
    const bounding = target.getBoundingClientRect();
    const offset = ev.clientY - bounding.top;
    if (offset > bounding.height / 2) target.parentNode.insertBefore(dragged, target.nextSibling);
    else target.parentNode.insertBefore(dragged, target);
  } else {
    const container = ev.target.closest(".kanban-container");
    if (container) container.appendChild(dragged);
  }
}
function dropAtEnd(ev) {
  ev.preventDefault();
  const data = ev.dataTransfer.getData("text");
  const dragged = document.getElementById(data);
  const container = ev.target.closest(".kanban-container");
  if (container && dragged) container.appendChild(dragged);
}

// ========== BOTÓN ABRIR NUEVA SOLICITUD + SUBIDA DE ARCHIVOS ==========
document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("nuevaSolicitudModal");
  const btn = document.getElementById("nuevaSolicitudBtn");
  const closeBtn = document.getElementById("cerrarModal");
  const fileUploadLabel = document.getElementById('fileUploadLabel');
  const fileUploadContainer = document.getElementById('fileUploadContainer');
  const browseFilesBtn = document.getElementById('browseFilesBtn');
  const fileInput = document.getElementById("archivo");
  const fileName = document.getElementById("fileName");
  const fileList = document.getElementById('fileList');
  const successMessage = document.getElementById('successMessage');
  const form = document.getElementById("solicitudForm");

  if (!modal || !btn || !closeBtn || !fileInput) return;

  btn.addEventListener("click", () => { modal.style.display = "block"; });
  closeBtn.addEventListener("click", () => { modal.style.display = "none"; });
  window.addEventListener("click", function (event) {
    if (event.target === modal) modal.style.display = "none";
  });

  if (fileInput && fileName) {
    fileInput.addEventListener("change", function () {
      fileName.textContent = this.files.length > 0 ? this.files[0].name : "";
      handleFiles(this.files);
    });
  }

  if (fileUploadLabel && fileUploadContainer) {
    fileUploadLabel.addEventListener('click', function(e){
      e.preventDefault();
      fileUploadContainer.classList.add('active');
    });
  }

  if (browseFilesBtn && fileInput) {
    browseFilesBtn.addEventListener('click', function(){ fileInput.click(); });
  }

  if (fileUploadContainer) {
    fileUploadContainer.addEventListener('dragover', function(e){
      e.preventDefault();
      fileUploadContainer.classList.add('drag-over');
    });
    fileUploadContainer.addEventListener('dragleave', function(){
      fileUploadContainer.classList.remove('drag-over');
    });
    fileUploadContainer.addEventListener('drop', function(e){
      e.preventDefault();
      fileUploadContainer.classList.remove('drag-over');
      if (e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        handleFiles(e.dataTransfer.files);
      }
    });
  }

  function handleFiles(files) {
    if (!fileList || !successMessage) return;
    fileList.innerHTML = '';
    if (files.length > 0) {
      successMessage.style.display = 'block';
      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';

        const fileNameSpan = document.createElement('span');
        fileNameSpan.className = 'file-name';
        fileNameSpan.textContent = file.name;

        const fileRemove = document.createElement('span');
        fileRemove.className = 'file-remove';
        fileRemove.textContent = 'X';
        fileRemove.addEventListener('click', function(){
          fileItem.remove();
          fileInput.value = '';
          if (fileList.children.length === 0) successMessage.style.display = 'none';
        });

        fileItem.appendChild(fileNameSpan);
        fileItem.appendChild(fileRemove);
        fileList.appendChild(fileItem);
      }
    } else {
      successMessage.style.display = 'none';
    }
  }

  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(this);

      fetch("./AJAX/registrar_archivo_admin.php", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert("Solicitud guardada con éxito!" + (data.archivo_subido ? " Archivo subido correctamente." : ""));
          location.reload();
        } else {
          alert("Error: " + data.error);
        }
      })
      .catch(err => {
        console.error(err);
        alert("Error de conexión con el servidor.");
      });

      modal.style.display = "none";
      this.reset();
      if (fileName) fileName.textContent = "";
      if (fileList) fileList.innerHTML = '';
      if (successMessage) successMessage.style.display = 'none';
    });
  }
});

// ========== DETALLE DEL SERVICIO CON COMENTARIO Y ARCHIVO ==========
function verDetalle(idServicio) {
  fetch('./AJAX/getservicio.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'id=' + encodeURIComponent(idServicio)
  })
  .then(response => response.json())
  .then(data => {
    if (data.error) { alert('Error: ' + data.error); return; }

    document.getElementById('detalleTitulo').textContent = `${data.Titulo} (${data.numero_servicio})`;
    document.getElementById('detalleEstatus').textContent = data.estatus;
    document.getElementById('detalleTurnado').textContent = data.turnado;
    document.getElementById('detalleFecha').textContent = data.fecha;
    document.getElementById('detalleDescripcion').textContent = data.descripcion;

    let comentarioDiv = document.getElementById('detalleComentario');
    comentarioDiv.textContent = data.comentario || 'Sin comentarios';

    const archivoSection = document.getElementById('archivoSection');
    const archivoNombre = document.getElementById('archivoNombre');
    const archivoLink = document.getElementById('archivoLink');
    const descargarBtn = document.getElementById('descargarBtn');

    if (data.archivo_ruta && data.archivo_ruta.trim() !== '') {
      archivoSection.style.display = 'block';
      archivoNombre.textContent = data.archivo_nombre || 'Archivo adjunto';
      archivoLink.href = '../' + data.archivo_ruta;
      descargarBtn.onclick = function() {
        descargarArchivo(data.archivo_ruta, data.archivo_nombre);
      };
    } else {
      archivoSection.style.display = 'none';
    }

    document.getElementById('detalleModal').style.display = 'block';
  });
}
function cerrarModalDetalle() { document.getElementById("detalleModal").style.display = "none"; }
window.addEventListener("click", function (event) {
  const modal = document.getElementById("detalleModal");
  if (event.target === modal) modal.style.display = "none";
});

// ========== COLAPSAR SIDEBAR ==========
const toggleSidebar = document.getElementById("toggleSidebar");
if (toggleSidebar) {
  toggleSidebar.addEventListener("click", function () {
    document.getElementById("sidebar").classList.toggle("collapsed");
  });
}

// ========== CHAT DEL ADMINISTRADOR ==========
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".kanban-card .chat").forEach(btn => {
    btn.addEventListener("click", function () {
      const card = this.closest(".kanban-card");
      const idServicio = card.id.replace("servicio-", "");
      abrirChatModal(idServicio);
    });
  });

  const formChat = document.getElementById("formChat");
  if (formChat) {
    formChat.addEventListener("submit", async function (e) {
      e.preventDefault();
      const idServicio = document.getElementById("chatIdServicio").value;
      const mensaje = document.getElementById("mensajeChat").value;

      const res = await fetch("AJAX/guardar_mensaje.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id_servicio=${idServicio}&mensaje=${encodeURIComponent(mensaje)}&emisor=admin`
      });

      if (res.ok) {
        document.getElementById("mensajeChat").value = "";
        cargarMensajes(idServicio);
      }
    });
  }
});

function abrirChatModal(idServicio) {
  document.getElementById("chatIdServicio").value = idServicio;
  document.getElementById("chatModal").style.display = "block";
  cargarMensajes(idServicio);
}
function cerrarChatModal() { document.getElementById("chatModal").style.display = "none"; }

async function cargarMensajes(idServicio) {
  const res = await fetch(`AJAX/obtener_mensaje.php?id_servicio=${idServicio}`);
  const mensajes = await res.json();
  const contenedor = document.getElementById("chatMensajes");
  contenedor.innerHTML = "";
  mensajes.forEach(m => {
    const div = document.createElement("div");
    div.className = `chat-mensaje ${m.emisor}`;
    div.innerText = `${m.emisor === 'admin' ? 'Tú' : 'Cliente'}: ${m.mensaje}`;
    contenedor.appendChild(div);
  });
  contenedor.scrollTop = contenedor.scrollHeight;
}

// ========== CONCLUIR SERVICIO CON COMENTARIO ==========
let servicioConcluirId = null;
function concluirServicio(idServicio) {
  servicioConcluirId = idServicio;
  document.getElementById("comentarioConcluir").value = "";
  document.getElementById("concluirModal").style.display = "block";
}
function cerrarConcluirModal() {
  document.getElementById("concluirModal").style.display = "none";
}
const enviarConcluirBtn = document.getElementById("enviarConcluirBtn");
if (enviarConcluirBtn) {
  enviarConcluirBtn.addEventListener("click", function () {
    const comentario = document.getElementById("comentarioConcluir").value.trim();
    if (!comentario) {
      alert("Por favor, escribe un comentario antes de concluir el servicio.");
      return;
    }
    if (!servicioConcluirId) return;

    fetch('./AJAX/concluir_servicio.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `id_servicio=${encodeURIComponent(servicioConcluirId)}&comentario=${encodeURIComponent(comentario)}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const card = document.getElementById('servicio-' + servicioConcluirId);
        card.classList.remove('no-asignado', 'asignado');
        card.classList.add('concluido');
        const badge = card.querySelector('.badge');
        badge.textContent = 'CONCLUIDO';
        badge.className = 'badge concluido';
        cerrarConcluirModal();
        alert('Servicio concluido con comentario.');
      } else {
        alert('Error al concluir el servicio: ' + data.error);
      }
    })
    .catch(err => {
      console.error(err);
      alert('Error al conectar con el servidor.');
    });
  });
}
window.addEventListener("click", function (event) {
  const modal = document.getElementById("concluirModal");
  if (event.target === modal) modal.style.display = "none";
});

// ========== MENÚ DE FILTROS ==========
function toggleFiltroMenu() {
  const menu = document.getElementById("filtroMenu");
  if (menu) menu.style.display = (menu.style.display === "block") ? "none" : "block";
}
window.addEventListener("click", function(e) {
  const menu = document.getElementById("filtroMenu");
  const filtroBtn = document.querySelector(".btn.filter");
  if (menu && filtroBtn && !filtroBtn.contains(e.target) && !menu.contains(e.target)) {
    menu.style.display = "none";
  }
});
function filtrarColumna(tipo) {
  const tarjetas = document.querySelectorAll('.kanban-card');
  tarjetas.forEach(card => {
    const estatus = card.getAttribute('data-status').toLowerCase();
    switch(tipo) {
      case 'todas': card.style.display = 'block'; break;
      case 'no-asignado': card.style.display = (estatus === 'no-asignado' || estatus === 'no asignado') ? 'block' : 'none'; break;
      case 'asignado': card.style.display = (estatus === 'asignado') ? 'block' : 'none'; break;
      case 'concluido': card.style.display = (estatus === 'concluido' || estatus === 'cancelado') ? 'block' : 'none'; break;
      default: card.style.display = 'block';
    }
  });
  const menu = document.getElementById("filtroMenu");
  if (menu) menu.style.display = 'none';
}

// ========== ASIGNAR SERVICIO ==========
function asignarServicio(idServicio) {
  if (!confirm("¿Deseas asignarte este servicio?")) return;
  fetch("./AJAX/asignar_servicio.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "id_servicio=" + encodeURIComponent(idServicio)
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      const card = document.getElementById("servicio-" + idServicio);
      card.classList.remove("no-asignado");
      card.classList.add("asignado");
      const badge = card.querySelector(".badge");
      badge.textContent = "ASIGNADO";
      badge.className = "badge asignado";
      alert("Servicio asignado correctamente.");
    } else {
      alert("Error al asignar: " + data.error);
    }
  })
  .catch(err => {
    console.error(err);
    alert("Error de conexión con el servidor.");
  });
}
