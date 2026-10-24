<?php
session_start();
require_once __DIR__ . '/HHH/Conexion.php';

// 1. Verificar Autenticación obligatoria
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['usuario_nombre'] ?? 'Autor';
$error = "";

// 2. Consultar si el usuario tiene novelas creadas (Filtro nativo de Supabase REST)
$mis_novelas = [];
$resNovelas = supabase_request("novela?select=id,Titulo&usuario_id=eq." . $usuario_id, "GET");
if (is_array($resNovelas) && !isset($resNovelas['error'])) {
    $mis_novelas = $resNovelas;
}
$tiene_novelas = count($mis_novelas) > 0;

// 3. PROCESAR ACCIÓN DE DESCARGA DE RESPALDO (JSON)
if (isset($_POST['accion']) && $_POST['accion'] === 'descargar_respaldo') {
    $respaldoData = [];

    // Obtener todas las novelas con sus respectivos capítulos estructurados
    foreach ($mis_novelas as $novela) {
        $resCap = supabase_request("capitulos?select=Capitulo,Titulo,Contenido_markdown,fecha_Publicacion&novela_id=eq." . $novela['id'] . "&order=Capitulo.asc", "GET");
        $capitulos = (is_array($resCap) && !isset($resCap['error'])) ? $resCap : [];

        $respaldoData[] = [
            'novela' => $novela['Titulo'],
            'capitulos_totales' => count($capitulos),
            'contenido_capitulos' => $capitulos
        ];
    }

    $json_data = json_encode($respaldoData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $filename = "respaldo_novelas_" . strtolower(str_replace(' ', '_', $usuario_nombre)) . "_" . date('Y-m-d') . ".json";

    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $json_data;
    exit();
}

// 4. PROCESAR LA ELIMINACIÓN DEFINITIVA VÍA POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_eliminacion'])) {
    $confirmacion_password = $_POST['password'] ?? '';
    $seguridad_check = isset($_POST['seguridad_check']);
    $destino_novelas = $_POST['destino_novelas'] ?? '';

    // Validaciones lógicas iniciales
    if (!$seguridad_check) {
        $error = "Debes marcar la casilla confirmando que estás completamente seguro.";
    } elseif ($tiene_novelas && empty($destino_novelas)) {
        $error = "Debes seleccionar qué deseas hacer con tus novelas publicadas.";
    } else {
        // Obtener las credenciales actuales del usuario en Supabase
        $endpoint = 'usuario?select=contrasena,auth_provider&id=eq.' . $usuario_id;
        $resUser = supabase_request($endpoint, 'GET');

        if (is_array($resUser) && !isset($resUser['error']) && count($resUser) > 0) {
            $usuario = $resUser[0];
            $es_google = ($usuario['auth_provider'] === 'google' || !empty($_SESSION['google_id']));
            $puede_eliminar = false;

            if ($es_google) {
                $puede_eliminar = true; 
            } else {
                if (password_verify($confirmacion_password, $usuario['contrasena'])) {
                    $puede_eliminar = true;
                } else {
                    $error = "La contraseña ingresada es incorrecta.";
                }
            }

            if ($puede_eliminar) {
                // Si el usuario decidió borrar todo, las relaciones ON DELETE CASCADE de Supabase
                // limpiarán automáticamente las novelas, capítulos, favoritos y comentarios.
                $resDelete = supabase_request('usuario?id=eq.' . $usuario_id, 'DELETE');

                if (!isset($resDelete['error'])) {
                    // Limpieza total y segura de sesiones
                    $_SESSION = array();
                    if (ini_get("session.use_cookies")) {
                        $params = session_get_cookie_params();
                        setcookie(session_name(), '', time() - 42000,
                            $params["path"], $params["domain"],
                            $params["secure"], $params["httponly"]
                        );
                    }
                    session_destroy();

                    header("Location: index.php?mensaje=cuenta_eliminada");
                    exit();
                } else {
                    $error = "Error al intentar eliminar tu cuenta en el servidor de base de datos.";
                }
            }
        } else {
            $error = "No se pudo verificar la información de credenciales del usuario.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Cuenta - FoxNovel</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
</head>
<body>
    <?php include 'Header.php'; ?>

    <div class="container form-container" style="max-width: 550px; margin: 40px auto;">
        <aside class="filter-sidebar form-sidebar" style="width: 100%; border-color: #d9534f; padding: 25px;">
            <h2 class="form-title" style="color: #d9534f; margin-bottom: 15px;">⚠️ Proceso de Baja de Cuenta</h2>
            
            <?php if (!empty($error)): ?>
                <div style="background-color: #451a1a; color: #f87171; border: 1px solid #ef4444; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-weight: bold; font-size: 0.9em;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- PASO A: DETECCIÓN DE CONTENIDO Y RESPALDO -->
            <?php if ($tiene_novelas): ?>
                <div style="background: #26201b; border: 1px solid #3b3129; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <h3 style="color: #ff6b35; font-size: 1.05em; margin-top: 0; margin-bottom: 8px;">📊 Hemos detectado que eres Autor</h3>
                    <p style="color: #b0a8a0; font-size: 0.88em; line-height: 1.4; margin-bottom: 12px;">
                        Tienes <strong><?php echo count($mis_novelas); ?> novela(s)</strong> registradas en nuestro catálogo técnico. Te recomendamos descargar una copia de seguridad en limpio con todos tus capítulos redactados en Markdown antes de continuar.
                    </p>
                    <form action="Eliminar.php" method="post">
                        <input type="hidden" name="accion" value="descargar_respaldo">
                        <button type="submit" class="btn-leer" style="background-color: #2e7d32; font-size: 0.85em; padding: 8px 14px; margin-top: 0; cursor: pointer; width: 100%; text-align: center;">
                            📥 Descargar Respaldo Completo (.JSON)
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- FORMULARIO PRINCIPAL DE ELIMINACIÓN -->
            <form action="Eliminar.php" method="post" onsubmit="return verificarSeguridad();">
                <input type="hidden" name="confirmar_eliminacion" value="1">

                <!-- Si es creador, exigir decisión sobre las obras -->
                <?php if ($tiene_novelas): ?>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <h3 style="font-size: 0.95em; margin-bottom: 8px;">¿Qué deseas hacer con tus historias publicadas? *</h3>
                        <select name="destino_novelas" class="finder-select" required style="border-color: #3b3129;">
                            <option value="">-- Selecciona una opción --</option>
                            <option value="eliminar">Eliminar definitivamente todas mis novelas del catálogo técnico</option>
                            <option value="ceder">Ceder el código de autorización al catálogo global (Mantener lecturas activas)</option>
                        </select>
                    </div>
                <?php endif; ?>

                <!-- Doble Verificación Checkbox -->
                <div class="form-group" style="display: flex; gap: 10px; align-items: flex-start; background: rgba(0,0,0,0.15); padding: 12px; border-radius: 6px; border: 1px solid #26201b; margin-bottom: 20px;">
                    <input type="checkbox" name="seguridad_check" id="seguridad_check" required style="width: 20px; height: 20px; accent-color: #d9534f; margin-top: 2px; cursor: pointer;">
                    <label for="seguridad_check" style="color: #d1d1d6; font-size: 0.88em; line-height: 1.4; cursor: pointer; user-select: none;">
                        Confirmo que entiendo que esta acción destruirá de forma permanente mi perfil, favoritos, comentarios e historial de progreso técnico.
                    </label>
                </div>

                <!-- Campo de Contraseña -->
                <?php if (!isset($_SESSION['google_id'])): ?>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <h3 style="font-size: 0.95em; margin-bottom: 6px;">Contraseña actual de FoxNovel:</h3>
                        <input type="password" name="password" class="finder-input" placeholder="••••••••" required>
                    </div>
                <?php endif; ?>

                <!-- Acciones del Formulario -->
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <a href="usuario.php" class="btn-nav" style="flex: 1; text-align: center; text-decoration: none; background: #2b2b36; border: 1px solid #3b3129; padding: 10px 0;">Cancelar</a>
                    <input type="submit" value="Borrar Permanentemente" class="btn-leer form-btn" style="flex: 1; background-color: #d9534f; cursor: pointer; margin: 0; padding: 10px 0;">
                </div>
            </form>
        </aside>
    </div>

    <script>
        function verificarSeguridad() {
            const check = document.getElementById('seguridad_check');
            if (!check.checked) {
