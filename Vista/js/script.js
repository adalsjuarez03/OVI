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

// ========== DRAG AND DROP ==========
function allowDrop(ev) {
  ev.preventDefault();
}

function drag(ev) {
  ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev) {
  ev.preventDefault();
  const data = ev.dataTransfer.getData("text");
  const dragged = document.getElementById(data);
  const target = ev.target.closest(".kanban-card");

  if (!dragged || dragged === target) return;

  if (target) {
    const bounding = target.getBoundingClientRect();
    const offset = ev.clientY - bounding.top;
    if (offset > bounding.height / 2) {
      target.parentNode.insertBefore(dragged, target.nextSibling);
    } else {
      target.parentNode.insertBefore(dragged, target);
    }
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
  if (container && dragged) {
    container.appendChild(dragged);
  }
}

// ========== BOTÓN ABRIR NUEVA SOLICITUD ==========
document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("nuevaSolicitudModal");
  const btn = document.getElementById("nuevaSolicitudBtn");
  const closeBtn = document.getElementById("cerrarModal");

  if (modal && btn && closeBtn) {
    btn.addEventListener("click", () => {
      modal.style.display = "block";
    });

    closeBtn.addEventListener("click", () => {
      modal.style.display = "none";
    });

    window.addEventListener("click", function (event) {
      if (event.target === modal) {
        modal.style.display = "none";
      }
    });

    const fileInput = document.getElementById("archivo");
    const fileName = document.getElementById("fileName");

    if (fileInput && fileName) {
      fileInput.addEventListener("change", function () {
        fileName.textContent = this.files.length > 0 ? this.files[0].name : "";
      });
    }

    const form = document.getElementById("solicitudForm");
    if (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
        alert("Solicitud enviada con éxito!");
        modal.style.display = "none";
        this.reset();
        const fileName = document.getElementById("fileName");
        if (fileName) fileName.textContent = "";
      });
    }
  }
});

// ========== DETALLE DEL SERVICIO ==========
function verDetalle(idServicio) {
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
      document.getElementById('detalleEstatus').textContent = data.estatus;
      document.getElementById('detalleTurnado').textContent = data.turnado;
      document.getElementById('detalleFecha').textContent = data.fecha;
      document.getElementById('detalleDescripcion').textContent = data.descripcion;

      document.getElementById('detalleModal').style.display = 'block';
    });
}

function cerrarModalDetalle() {
  document.getElementById("detalleModal").style.display = "none";
}

window.addEventListener("click", function (event) {
  const modal = document.getElementById("detalleModal");
  if (event.target === modal) {
    modal.style.display = "none";
  }
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

function cerrarChatModal() {
  document.getElementById("chatModal").style.display = "none";
}

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
