
    function abrirEditar(datos) {
        document.getElementById('edit-id').value = datos.id_usuario;
        document.getElementById('edit-nombre').value = datos.nombre;
        document.getElementById('edit-apellido').value = datos.apellido;
        document.getElementById('edit-correo').value = datos.correo;
        document.getElementById('edit-telefono').value = datos.telefono;
        document.getElementById('edit-rol').value = datos.rol;
        document.getElementById('modalEditar').showModal();
    }

    function cerrarEditar() {
        document.getElementById('modalEditar').close();
    }

    function abrirEliminar(id) {
        document.getElementById('delete-id').value = id;
        document.getElementById('modalEliminar').showModal();
    }

    function cerrarEliminar() {
        document.getElementById('modalEliminar').close();
    }
