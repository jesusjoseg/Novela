<?php
session_start();
require_once 'HHH/Conexion.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$mensaje_perfil = "";
$error_perfil = "";

// 1. Procesar actualización de perfil (Nombre y Foto de Perfil)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar_perfil') {
    $nuevo_nombre = trim($_POST['nombre'] ?? '');
    $avatar_url = trim($_POST['avatar_url'] ?? '');

    if (!empty($nuevo_nombre)) {
        try {
            $stmtUpdate = $conexion->prepare('UPDATE usuario SET "Nombre" = :nombre, "Avatar" = :avatar WHERE id = :id');
            $stmtUpdate->execute([
                ':nombre' => $nuevo_nombre,
                ':avatar' => $avatar_url,
                ':id'     => $usuario_id
            ]);
            $_SESSION['usuario_nombre'] = $nuevo_nombre;
            $mensaje_perfil = "¡Perfil actualizado correctamente!";
        } catch (PDOException $e) {
            error_log("Error al actualizar perfil: " . $e->getMessage());
            $error_perfil = "Error al actualizar los datos en la base de datos.";
        }
    } else {
        $error_perfil = "El nombre no puede estar vacío.";
    }
}

// 2. Obtener la información completa del usuario
$datos_usuario = null;
try {
    $stmtUser = $conexion->prepare('SELECT id, "Nombre", "Correo", "Rol", "fecha_Registro", "Avatar" FROM usuario WHERE id = :id');
    $stmtUser->execute([':id' => $usuario_id]);
    $datos_usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al consultar datos de usuario: " . $e->getMessage());
}

// 3. Obtener Novelas que le gustan / Favoritos
$favoritos = [];
try {
    $sqlFav = 'SELECT n.id, n."Titulo", n."Portada", n."link", n."autor_original"
               FROM favoritos f 
               INNER JOIN novela n ON f.novela_id = n.id 
               WHERE f.usuario_id = :usuario_id 
               ORDER BY f.id DESC';
    $stmtFav = $conexion->prepare($sqlFav);
    $stmtFav->execute([':usuario_id' => $usuario_id]);
    $favoritos = $stmtFav->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al obtener favoritos: " . $e->getMessage());
}

// Avatar por defecto o el asignado por el usuario
$nombre_usuario = $datos_usuario['Nombre'] ?? 'Usuario';
$avatar_usuario = !empty($datos_usuario['Avatar']) ? $datos_usuario['Avatar'] : null;
$inicial_avatar = strtoupper(substr($nombre_usuario, 0, 1));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Foxnovel</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container" style="max-width: 950px; margin: 30px auto;">
        
        <!-- HEADER DE PERFIL CON AVATAR Y BOTÓN DE CREADOR -->
        <div class="profile-header">
            <div class="profile-avatar-box">
                <?php if ($avatar_usuario): ?>
                    <img src="<?php echo htmlspecialchars($avatar_usuario); ?>" alt="Foto de perfil" class="profile-avatar-img">
                <?php else: ?>
                    <div class="profile-avatar-text"><?php echo $inicial_avatar; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($nombre_usuario); ?></h2>
                <p class="profile-email"><?php echo htmlspecialchars($datos_usuario['Correo'] ?? ''); ?></p>
                <span class="badge-rol"><?php echo htmlspecialchars(strtoupper($datos_usuario['Rol'] ?? 'USUARIO')); ?></span>
            </div>

            <div class="profile-header-actions">
                <!-- BOTÓN PARA IR AL PANEL DE CREADOR -->
                <a href="creador.php" class="btn-creador">✍️ Ir a Creador</a>
                <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>

        <!-- TARJETAS DE ESTADÍSTICAS RÁPIDAS -->
        <div class="profile-stats">
            <div class="stat-card">
                <span class="stat-number">❤️ <?php echo count($favoritos); ?></span>
                <span class="stat-label">Novelas Guardadas</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">
                    <?php 
                    $fecha = $datos_usuario['fecha_Registro'] ?? null;
                    echo $fecha ? date('d/m/Y', strtotime($fecha)) : 'Reciente'; 
                    ?>
                </span>
                <span class="stat-label">Miembro Desde</span>
            </div>
        </div>

        <!-- Módulos / Pestañas principales -->
        <aside class="filter-sidebar form-sidebar" style="margin-top: 25px;">
            <div class="tabs-nav">
                <button class="tab-btn active" onclick="switchTab('tab-favoritos')">❤️ Novelas Guardadas (<?php echo count($favoritos); ?>)</button>
                <button class="tab-btn" onclick="switchTab('tab-configuracion')">⚙️ Configuración de Perfil</button>
            </div>

            <?php if ($mensaje_perfil): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($mensaje_perfil); ?></div>
            <?php endif; ?>
            
            <?php if ($error_perfil): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error_perfil); ?></div>
            <?php endif; ?>

            <!-- PESTAÑA 1: NOVELAS QUE LE GUSTAN / FAVORITOS -->
            <div id="tab-favoritos" class="tab-content active">
                <h3 style="color: #ff6b35; margin-bottom: 15px;">Tus Novelas Favoritas</h3>
                
                <?php if (!empty($favoritos)): ?>
                    <div class="grid-capitulos">
                        <?php foreach ($favoritos as $fav): ?>
                            <div class="card-capitulo">
                                <a href="<?php echo htmlspecialchars($fav['link']); ?>">
                                    <img src="<?php echo htmlspecialchars($fav['Portada']); ?>" alt="<?php echo htmlspecialchars($fav['Titulo']); ?>" class="card-portada">
                                </a>
                                <div class="card-info">
                                    <span class="card-novela-titulo"><?php echo htmlspecialchars($fav['Titulo']); ?></span>
                                    <?php if (!empty($fav['autor_original'])): ?>
                                        <small style="color: #b0a8a0;">Autor: <?php echo htmlspecialchars($fav['autor_original']); ?></small>
                                    <?php endif; ?>
                                    <a href="<?php echo htmlspecialchars($fav['link']); ?>" class="card-capitulo-link">Ver Novela</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>Aún no has guardado ninguna novela en tus me gusta.</p>
                        <a href="index.php" class="card-capitulo-link" style="color: #ff6b35;">Explorar catálogo principal →</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- PESTAÑA 2: CONFIGURACIÓN DE PERFIL -->
            <div id="tab-configuracion" class="tab-content">
                <h3 style="color: #ff6b35; margin-bottom: 15px;">Editar Configuración de Perfil</h3>

                <form action="usuario.php" method="post" class="profile-form">
                    <input type="hidden" name="accion" value="actualizar_perfil">

                    <div class="form-group">
                        <label>Nombre de Usuario:</label>
                        <input type="text" name="nombre" class="finder-input" value="<?php echo htmlspecialchars($nombre_usuario); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>URL de Imagen de Perfil (Avatar):</label>
                        <input type="url" name="avatar_url" class="finder-input" placeholder="https://ejemplo.com/mi-avatar.jpg" value="<?php echo htmlspecialchars($avatar_usuario ?? ''); ?>">
                        <small style="color: #b0a8a0;">Introduce un enlace de imagen directa para cambiar tu foto de perfil.</small>
                    </div>

                    <div class="form-group">
                        <label>Correo Electrónico:</label>
                        <input type="email" class="finder-input disabled-input" value="<?php echo htmlspecialchars($datos_usuario['Correo'] ?? ''); ?>" disabled>
                        <small style="color: #b0a8a0;">El correo está enlazado a la cuenta y no se puede modificar.</small>
                    </div>

                    <button type="submit" class="btn-leer form-btn">Guardar Cambios</button>
                </form>
            </div>
        </aside>
    </div>

    <script src="Novela.js"></script>
</body>
</html>