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

    // Agrega eventos a los botones ✏️ Editar
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


    // Enviar cambios del formulario editar
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
});

// Función para cerrar modal de edición
function cerrarModalEditar() {
    document.getElementById('editarModal').style.display = 'none';
}

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
        document.getElementById('detalleTitulo').textContent = data.numero_servicio;
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

window.addEventListener("click", function(event) {
    const modal = document.getElementById("detalleModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
});

// Filtro de columnas
function toggleFiltroMenu() {
    const menu = document.getElementById("filtroMenu");
    menu.style.display = (menu.style.display === "none" || menu.style.display === "") ? "block" : "none";
}

// Ocultar menú filtro al hacer clic fuera
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
