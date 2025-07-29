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

    // Enviar solicitud con AJAX (fetch)
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
                    location.reload(); // Recargar para mostrar el nuevo servicio
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
});

// Mostrar menú de 3 puntos
function toggleMenu(element) {
    const menu = element.nextElementSibling;
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
    document.querySelectorAll(".dropdown").forEach(el => {
        if (el !== menu) el.style.display = "none";
    });
}

// VER DETALLE desde BD
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

        document.getElementById('detalleTitulo').textContent = 'Servicio #' + idServicio;
        document.getElementById('detalleEstatus').textContent = data.estatus;
        document.getElementById('detalleTurnado').textContent = data.turnado;
        document.getElementById('detalleFecha').textContent = data.fecha;
        document.getElementById('detalleDescripcion').textContent = data.descripcion;

        document.getElementById('detalleModal').style.display = 'block';
    });
}

// Cerrar modal detalle
function cerrarModalDetalle() {
    document.getElementById('detalleModal').style.display = 'none';
}
