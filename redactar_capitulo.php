<?php
session_start();
require_once __DIR__ . '/HHH/Conexion.php';

// 1. Verificación de Autenticación
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// 2. Obtención y normalización del rol (Supabase -> Sesión -> Default)
$rol_usuario = '';
$resUsuario = supabase_request('usuario?select=rol&id=eq.' . $usuario_id, 'GET');

if (!isset($resUsuario['error']) && is_array($resUsuario) && !empty($resUsuario)) {
    $rol_usuario = trim(strtolower($resUsuario[0]['rol'] ?? ''));
}

if (empty($rol_usuario)) {
    $rol_sesion = $_SESSION['usuario_rol'] ?? $_SESSION['rol'] ?? 'lector';
    $rol_usuario = trim(strtolower($rol_sesion));
}

// 3. Validar permisos: Solo permitir si es 'creador' o 'admin'
if ($rol_usuario !== 'creador' && $rol_usuario !== 'admin') {
    header('Location: index.php');
    exit();
}

$mensaje = "";
$error = "";

// 4. Procesamiento del Formulario (Guardar Capítulo)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'subir_capitulo') {
    $novela_id       = intval($_POST['novela_id'] ?? 0);
    $titulo_capitulo = trim($_POST['titulo_capitulo'] ?? '');
    $markdown        = $_POST['contenido_markdown'] ?? '';

    if ($novela_id <= 0 || empty($titulo_capitulo) || empty($markdown)) {
        $error = "Por favor completa todos los campos requeridos.";
    } else {
        $permite_publicar = false;

        if ($rol_usuario === 'admin') {
            $permite_publicar = true;
        } else {
            $checkNovela = supabase_request('novela?select=id&id=eq.' . $novela_id . '&usuario_id=eq.' . $usuario_id, 'GET');
            if (!isset($checkNovela['error']) && !empty($checkNovela)) {
                $permite_publicar = true;
            }
        }

        if ($permite_publicar) {
            // Obtener el número consecutivo del último capítulo
            $resUltimo = supabase_request('capitulos?select=Capitulo&novela_id=eq.' . $novela_id . '&order=Capitulo.desc&limit=1', 'GET');
            $num_capitulo = (!empty($resUltimo) && isset($resUltimo[0]['Capitulo'])) ? intval($resUltimo[0]['Capitulo']) + 1 : 1;

            $dataCap = [
                'novela_id' => $novela_id,
                'Capitulo' => $num_capitulo,
                'Titulo' => $titulo_capitulo,
                'Contenido_markdown' => $markdown
            ];

            $res = supabase_request('capitulos', 'POST', $dataCap);

            if (!isset($res['error'])) {
                $mensaje = "¡Capítulo " . $num_capitulo . " (\"" . htmlspecialchars($titulo_capitulo) . "\") publicado con éxito! Puedes redactar el siguiente inmediatamente.";
            } else {
                $error = "Ocurrió un error al guardar el capítulo en la base de datos.";
            }
        } else {
            $error = "No tienes permiso para agregar capítulos a esta novela.";
        }
    }
}

// 5. Consultar las novelas pertenecientes al usuario (o todas si es admin)
if ($rol_usuario === 'admin') {
    $resMisNovelas = supabase_request('novela?select=id,Titulo&order=Titulo.asc', 'GET');
} else {
    $resMisNovelas = supabase_request('novela?select=id,Titulo&usuario_id=eq.' . $usuario_id . '&order=Titulo.asc', 'GET');
}

$mis_novelas = (!isset($resMisNovelas['error']) && is_array($resMisNovelas)) ? $resMisNovelas : [];

// Mantener seleccionada la última novela utilizada para agilizar la subida múltiple
$novela_seleccionada = $_POST['novela_id'] ?? ($_GET['novela_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovelaFox -- Redactar Capítulo</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        .editor-container {
            max-width: 1100px;
            margin: 30px auto;
            background-color: #1a1613;
            border: 1px solid #3b3129;
            border-radius: 8px;
            padding: 25px;
            color: #f5ebe6;
        }

        .editor-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        @media (max-width: 850px) {
            .editor-grid {
                grid-template-columns: 1fr;
            }
        }

        .preview-box {
            background-color: #121212;
            border: 1px solid #3b3129;
            border-radius: 6px;
            padding: 15px;
            min-height: 250px;
            max-height: 400px;
            overflow-y: auto;
            color: #e0e0e0;
            line-height: 1.6;
        }

        .guide-box {
            background-color: #26201b;
            border: 1px solid #3b3129;
            border-radius: 6px;
            padding: 15px;
            font-size: 0.88em;
        }

        .guide-box h4 {
            color: #ff6b35;
            margin-top: 0;
            margin-bottom: 10px;
            border-bottom: 1px solid #3b3129;
            padding-bottom: 5px;
        }

        .guide-box code {
            background-color: #121212;
            color: #ff8552;
            padding: 2px 5px;
            border-radius: 3px;
        }

        .alert-success {
            background-color: #14532d;
            color: #4ade80;
            border: 1px solid #22c55e;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .alert-danger {
            background-color: #451a1a;
            color: #f87171;
            border: 1px solid #ef4444;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <?php include 'Header.php'; ?>

    <div class="editor-container">
        <h2 style="color:#ff6b35; margin-top:0;">Publicar Capítulos de Tu Novela</h2>
        <p style="color:#aaa;">Puedes redactar y publicar múltiples capítulos uno tras otro seleccionando la novela correspondiente.</p>

        <?php if ($mensaje): ?>
            <div class="alert-success"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (empty($mis_novelas)): ?>
            <div class="alert-danger">
                No tienes novelas registradas o aprobadas para agregar capítulos. 
                <a href="publicar_novela.php" style="color:#ff6b35; font-weight:bold;">Crea o solicita una novela aquí.</a>
            </div>
        <?php else: ?>

            <form action="redactar_capitulo.php" method="post">
                <input type="hidden" name="accion" value="subir_capitulo">

                <div class="form-group">
                    <h3>1. Selecciona la Novela:</h3>
                    <select name="novela_id" class="finder-input" required>
                        <option value="">-- Seleccionar Novela --</option>
                        <?php foreach ($mis_novelas as $nov): ?>
                            <option value="<?php echo $nov['id']; ?>" <?php echo ($novela_seleccionada == $nov['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($nov['Titulo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <h3>2. Título del Capítulo:</h3>
                    <input type="text" name="titulo_capitulo" class="finder-input" placeholder="Ej: Capítulo 1: El Despertar" required>
                </div>

                <div class="editor-grid">
                    <!-- Columna Izquierda: Editor Markdown -->
                    <div>
                        <div class="form-group">
                            <h3>3. Contenido del Capítulo (Markdown):</h3>
                            <textarea name="contenido_markdown" id="md-editor" class="finder-input" style="min-height: 350px; resize: vertical;" placeholder="Escribe el contenido aquí..." required></textarea>
                        </div>
                    </div>

                    <!-- Columna Derecha: Guía de Markdown e Instrucciones -->
                    <div>
                        <div class="guide-box">
                            <h4>Guía Rápida de Markdown</h4>
                            <p>Usa los siguientes formatos para dar estilo a tu texto:</p>
                            <ul style="padding-left: 18px; line-height: 1.8;">
                                <li><code>**Texto en Negrita**</code> &rarr; <strong>Texto en Negrita</strong></li>
                                <li><code>*Texto en Cursiva*</code> &rarr; <em>Texto en Cursiva</em></li>
                                <li><code># Título Principal</code></li>
                                <li><code>## Subtítulo</code></li>
                                <li><code>> Texto en Cita</code> &rarr; Cita destacada</li>
                                <li><code>---</code> &rarr; Línea separadora</li>
                                <li><code>[^1]: Nota al pie</code> &rarr; Para notas explicativas</li>
                            </ul>
                        </div>

                        <div style="margin-top: 15px;">
                            <h3>Vista Previa:</h3>
                            <div id="md-preview" class="preview-box"></div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <input type="submit" value="Publicar Capítulo y Continuar" class="btn-leer" style="cursor:pointer; width:100%; text-align:center; font-size:1.1em;">
                </div>
            </form>

        <?php endif; ?>
    </div>

    <!-- Script de procesamiento para la vista previa -->
    <script src="novela.js"></script>
</body>

</html>