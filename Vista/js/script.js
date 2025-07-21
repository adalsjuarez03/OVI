
// Menú desplegable de "Mi Perfil"
document.addEventListener("DOMContentLoaded", function() {
    const toggle = document.getElementById("togglePerfil");
    const item = toggle.parentElement;
    toggle.addEventListener("click", function(e) {
        e.preventDefault();
        item.classList.toggle("open");
    });
});


// Drag and Drop
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
    let target = ev.target.closest('.kanban-card');

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
        // Si soltamos fuera de una tarjeta, lo agregamos al final
        const container = ev.target.closest('.kanban-container');
        if (container) {
            container.appendChild(dragged);
        }
    }
}




// Menú de 3 puntos (⋮)
function toggleMenu(element) {
    const menu = element.nextElementSibling;
    menu.style.display = (menu.style.display === "block") ? "none" : "block";

    // Cierra otros menús abiertos
    document.querySelectorAll(".dropdown").forEach(el => {
        if (el !== menu) el.style.display = "none";
    });
}

// Cerrar menú si se hace clic fuera
document.addEventListener("click", function(e) {
    if (!e.target.matches(".dots")) {
        document.querySelectorAll(".dropdown").forEach(el => el.style.display = "none");
    }
});

function dropAtEnd(ev) {
    ev.preventDefault();
    const data = ev.dataTransfer.getData("text");
    const dragged = document.getElementById(data);
    const container = ev.target.closest('.kanban-container');
    if (container && dragged) {
        container.appendChild(dragged);
    }
}

// Script para el modal de nueva solicitud
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById("nuevaSolicitudModal");
        const btn = document.getElementById("nuevaSolicitudBtn");
        const closeBtn = document.getElementById("cerrarModal");
        const fileInput = document.getElementById("archivo");
        const fileName = document.getElementById("fileName");

        // Mostrar modal al hacer clic en el botón
        btn.addEventListener("click", function() {
            modal.style.display = "block";
        });

        // Cerrar modal
        closeBtn.addEventListener("click", function() {
            modal.style.display = "none";
        });

        // Cerrar al hacer clic fuera del modal
        window.addEventListener("click", function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        });

        // Mostrar nombre del archivo seleccionado
        fileInput.addEventListener("change", function() {
            if (this.files.length > 0) {
                fileName.textContent = this.files[0].name;
            } else {
                fileName.textContent = "";
            }
        });

        // Manejar el envío del formulario
        document.getElementById("solicitudForm").addEventListener("submit", function(e) {
            e.preventDefault();
            
            // Aquí puedes agregar la lógica para enviar el formulario
            alert("Solicitud enviada con éxito!");
            modal.style.display = "none";
            
            // Resetear el formulario
            this.reset();
            fileName.textContent = "";
        });
    });
