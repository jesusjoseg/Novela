<?php
session_start();
require_once __DIR__ . '/HHH/Conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$mensaje    = "";
$error      = "";

// Consultar estado actual del usuario
$rol_actual = 'lector';
$resUser = supabase_request('usuario?select=id,nombre,email,rol&id=eq.' . $usuario_id, 'GET');
if (!isset($resUser['error']) && is_array($resUser) && count($resUser) > 0) {
    $rol_actual = $resUser[0]['rol'] ?? 'lector';
}

// Si ya es creador o admin, redirigir directamente al panel de creador
if (in_array(strtolower($rol_actual), ['creador', 'admin'])) {
    header('Location: creador.php');
    exit();
}

// Procesar solicitud de cambio de rol a 'pendiente_creador'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'solicitar_rol') {
    $res = supabase_request('usuario?id=eq.' . $usuario_id, 'PATCH', [
        'rol' => 'pendiente_creador'
    ]);

    if (!isset($res['error'])) {
        $rol_actual = 'pendiente_creador';
        $mensaje = "¡Tu solicitud ha sido enviada con éxito! Un administrador revisará tu perfil en breve.";
    } else {
        $error = "Ocurrió un error al procesar tu solicitud. Por favor intenta de nuevo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Cuenta de Creador - Foxnovel</title>
    <link rel="stylesheet" href="Style.css">
    <style>
        .card-info {
            background-color: #1a1613;
            border: 1px solid #26201b;
            border-radius: 12px;
            padding: 30px;
            max-width: 600px;
            margin: 40px auto;
            text-align: center;
        }
        .warning-title { color: #ff6b35; font-size: 1.5em; font-weight: bold; margin-bottom: 15px; }
        .warning-text { color: #d1d1d6; font-size: 1em; line-height: 1.6; margin-bottom: 25px; }
        .btn-solicitar {
            background-color: #ff6b35;
            color: #ffffff;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .btn-solicitar:hover { background-color: #e05a2b; }
        .badge-status {
            display: inline-block;
            background: #d9534f;
            color: #fff;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .badge-pending { background: #f0ad4e; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="card-info">
            <div class="warning-title">⚠️ Acceso Restringido</div>

            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-success" style="color: #4ade80; margin-bottom: 15px; font-weight: bold;">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" style="color: #ef4444; margin-bottom: 15px; font-weight: bold;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($rol_actual === 'pendiente_creador'): ?>
                <span class="badge-status badge-pending">SOLICITUD EN REVISIÓN</span>
                <p class="warning-text">
                    Tu cuenta de creador está en proceso de revisión por parte de un administrador. Recibirás acceso completo al panel de publicación tan pronto como sea aprobada.
                </p>
                <a href="index.php" class="btn-solicitar" style="text-decoration: none; display: inline-block;">Volver al Inicio</a>
            <?php else: ?>
                <p class="warning-text">
                    Para poder publicar tus propias historias y novelas originales en la plataforma, primero debes solicitar la verificación como creador.
                </p>
                <form action="solicitar_creador.php" method="post">
                    <input type="hidden" name="accion" value="solicitar_rol">
                    <button type="submit" class="btn-solicitar">🚀 Solicitar Verificación de Creador</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>