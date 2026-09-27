<?php
session_start();
require_once __DIR__ . '/HHH/Conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id     = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['usuario_nombre'] ?? $_SESSION['nombre'] ?? 'Autor';
$mensaje        = "";
$error          = "";

// Dominio Base de tu Proyecto Supabase (Sin la ruta /rest/v1)
define('SUPABASE_BASE_URL', 'https://ahprflxvnrovrwxaojrw.supabase.co'); 
define('SUPABASE_BUCKET', 'portadas');

// 1. Verificación de permisos de usuario desde Supabase
$rol_actual = 'lector';
$resUser = supabase_request('usuario?select=id,nombre,email,rol&id=eq.' . $usuario_id, 'GET');
if (!isset($resUser['error']) && is_array($resUser) && count($resUser) > 0) {
    $rol_actual = $resUser[0]['rol'] ?? 'lector';
    if (!empty($resUser[0]['nombre'])) {
        $usuario_nombre = $resUser[0]['nombre'];
    }
}

// Redirigir si no tiene permisos
if (!in_array(strtolower($rol_actual), ['creador', 'admin'])) {
    header('Location: solicitar_creador.php');
    exit();
}

// Listas de Géneros y Plataformas
$lista_generos = [
    'Accion', 'Aventura', 'Artes Marciales', 'Supervivencia', 'Militar',
    'Fantasia', 'Alta Fantasia', 'Isekai', 'Reencarnacion', 'Transmigracion',
    'Xianxia', 'Xuanhuan', 'Wuxia', 'Fantasía Urbana', 'Magia',
    'Sistema', 'LitRPG', 'Videojuegos', 'Ciencia Ficcion', 'Cyberpunk',
    'Mecha', 'Apocalptico', 'GenderBender', 'Yuri', 'Shoujo Ai',
    'Yaoi', 'Shounen Ai', 'Romance', 'Comedia Romantica', 'Harem',
    'Harem Inverso', 'Drama', 'Tragedia', 'Misterio', 'Psicologico',
    'Horror', 'Sobrenatural', 'Slice of Life', 'Comedia', 'Vida Escolar',
    'Historico', 'Realeza', 'Deportes', 'Mature', 'Ecchi'
];

$plataformas_origen = [
    'Obra Inédita',
    'Wattpad',
    'Webnovel',
    'Royal Road',
    'Scribble Hub',
    'Otra Plataforma'
];

/**
 * Procesa la imagen, la convierte a WebP temporalmente y la sube al Bucket de Supabase Storage.
 */
function procesarYSubirASupabase($file) {
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
        return ['error' => 'Solo se permiten imágenes en formato PNG, JPG o WEBP.'];
    }

    $nombreArchivo = 'portada_' . $GLOBALS['usuario_id'] . '_' . time() . '.webp';
    
    // Se usa /tmp explícitamente o la carpeta local si /tmp no está disponible, para evitar open_basedir restriction
    $tempDir = (is_dir('/tmp') && is_writable('/tmp')) ? '/tmp' : __DIR__;
    $tempWebpPath = $tempDir . '/' . $nombreArchivo;

    // Convertir o mover la imagen al directorio temporal del servidor
    if ($ext === 'webp') {
        move_uploaded_file($file['tmp_name'], $tempWebpPath);
    } else {
        if ($ext === 'png') {
            $img = imagecreatefrompng($file['tmp_name']);
            if ($img) {
                imagepalettetotruecolor($img);
                imagealphablending($img, true);
                imagesavealpha($img, true);
            }
        } else {
            $img = imagecreatefromjpeg($file['tmp_name']);
        }

        if (!$img) {
            return ['error' => 'Error al procesar la imagen seleccionada.'];
        }

        imagewebp($img, $tempWebpPath, 80);
        imagedestroy($img);
    }

    // Validar que el archivo temporal existe antes de leerlo
    if (!file_exists($tempWebpPath)) {
        return ['error' => 'No se pudo generar el archivo temporal de la imagen.'];
    }

    // Subir el archivo WebP a Supabase Storage vía cURL
    $fileData = file_get_contents($tempWebpPath);
    @unlink($tempWebpPath); // Eliminar el archivo temporal

    if ($fileData === false) {
        return ['error' => 'No se pudo leer la imagen convertida.'];
    }

    // Endpoint exacto para el Storage API de Supabase
    $storageUrl = SUPABASE_BASE_URL . '/storage/v1/object/' . SUPABASE_BUCKET . '/' . $nombreArchivo;

    $ch = curl_init($storageUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fileData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apikey: ' . SUPABASE_KEY,
        'Authorization: Bearer ' . SUPABASE_KEY,
        'Content-Type: image/webp',
        'x-upsert: true'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 || $httpCode === 201) {
        // URL pública directa para guardar en la BD
        $publicUrl = SUPABASE_BASE_URL . '/storage/v1/object/public/' . SUPABASE_BUCKET . '/' . $nombreArchivo;
        return ['ruta' => $publicUrl];
    }

    return ['error' => 'Error al subir la imagen a Supabase Storage. Código HTTP: ' . $httpCode];
}

// 2. Procesar el formulario POST de creación de novela
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear_novela') {
    $titulo            = trim($_POST['titulo'] ?? '');
    $descripcion       = trim($_POST['descripcion'] ?? '');
    $autor_original    = trim($_POST['autor_original'] ?? '');
    $plataforma_origen = trim($_POST['plataforma_origen'] ?? 'Obra Inédita');
    $link_origen       = trim($_POST['link_origen'] ?? '');
    $acepta_terminos   = isset($_POST['acepta_terminos']);

    $generos_seleccionados = isset($_POST['Genero']) && is_array($_POST['Genero']) ? $_POST['Genero'] : [];

    if (empty($titulo) || empty($descripcion)) {
        $error = "Por favor completa el título y la descripción.";
    } elseif (empty($generos_seleccionados)) {
        $error = "Debes seleccionar al menos un género.";
    } elseif ($plataforma_origen !== 'Obra Inédita' && empty($link_origen)) {
        $error = "Ingresa el enlace directo a tu historia en " . htmlspecialchars($plataforma_origen) . ".";
    } elseif (!$acepta_terminos) {
        $error = "Debes confirmar que tu novela no viola derechos de autor ni proviene de una versión web protegida.";
    } elseif (!isset($_FILES['portada']) || $_FILES['portada']['error'] !== UPLOAD_ERR_OK) {
        $error = "Debes seleccionar una imagen de portada para tu novela.";
    } else {
        // Subida directa a Supabase Storage
        $resImagen = procesarYSubirASupabase($_FILES['portada']);

        if (isset($resImagen['error'])) {
            $error = $resImagen['error'];
        } else {
            $generoCadena = implode(', ', $generos_seleccionados);
            $autorFinal   = !empty($autor_original) ? $autor_original : $usuario_nombre;

            $dataNovela = [
                'Titulo'            => $titulo,
                'Descripcion'       => $descripcion,
                'Genero'            => $generoCadena,
                'Portada'           => $resImagen['ruta'], // URL completa guardada
                'plataforma_origen' => $plataforma_origen,
                'link_origen'       => !empty($link_origen) ? $link_origen : null,
                'Estado'            => 'En emisión',
                'Visitas'           => 0,
                'autor_original'    => $autorFinal,
                'traductor_ingles'  => null,
                'usuario_id'        => $usuario_id,
                'es_oficial'        => false,
                'estado_revision'   => 'pendiente'
            ];

            // Inserción en Supabase
            $resInsert = supabase_request('novela', 'POST', $dataNovela);

            if (!isset($resInsert['error']) && is_array($resInsert) && count($resInsert) > 0) {
                $idNuevaNovela = $resInsert[0]['id'] ?? null;

                if ($idNuevaNovela) {
                    $linkInterno = "ver_novela.php?id=" . $idNuevaNovela;
                    supabase_request('novela?id=eq.' . $idNuevaNovela, 'PATCH', ['link' => $linkInterno]);
                }

                $mensaje = "¡Tu historia ha sido enviada para revisión por el administrador!";
            } else {
                $error = "No se pudo registrar la novela. Intenta nuevamente.";
            }
        }
    }
}

// 3. Consultar novelas creadas por el usuario actual
$mis_novelas = [];
$resNovelas = supabase_request('novela?select=id,Titulo,Portada,link,estado_revision&usuario_id=eq.' . $usuario_id . '&order=id.desc', 'GET');
if (!isset($resNovelas['error']) && is_array($resNovelas)) {
    $mis_novelas = $resNovelas;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Creador - Foxnovel</title>
    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
    <link rel="stylesheet" href="Style.css">
    <style>
        .creador-container { max-width: 900px; margin: 30px auto; padding: 0 15px; }
        .page-title { color: #ff6b35; font-size: 1.8rem; font-weight: bold; text-align: center; margin-bottom: 4px; }
        .page-subtitle { color: #8e8e93; font-size: 0.95rem; text-align: center; margin-bottom: 25px; }
        
        .form-section { background: #1a1613; border: 1px solid #26201b; border-radius: 12px; padding: 25px; margin-bottom: 30px; }
        .form-label { color: #ffffff; font-size: 0.95rem; font-weight: 600; margin-bottom: 8px; display: block; margin-top: 15px; }
        .form-input {
            width: 100%;
            background-color: #14110f;
            color: #ffffff;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #26201b;
            font-size: 0.95rem;
            box-sizing: border-box;
        }
        .form-input:focus { border-color: #ff6b35; outline: none; }
        textarea.form-input { resize: vertical; min-height: 100px; }

        /* Chips de Géneros */
        .generos-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            background: #14110f;
            padding: 14px;
            border-radius: 8px;
            border: 1px solid #26201b;
            max-height: 200px;
            overflow-y: auto;
        }
        .chip-checkbox { display: none; }
        .chip-label {
            background: #201a16;
            border: 1px solid #332b24;
            color: #b0a8a0;
            padding: 6px 14px;
            border-radius: 16px;
            font-size: 0.85rem;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s;
        }
        .chip-checkbox:checked + .chip-label {
            background: #ff6b35;
            border-color: #ff6b35;
            color: #ffffff;
            font-weight: bold;
        }

        /* Chips de Plataforma */
        .plataformas-row { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 5px; }
        .plat-radio { display: none; }
        .plat-label {
            background: #14110f;
            border: 1px solid #26201b;
            color: #8e8e93;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .plat-radio:checked + .plat-label {
            background: #ff6b35;
            border-color: #ff6b35;
            color: #ffffff;
            font-weight: bold;
        }

        /* Caja de Términos */
        .terms-box {
            background: #14110f;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #26201b;
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .terms-text { color: #d1d1d6; font-size: 0.85rem; line-height: 1.4; }

        .btn-submit {
            width: 100%;
            background-color: #ff6b35;
            color: #ffffff;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.2s;
        }
        .btn-submit:hover { background-color: #e05a2b; }

        /* Vista previa portada */
        .portada-preview-box { text-align: center; margin-bottom: 10px; }
        #img-preview { width: 120px; height: 180px; object-fit: cover; border-radius: 8px; display: none; margin: 0 auto 10px; border: 1px solid #26201b; }
        
        .btn-link-secundario {
            background: #201a16;
            color: #ff6b35;
            border: 1px solid #ff6b35;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-block;
            margin-right: 5px;
        }
        .btn-link-secundario:hover { background: #ff6b35; color: #fff; }
    </style>
</head>
<body>
    <?php include 'Header.php'; ?>

    <div class="creador-container">
        <h1 class="page-title">Panel de Creador</h1>
        <p class="page-subtitle">Publica tu historia original para revisión</p>

        <?php if ($mensaje): ?>
            <div style="background: #1f3a2b; color: #4ade80; border: 1px solid #2e7d32; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div style="background: #3a1f1f; color: #ef4444; border: 1px solid #d9534f; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- FORMULARIO DE PUBLICACIÓN -->
        <div class="form-section">
            <form action="creador.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="crear_novela">

                <!-- Portada -->
                <label class="form-label">Portada de la Novela *</label>
                <div class="portada-preview-box">
                    <img id="img-preview" alt="Vista previa de portada">
                    <input type="file" name="portada" id="portada_input" accept="image/png, image/jpeg, image/webp" class="form-input" required onchange="previewImagen(event)">
                </div>

                <!-- Título -->
                <label class="form-label">Título de la Novela *</label>
                <input type="text" name="titulo" class="form-input" placeholder="Ej. El Despertar del Dragón" required>

                <!-- Géneros -->
                <label class="form-label">Géneros *</label>
                <div class="generos-grid">
                    <?php foreach ($lista_generos as $g): ?>
                        <div>
                            <input type="checkbox" name="Genero[]" value="<?php echo htmlspecialchars($g); ?>" id="gen_<?php echo htmlspecialchars($g); ?>" class="chip-checkbox">
                            <label for="gen_<?php echo htmlspecialchars($g); ?>" class="chip-label">+ <?php echo htmlspecialchars($g); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Sinopsis -->
                <label class="form-label">Sinopsis / Descripción *</label>
                <textarea name="descripcion" class="form-input" placeholder="Escribe un resumen atractivo de tu historia..." required></textarea>

                <!-- Autor Original -->
                <label class="form-label">Autor Original (Opcional)</label>
                <input type="text" name="autor_original" class="form-input" placeholder="<?php echo htmlspecialchars($usuario_nombre); ?>">

                <!-- Plataforma de Origen -->
                <label class="form-label">¿Publicaste esta novela en otra plataforma?</label>
                <div class="plataformas-row">
                    <?php foreach ($plataformas_origen as $idx => $plat): ?>
                        <div>
                            <input type="radio" name="plataforma_origen" value="<?php echo htmlspecialchars($plat); ?>" id="plat_<?php echo $idx; ?>" class="plat-radio" <?php echo $idx === 0 ? 'checked' : ''; ?> onchange="toggleLinkInput(this.value)">
                            <label for="plat_<?php echo $idx; ?>" class="plat-label"><?php echo htmlspecialchars($plat); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Campo dinámico de URL -->
                <div id="box-link-origen" style="display: none;">
                    <label class="form-label" id="lbl-link">Enlace a tu historia *</label>
                    <input type="url" name="link_origen" id="input_link_origen" class="form-input" placeholder="https://...">
                </div>

                <!-- Declaración de Términos -->
                <div class="terms-box">
                    <input type="checkbox" name="acepta_terminos" id="acepta_terminos" value="1" required style="width: 18px; height: 18px; accent-color: #ff6b35;">
                    <label for="acepta_terminos" class="terms-text">
                        Declaro que soy el autor original de esta novela y que <strong style="color: #ff6b35;">tengo los derechos</strong> para publicarla. Entiendo que un administrador la revisará.
                    </label>
                </div>

                <button type="submit" class="btn-submit">🚀 Enviar Novela a Revisión</button>
            </form>
        </div>

        <!-- MIS OBRAS REGISTRADAS -->
        <div class="form-section">
            <h3 style="color: #f5ebe6; margin-top: 0; border-bottom: 1px solid #26201b; padding-bottom: 10px;">Mis Obras Registradas</h3>

            <?php if (!empty($mis_novelas)): ?>
                <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 15px;">
                    <?php foreach ($mis_novelas as $nov): ?>
                        <?php 
                            $enlaceVer = !empty($nov['link']) ? $nov['link'] : 'ver_novela.php?id=' . $nov['id'];
                            $estadoRev = strtolower($nov['estado_revision'] ?? 'pendiente');
                        ?>
                        <div style="display: flex; gap: 15px; background: #14110f; padding: 15px; border-radius: 8px; align-items: center; border: 1px solid #26201b;">
                            <img src="<?php echo htmlspecialchars($nov['Portada']); ?>" style="width: 60px; height: 85px; object-fit: cover; border-radius: 6px;">
                            <div style="flex-grow: 1;">
                                <h4 style="margin: 0; color: #f5ebe6; font-size: 1rem;"><?php echo htmlspecialchars($nov['Titulo']); ?></h4>
                                <div style="margin-top: 6px;">
                                    <span style="display: inline-block; padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; background: <?php echo $estadoRev === 'aprobado' ? '#2e7d32' : ($estadoRev === 'rechazado' ? '#d9534f' : '#b8860b'); ?>; color: #fff;">
                                        Estado: <?php echo strtoupper($estadoRev); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <a href="<?php echo htmlspecialchars($enlaceVer); ?>" class="btn-link-secundario" target="_blank">👁️ Ver Novela</a>

                                <?php if ($estadoRev === 'aprobado'): ?>
                                    <a href="redactar_capitulo.php?novela_id=<?php echo $nov['id']; ?>" class="btn-submit" style="text-decoration: none; padding: 8px 14px; font-size: 0.85rem; display: inline-block; margin: 0;">➕ Redactar Capítulo</a>
                                <?php else: ?>
                                    <button class="btn-submit" disabled style="opacity: 0.5; cursor: not-allowed; padding: 8px 14px; font-size: 0.85rem; margin: 0;">🔒 En Revisión</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: #8e8e93; margin-top: 15px;">Aún no has registrado ninguna novela original.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function previewImagen(event) {
            const input = event.target;
            const img = document.getElementById('img-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    img.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function toggleLinkInput(valor) {
            const box = document.getElementById('box-link-origen');
            const lbl = document.getElementById('lbl-link');
            const input = document.getElementById('input_link_origen');

            if (valor !== 'Obra Inédita') {
                box.style.display = 'block';
                lbl.textContent = 'Enlace a tu historia en ' + valor + ' *';
                input.placeholder = 'https://www.' + valor.toLowerCase().replace(/\s+/g, '') + '.com/...';
                input.required = true;
            } else {
                box.style.display = 'none';
                input.required = false;
                input.value = '';
            }
        }
    </script>
</body>
</html>