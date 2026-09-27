<?php
session_start();
// Carga la conexión API REST de Supabase
require_once __DIR__ . '/HHH/Conexion.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$mensaje_perfil = "";
$error_perfil = "";

// 1. Procesar actualización de perfil (nombre y avatar vía PATCH)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar_perfil') {
    $nuevo_nombre = trim($_POST['nombre'] ?? '');
    $avatar_url = trim($_POST['avatar'] ?? '');

    if (!empty($nuevo_nombre)) {
        // Mapeo exacto según tu tabla 'usuario' (todo en minúsculas)
        $dataUpdate = [
            'nombre' => $nuevo_nombre,
            'avatar' => $avatar_url
        ];

        // PATCH a la API REST de Supabase filtrando por ID
        $resUpdate = supabase_request('usuario?id=eq.' . $usuario_id, 'PATCH', $dataUpdate);

        if (isset($resUpdate['error'])) {
            $error_perfil = "Error al actualizar los datos en Supabase.";
        } else {
            $_SESSION['usuario_nombre'] = $nuevo_nombre;
            $mensaje_perfil = "¡Perfil actualizado correctamente!";
        }
    } else {
        $error_perfil = "El nombre no puede estar vacío.";
    }
}

// 2. Obtener la información del usuario con los campos exactos de tu tabla
$datos_usuario = null;
$resUser = supabase_request('usuario?select=id,nombre,email,rol,es_premium,fecha_registro,avatar&id=eq.' . $usuario_id, 'GET');

if (!isset($resUser['error']) && is_array($resUser) && count($resUser) > 0) {
    $datos_usuario = $resUser[0];
}

// 3. Obtener Novelas Favoritas
$favoritos = [];
$resFav = supabase_request('favoritos?select=id,novela(id,Titulo,Portada,link,autor_original)&usuario_id=eq.' . $usuario_id . '&order=id.desc', 'GET');

if (!isset($resFav['error']) && is_array($resFav)) {
    foreach ($resFav as $fav) {
        if (isset($fav['novela'])) {
            $favoritos[] = $fav['novela'];
        }
    }
}

// Mapeo de variables utilizando las claves en minúsculas recibidas de Supabase
$nombre_usuario = $datos_usuario['nombre'] ?? 'Usuario';
$email_usuario = $datos_usuario['email'] ?? '';
$avatar_usuario = !empty($datos_usuario['avatar']) ? $datos_usuario['avatar'] : 'https://via.placeholder.com/120/ff6b35/FFFFFF?text=User';
$rol = strtolower($datos_usuario['rol'] ?? 'lector');
$es_premium = !empty($datos_usuario['es_premium']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Foxnovel</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #14110f;
            color: #FFFFFF;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        .main-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* 1. CABECERA DEL PERFIL */
        .profile-header {
            width: 100%;
            background-color: #1a1613;
            border: 1px solid #26201b;
            border-radius: 12px;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 24px;
        }

        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50px;
            object-fit: cover;
            margin-bottom: 12px;
        }

        .user-name {
            font-size: 22px;
            font-weight: bold;
            color: #FFFFFF;
            margin: 0;
        }

        .user-email {
            font-size: 14px;
            color: #8e8e93;
            margin-top: 4px;
        }

        .badge-row {
            display: flex;
            flex-direction: row;
            gap: 8px;
            margin-top: 10px;
        }

        .role-badge {
            background-color: #201a16;
            border: 1px solid #332b24;
            padding: 6px 12px;
            border-radius: 20px;
        }

        .role-text {
            color: #ff6b35;
            font-weight: 600;
            font-size: 13px;
        }

        .premium-badge {
            background-color: #3d2e00;
            border: 1px solid #ffd700;
        }

        .premium-text {
            color: #ffd700;
        }

        .edit-button {
            margin-top: 16px;
            border: 1px solid #ff6b35;
            background: transparent;
            color: #ff6b35;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
        }

        /* 2. SECCIÓN DE CREADOR */
        .section-container {
            width: 100%;
            background-color: #1a1613;
            border: 1px solid #26201b;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-sizing: border-box;
        }

        .section-title {
            color: #FFFFFF;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .creator-btn-active {
            background-color: #ff6b35;
            padding: 16px;
            border-radius: 8px;
            text-align: center;
            color: #FFFFFF;
            font-weight: bold;
            font-size: 15px;
            text-decoration: none;
            display: block;
        }

        .creator-btn-request {
            background-color: #201a16;
            border: 1px solid #ff6b35;
            padding: 16px;
            border-radius: 8px;
            text-align: center;
            color: #FFFFFF;
            text-decoration: none;
            display: block;
        }

        .creator-subtext {
            color: #8e8e93;
            font-size: 12px;
            margin-top: 4px;
        }

        .pending-box {
            background-color: #322E1A;
            border: 1px solid #D4A373;
            padding: 14px;
            border-radius: 8px;
        }

        .pending-text {
            color: #E0A96D;
            font-size: 13px;
            line-height: 18px;
            margin: 0;
        }

        /* 3. BOTÓN DE CIERRE DE SESIÓN */
        .logout-button {
            width: 100%;
            background-color: #2A1515;
            border: 1px solid #E53E3E;
            padding: 14px;
            border-radius: 8px;
            text-align: center;
            color: #E53E3E;
            font-weight: bold;
            text-decoration: none;
            box-sizing: border-box;
            display: block;
        }

        /* MODAL DE EDICIÓN */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: #1a1613;
            border: 1px solid #26201b;
            padding: 20px;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
        }

        .modal-title {
            font-size: 18px;
            font-weight: bold;
            color: #FFF;
            margin-bottom: 16px;
        }

        .input-label {
            color: #FFF;
            margin-bottom: 6px;
            font-size: 13px;
            display: block;
        }

        .input-field {
            width: 100%;
            background-color: #201a16;
            color: #FFF;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 14px;
            border: 1px solid #332b24;
            box-sizing: border-box;
        }

        .modal-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .modal-btn {
            width: 48%;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        .cancel-btn { background-color: #332b24; color: #FFF; }
        .save-btn { background-color: #ff6b35; color: #FFF; }

        .alert {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .alert-success { background-color: #1e3a29; color: #4ade80; border: 1px solid #22c55e; }
        .alert-danger { background-color: #3a1e1e; color: #f87171; border: 1px solid #ef4444; }
    </style>
</head>
<body>
    <?php include 'Header.php' ?>
    <div class="main-container">
        
        <?php if ($mensaje_perfil): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje_perfil); ?></div>
        <?php endif; ?>
        <?php if ($error_perfil): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_perfil); ?></div>
        <?php endif; ?>

        <!-- 1. CABECERA DEL PERFIL -->
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($avatar_usuario); ?>" alt="Avatar" class="avatar">
            <h1 class="user-name"><?php echo htmlspecialchars($nombre_usuario); ?></h1>
            <span class="user-email"><?php echo htmlspecialchars($email_usuario); ?></span>

            <!-- Badges de Rol y Premium -->
            <div class="badge-row">
                <div class="role-badge">
                    <span class="role-text">
                        <?php 
                        if ($rol === 'creador') echo '✍️ Creador Verificado';
                        elseif ($rol === 'admin') echo '👑 Administrador';
                        elseif ($rol === 'pendiente_creador') echo '⏳ Verificación Pendiente';
                        else echo '📖 Lector';
                        ?>
                    </span>
                </div>
                <?php if ($es_premium): ?>
                    <div class="role-badge premium-badge">
                        <span class="role-text premium-text">⭐ Premium</span>
                    </div>
                <?php endif; ?>
            </div>

            <button class="edit-button" onclick="openModal()">Editar Perfil</button>
        </div>

        <!-- 2. SECCIÓN DE CREADOR -->
        <div class="section-container">
            <div class="section-title">Panel de Creador de Novelas</div>

            <?php if ($rol === 'creador' || $rol === 'admin'): ?>
                <a href="creador.php" class="creator-btn-active">🚀 Ir a Panel de Creador</a>
            <?php elseif ($rol === 'pendiente_creador'): ?>
                <div class="pending-box">
                    <p class="pending-text">
                        🕒 Tu cuenta está siendo revisada por un administrador en el Dashboard. Pronto podrás publicar historias originales.
                    </p>
                </div>
            <?php else: ?>
                <a href="solicitar_creador.php" class="creator-btn-request">
                    <div style="font-weight: bold; font-size: 15px;">✨ Convertirme en Creador</div>
                    <div class="creator-subtext">Solicita la verificación para publicar tus propias novelas</div>
                </a>
            <?php endif; ?>
        </div>

        <!-- 3. BOTÓN DE CIERRE DE SESIÓN -->
        <a href="logout.php" class="logout-button">Cerrar Sesión</a>
    </div>

    <!-- MODAL DE EDICIÓN DE PERFIL -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-title">Editar Perfil</div>
            
            <form action="usuario.php" method="POST">
                <input type="hidden" name="accion" value="actualizar_perfil">

                <label class="input-label">Nombre de Usuario:</label>
                <input type="text" name="nombre" class="input-field" value="<?php echo htmlspecialchars($nombre_usuario); ?>" required>

                <label class="input-label">URL de Avatar:</label>
                <input type="url" name="avatar" class="input-field" value="<?php echo htmlspecialchars($datos_usuario['avatar'] ?? ''); ?>" placeholder="https://ejemplo.com/avatar.jpg">

                <div class="modal-actions">
                    <button type="button" class="modal-btn cancel-btn" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="modal-btn save-btn">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('editModal').style.display = 'flex';
        }
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }
    </script>
</body>
</html>