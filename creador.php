<?php
session_start();
require_once 'HHH/Conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id     = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['usuario_nombre'] ?? 'Autor';
$mensaje        = "";
$error          = "";

// Función para procesar la imagen de portada y convertirla automáticamente a WebP
function procesarEInsertarWebp($file, $destinoDir = 'uploads/portadas/') {
    if (!file_exists($destinoDir)) {
        mkdir($destinoDir, 0777, true);
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['png', 'jpg', 'jpeg'])) {
        return ['error' => 'Solo se permiten imágenes en formato PNG o JPG.'];
    }

    // Cargar imagen según el formato original
    if ($ext === 'png') {
        $img = imagecreatefrompng($file['tmp_name']);
        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);
    } else {
        $img = imagecreatefromjpeg($file['tmp_name']);
    }

    if (!$img) {
        return ['error' => 'Error al procesar la imagen seleccionada.'];
    }

    // Nombre único para el archivo WebP
    $nombreArchivo = 'portada_' . uniqid() . '.webp';
    $rutaFinal     = $destinoDir . $nombreArchivo;

    // Convertir y guardar en formato WebP con calidad 80
    imagewebp($img, $rutaFinal, 80);
    imagedestroy($img);

    return ['ruta' => $rutaFinal];
}

// PROCESAR FORMULARIO DE NUEVA NOVELA ORIGINAL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear_novela') {
    $titulo   = trim($_POST['titulo'] ?? '');
    $sipnosis = trim($_POST['sipnosis'] ?? '');

    if (!empty($titulo) && isset($_FILES['portada']) && $_FILES['portada']['error'] === UPLOAD_ERR_OK) {
        $resultadoImagen = procesarEInsertarWebp($_FILES['portada']);

        if (isset($resultadoImagen['error'])) {
            $error = $resultadoImagen['error'];
        } else {
            try {
                // Se asigna automáticamente el usuario_nombre como autor original
                $sql = 'INSERT INTO novela ("Titulo", "Sipnosis", "Portada", "autor_original", "estado_revision", "usuario_id") 
                        VALUES (:titulo, :sipnosis, :portada, :autor, \'pendiente\', :usuario_id)';
                $stmt = $conexion->prepare($sql);
                $stmt->execute([
                    ':titulo'    => $titulo,
                    ':sipnosis'  => $sipnosis,
                    ':portada'   => $resultadoImagen['ruta'],
                    ':autor'     => $usuario_nombre,
                    ':usuario_id'=> $usuario_id
                ]);
                $mensaje = "¡Tu obra original fue enviada a revisión! Un administrador la revisará antes de que puedas publicar capítulos.";
            } catch (PDOException $e) {
                error_log("Error crear novela: " . $e->getMessage());
                $error = "Ocurrió un error al registrar tu novela en la base de datos.";
            }
        }
    } else {
        $error = "Por favor ingresa un título y adjunta una imagen de portada (PNG o JPG).";
    }
}

// CONSULTAR LAS OBRAS REGISTRADAS POR ESTE CREADOR
$mis_novelas = [];
try {
    $stmt = $conexion->prepare('SELECT id, "Titulo", "Portada", "estado_revision" FROM novela WHERE "usuario_id" = :uid ORDER BY id DESC');
    $stmt->execute([':uid' => $usuario_id]);
    $mis_novelas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al listar novelas: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Creador - Foxnovel</title>
    <link rel="stylesheet" href="Style.css">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container" style="max-width: 1000px; margin: 30px auto;">
        <h2 style="color: #ff6b35;">✍️ Publicar Obra Original</h2>
        
        <?php if ($mensaje): ?><div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

        <!-- FORMULARIO DE REGISTRO DE NOVELA ORIGINAL -->
        <div class="filter-sidebar form-sidebar" style="margin-bottom: 30px;">
            <h3 style="color: #f5ebe6; border-bottom: 1px solid #3b3129; padding-bottom: 10px;">Registrar Nueva Novela Original</h3>
            
            <form action="creador.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="crear_novela">

                <div class="form-group">
                    <label>Título de la Obra:</label>
                    <input type="text" name="titulo" class="finder-input" placeholder="Escribe el nombre de tu historia" required>
                </div>

                <div class="form-group">
                    <label>Portada de la obra (Sube una imagen PNG o JPG $\rightarrow$ se optimizará a WebP):</label>
                    <input type="file" name="portada" accept="image/png, image/jpeg" class="finder-input" required style="padding: 8px;">
                </div>

                <div class="form-group">
                    <label>Sinopsis / Descripción corta:</label>
                    <textarea name="sipnosis" class="finder-input" rows="4" placeholder="¿De qué trata tu novela?" style="resize: vertical;"></textarea>
                </div>

                <button type="submit" class="btn-leer form-btn">Enviar a Revisión</button>
            </form>
        </div>

        <!-- LISTA DE OBRAS REGISTRADAS -->
        <div class="filter-sidebar form-sidebar">
            <h3 style="color: #f5ebe6;">Mis Obras Registradas</h3>

            <?php if (!empty($mis_novelas)): ?>
                <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 15px;">
                    <?php foreach ($mis_novelas as $nov): ?>
                        <div style="display: flex; gap: 15px; background: #1a1613; padding: 15px; border-radius: 8px; align-items: center; border: 1px solid #3b3129;">
                            <img src="<?php echo htmlspecialchars($nov['Portada']); ?>" style="width: 65px; height: 90px; object-fit: cover; border-radius: 4px;">
                            <div style="flex-grow: 1;">
                                <h4 style="margin: 0; color: #f5ebe6;"><?php echo htmlspecialchars($nov['Titulo']); ?></h4>
                                <span class="badge-rol" style="margin-top: 5px; display: inline-block; background: <?php echo ($nov['estado_revision'] ?? '') === 'aprobado' ? '#2e7d32' : '#d9534f'; ?>;">
                                    <?php echo strtoupper($nov['estado_revision'] ?? 'PENDIENTE'); ?>
                                </span>
                            </div>
                            
                            <div>
                                <?php if (($nov['estado_revision'] ?? '') === 'aprobado'): ?>
                                    <a href="redactar_capitulo.php?novela_id=<?php echo $nov['id']; ?>" class="btn-creador" style="text-decoration: none;">➕ Redactar Capítulo</a>
                                <?php else: ?>
                                    <button class="btn-creador" disabled style="opacity: 0.5; cursor: not-allowed;" title="Aún está pendiente de aprobación por el administrador">🔒 En Revisión</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: #b0a8a0; margin-top: 15px;">Aún no has registrado ninguna novela original.</p>
            <?php endif; ?>
        </div>

        <!-- GUÍA DE FORMATO MARKDOWN -->
        <div class="filter-sidebar form-sidebar" style="margin-top: 30px;">
            <h3 style="color: #ff6b35;">📖 Guía Práctica de Markdown para Autores</h3>
            <p style="color: #b0a8a0; font-size: 0.9em;">Aplica estas etiquetas al escribir el texto de tus capítulos para formatearlo fácilmente:</p>
            
            <table style="width: 100%; color: #f5ebe6; font-size: 0.9em; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="border-bottom: 1px solid #3b3129; text-align: left; color: #ff6b35;">
                        <th style="padding: 8px;">Lo que escribes</th>
                        <th style="padding: 8px;">Resultado en pantalla</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid #2a221c;">
                        <td style="padding: 8px;"><code>**Texto resaltado**</code></td>
                        <td style="padding: 8px;"><strong>Texto resaltado</strong></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #2a221c;">
                        <td style="padding: 8px;"><code>*Texto en cursiva*</code></td>
                        <td style="padding: 8px;"><em>Texto en cursiva</em></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #2a221c;">
                        <td style="padding: 8px;"><code># Capítulo 1</code> / <code>## Título de Escena</code></td>
                        <td style="padding: 8px;"><strong style="font-size: 1.1em;">Títulos y Encabezados</strong></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #2a221c;">
                        <td style="padding: 8px;"><code>Palabra[^1]</code> y <code>[^1]: Explicación o nota</code></td>
                        <td style="padding: 8px;">Palabra<sup>1</sup> (Notas a pie de página)</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px;"><code>---</code></td>
                        <td style="padding: 8px;">Separador de escena (Línea)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>