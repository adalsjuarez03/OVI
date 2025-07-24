<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: Login.php');
    exit();
}

require_once '../Modelo\Conexion.php';
$conexion = Conexion::conectar();

// Obtener datos actuales del usuario
$usuario_id = $_SESSION['usuario'];
$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id_usuario= ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    
    // Actualizar en la base de datos
    $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, correo = ?, telefono = ? WHERE id = ?");
    $stmt->bind_param("sssi", $nombre, $correo, $telefono, $usuario_id);
    
    if ($stmt->execute()) {
        $_SESSION['usuario'] = $nombre;
        $mensaje = "Perfil actualizado correctamente";
        $tipo_mensaje = "success";
        
        // Actualizar datos locales
        $usuario['nombre'] = $nombre;
        $usuario['correo'] = $correo;
        $usuario['telefono'] = $telefono;
    } else {
        $mensaje = "Error al actualizar el perfil";
        $tipo_mensaje = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a6fa5;
            --secondary-color: #166088;
            --accent-color: #4fc3f7;
            --background-color: #f8f9fa;
            --card-color: #ffffff;
            --text-color: #333333;
            --border-color: #e0e0e0;
            --success-color: #4caf50;
            --error-color: #f44336;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .profile-header h1 {
            font-size: 28px;
            color: var(--secondary-color);
            font-weight: 600;
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-container img {
            height: 50px;
            margin-left: 15px;
        }

        .profile-content {
            display: flex;
            gap: 30px;
        }

        .profile-sidebar {
            width: 300px;
            background-color: var(--card-color);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
            height: fit-content;
        }

        .profile-avatar {
            text-align: center;
            margin-bottom: 20px;
        }

        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--accent-color);
            margin: 0 auto 15px;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: #666;
        }

        .profile-info h2 {
            font-size: 20px;
            margin-bottom: 5px;
            color: var(--secondary-color);
        }

        .profile-info p {
            color: #666;
            margin-bottom: 15px;
        }

        .profile-stats {
            margin-top: 20px;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .profile-main {
            flex: 1;
            background-color: var(--card-color);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .section-title {
            font-size: 22px;
            margin-bottom: 20px;
            color: var(--secondary-color);
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            color: var(--accent-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-col {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"],
        textarea,
        select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        input[type="password"]:focus,
        textarea:focus,
        select:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(79, 195, 247, 0.2);
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s, transform 0.2s;
            text-align: center;
        }

        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        @media (max-width: 768px) {
            .profile-content {
                flex-direction: column;
            }

            .profile-sidebar {
                width: 100%;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <h1>Editar Perfil</h1>
            <div class="logo-container">
                <img src="https://ovi.economiaytrabajo.chiapas.gob.mx/static/LOGO.png" alt="Logo Oficina Virtual">
            </div>
        </div>

        <div class="profile-content">
            <div class="profile-sidebar">
                <div class="profile-avatar">
                    <div class="avatar">
                        <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
                    </div>
                    <h2><?php echo htmlspecialchars($usuario['nombre']); ?></h2>
                    <p>Cliente</p>
                </div>

                <div class="profile-info">
                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($usuario['correo']); ?></p>
                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($usuario['telefono'] ?? 'No especificado'); ?></p>
                </div>

                <div class="profile-stats">
                    <div class="stat-item">
                        <span>Servicios activos</span>
                        <span>3</span>
                    </div>
                    <div class="stat-item">
                        <span>Servicios completados</span>
                        <span>12</span>
                    </div>
                    <div class="stat-item">
                        <span>Miembro desde</span>
                        <span><?php echo date('Y', strtotime($usuario['fecha_registro'] ?? 'now')); ?></span>
                    </div>
                </div>
            </div>

            <div class="profile-main">
                <?php if (isset($mensaje)): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                        <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>

                <form action="edit_profile.php" method="POST">
                    <h2 class="section-title"><i class="fas fa-user-edit"></i> Información Personal</h2>

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="nombre">Nombre completo</label>
                                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="correo">Correo electrónico</label>
                                <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <h2 class="section-title" style="margin-top: 40px;"><i class="fas fa-lock"></i> Seguridad</h2>

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group password-toggle">
                                <label for="password_actual">Contraseña actual</label>
                                <input type="password" id="password_actual" name="password_actual">
                                <i class="fas fa-eye" onclick="togglePassword('password_actual')"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group password-toggle">
                                <label for="nuevo_password">Nueva contraseña</label>
                                <input type="password" id="nuevo_password" name="nuevo_password">
                                <i class="fas fa-eye" onclick="togglePassword('nuevo_password')"></i>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group password-toggle">
                                <label for="confirmar_password">Confirmar nueva contraseña</label>
                                <input type="password" id="confirmar_password" name="confirmar_password">
                                <i class="fas fa-eye" onclick="togglePassword('confirmar_password')"></i>
                            </div>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn">Guardar cambios</button>
                        <a href="Cliente.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
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
    </script>
</body>
</html>