<?php
session_start();


// Verificar que el usuario esté autenticado como "cliente"
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: Login.php');
    exit();
}

require_once '../Modelo/Conexion.php';
$conexion = Conexion::conectar();

// Obtener ID del usuario desde la sesión
$usuario_id = $_SESSION['usuario'];

// Consultar datos actuales del usuario
$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

// Verificar si se encontró el usuario
if (!$usuario) {
    die("Usuario no encontrado.");
}
// Procesar actualización si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];

    // Actualizar los datos en la base de datos
    $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, correo = ?, telefono = ? WHERE id_usuario = ?");
    $stmt->bind_param("sssi", $nombre, $correo, $telefono, $usuario_id);

    if ($stmt->execute()) {
        $mensaje = "Perfil actualizado correctamente";
        $tipo_mensaje = "success";

        // Actualizar los datos locales para reflejarlos en pantalla
        $usuario['nombre'] = $nombre;
        $usuario['correo'] = $correo;
        $usuario['telefono'] = $telefono;

        // (Opcional) Guardar nombre en sesión si lo deseas
        // $_SESSION['nombre'] = $nombre;

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
    <link rel="stylesheet" href="./CSS/editar_perfil.css">
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

<script src="./js/editar_perfil.js"></script>
</body>
</html>
