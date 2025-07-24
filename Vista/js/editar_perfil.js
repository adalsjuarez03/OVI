
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = input.nextElementSibling;
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Validación de contraseña
        document.querySelector('form').addEventListener('submit', function(e) {
            const nuevoPassword = document.getElementById('nuevo_password').value;
            const confirmarPassword = document.getElementById('confirmar_password').value;
            
            if (nuevoPassword !== confirmarPassword) {
                alert('Las contraseñas no coinciden');
                e.preventDefault();
            }
            
            // Si se llena algún campo de contraseña, todos son requeridos
            const passwordActual = document.getElementById('password_actual').value;
            if ((nuevoPassword || confirmarPassword) && !passwordActual) {
                alert('Debe ingresar su contraseña actual para cambiarla');
                e.preventDefault();
            }
        });
