
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
    document.addEventListener("click", function(e) {
        if (!e.target.matches(".dots")) {
            document.querySelectorAll(".dropdown").forEach(el => el.style.display = "none");
        }
    });

    document.getElementById('toggleSidebar').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });

    const toggle = document.getElementById("togglePerfil");
    const item = toggle.parentElement;
    toggle.addEventListener("click", function(e) {
        e.preventDefault();
        item.classList.toggle("open");
    });

    // Modal nueva solicitud
    const modal = document.getElementById("nuevaSolicitudModal");
    document.getElementById("nuevaSolicitudBtn").addEventListener("click", () => modal.style.display = "block");
    document.getElementById("cerrarModal").addEventListener("click", () => modal.style.display = "none");

    window.addEventListener("click", function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });

    const fileInput = document.getElementById("archivo");
    const fileName = document.getElementById("fileName");
    fileInput.addEventListener("change", function () {
        fileName.textContent = this.files.length > 0 ? this.files[0].name : "";
    });

    document.getElementById("solicitudForm").addEventListener("submit", function (e) {
        e.preventDefault();
        alert("Solicitud enviada con éxito!");
        modal.style.display = "none";
        this.reset();
        fileName.textContent = "";
    });
});

function toggleMenu(element) {
    const menu = element.nextElementSibling;
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
    document.querySelectorAll(".dropdown").forEach(el => {
        if (el !== menu) el.style.display = "none";
    });
}

function verDetalle() {
    document.getElementById("detalleTitulo").innerText = "SEyT-UI-160 - Generar sesión Zoom";
    document.getElementById("detalleEstatus").innerText = "CANCELADO";
    document.getElementById("detalleTurnado").innerText = "DIAZ TORAL CARLOS ARTURO";
    document.getElementById("detalleFecha").innerText = "03/04/2025";
    document.getElementById("detalleDescripcion").innerText = "Solicitud para generar una sesión de videoconferencia a través de la aplicación Zoom...";
    document.getElementById("detalleModal").style.display = "block";
}

function cerrarModalDetalle() {
    document.getElementById("detalleModal").style.display = "none";
}