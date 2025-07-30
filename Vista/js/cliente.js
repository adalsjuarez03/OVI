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
                        // Cambiar visualmente la tarjeta
                        card.setAttribute("data-status", "cancelado");
                        const badge = card.querySelector(".badge");
                        badge.textContent = "CANCELADO";
                        badge.className = "badge cancelado";

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
    document.getElementById("nuevaSolicitudBtn").addEventListener("click", () => modal.style.display = "block");
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
        document.getElementById('detalleEstatus').textContent = data.estatus;
        document.getElementById('detalleTurnado').textContent = data.turnado;
        document.getElementById('detalleFecha').textContent = data.fecha;
        document.getElementById('detalleDescripcion').textContent = data.descripcion;

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
        columnas[key].style.display = (tipo === 'todas' || tipo === key) ? 'block' : 'none';
    }

    document.getElementById("filtroMenu").style.display = "none";
}

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
        div.innerText = `${m.emisor === 'cliente' ? 'Tú' : 'Admin'}: ${m.mensaje}`;
        contenedor.appendChild(div);
    });

    contenedor.scrollTop = contenedor.scrollHeight;
}
