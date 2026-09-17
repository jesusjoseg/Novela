<?php
session_start();
require_once 'HHH/Conexion.php';

// 1. Verificación de Seguridad (Solo Administradores)
$rol_actual = $_SESSION['usuario_rol'] ?? $_SESSION['rol'] ?? null;
if (!$rol_actual || $rol_actual !== 'admin') {
    header('Location: index.php');
    exit();
}

// Función para reordenar automáticamente los números de capítulos con PDO
function reordenaCapitulo($conexion, $novela_id)
{
    try {
        $stmt = $conexion->prepare("SELECT id FROM capitulos WHERE novela_id = :novela_id ORDER BY id ASC");
        $stmt->execute([':novela_id' => $novela_id]);
        $capitulos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $nuevo_numero = 1;
        $update = $conexion->prepare('UPDATE capitulos SET "Capitulo" = :capitulo WHERE id = :id');
        
        foreach ($capitulos as $cap) {
            $update->execute([
                ':capitulo' => $nuevo_numero,
                ':id'       => $cap['id']
            ]);
            $nuevo_numero++;
        }
    } catch (PDOException $e) {
        error_log("Error reordenando capítulos: " . $e->getMessage());
    }
}

$mensaje_novela = "";
$mensaje_Capitulo = "";

// 2. Procesamiento de Formularios POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- ACCIÓN: GUARDAR NOVELA ---
    if (isset($_POST['accion']) && $_POST['accion'] === 'guardar_novela') {
        $titulo      = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $portada     = trim($_POST['portada'] ?? '');
        $estado      = trim($_POST['estado'] ?? 'Pendiente');

        if (isset($_POST['Genero']) && is_array($_POST['Genero'])) {
            $genero = implode(', ', $_POST['Genero']);
        } else {
            $genero = 'sin Genero';
        }

        if (!empty($titulo)) {
            try {
                // Inserción sin enviar la columna usuario_id
                $sqlInsert = 'INSERT INTO novela (
                            "Titulo", "Descripcion", "Genero", "Portada", "link", "Estado", 
                            "Visitas", "es_oficial", "estado_revision"
                          ) 
                          VALUES (
                            :titulo, :descripcion, :genero, :portada, \'\', :estado, 
                            0, TRUE, \'aprobado\'
                          ) 
                          RETURNING id';
                
                $stmt = $conexion->prepare($sqlInsert);
                $stmt->execute([
                    ':titulo'      => $titulo,
                    ':descripcion' => $descripcion,
                    ':genero'      => $genero,
                    ':portada'     => $portada,
                    ':estado'      => $estado
                ]);
                
                $id_novela = $stmt->fetchColumn();

                // Generación de la URL slug limpia
                $nombre_limpio = strtolower($titulo);
                $nombre_limpio = preg_replace('/[^a-z0-9 -]/', '', $nombre_limpio);
                $nombre_limpio = str_replace(' ', '-', $nombre_limpio);
                $link_dinamico = "ver_novela.php?id=" . $id_novela . "&nombre=" . $nombre_limpio;

                // Actualizar el link en la base de datos
                $update = $conexion->prepare('UPDATE novela SET "link" = :link WHERE id = :id');
                $update->execute([
                    ':link' => $link_dinamico,
                    ':id'   => $id_novela
                ]);

                $mensaje_novela = "¡Novela registrada con éxito: " . htmlspecialchars($nombre_limpio) . "!";

            } catch (PDOException $e) {
                error_log("Error guardando novela: " . $e->getMessage());
                $mensaje_novela = "Error al registrar la novela en Supabase: " . htmlspecialchars($e->getMessage());
            }
        }
    }

    // --- ACCIÓN: SUBIR CAPÍTULO ---
    if (isset($_POST['accion']) && $_POST['accion'] === 'subir_capitulo') {
        $novela_id       = intval($_POST['novela_id'] ?? 0);
        $titulo_capitulo = trim($_POST['titulo_capitulo'] ?? '');
        $markdown        = $_POST['contenido_markdown'] ?? '';

        if ($novela_id > 0 && !empty($markdown) && !empty($titulo_capitulo)) {
            try {
                // Obtener el último número de capítulo asignado
                $check = $conexion->prepare('SELECT MAX("Capitulo") as ultimo FROM capitulos WHERE novela_id = :novela_id');
                $check->execute([':novela_id' => $novela_id]);
                $res = $check->fetch(PDO::FETCH_ASSOC);
                
                $num_capitulo = ($res && $res['ultimo'] !== null) ? intval($res['ultimo']) + 1 : 1;

                // Insertar el nuevo capítulo
                $stmt = $conexion->prepare('INSERT INTO capitulos (novela_id, "Capitulo", "Titulo", "Contenido_markdown") 
                                            VALUES (:novela_id, :capitulo, :titulo, :markdown)');
                
                $stmt->execute([
                    ':novela_id' => $novela_id,
                    ':capitulo'  => $num_capitulo,
                    ':titulo'    => $titulo_capitulo,
                    ':markdown'  => $markdown
                ]);

                // Reordenar secuencia de números
                reordenaCapitulo($conexion, $novela_id);
                $mensaje_Capitulo = "¡Capítulo publicado con éxito!";

            } catch (PDOException $e) {
                error_log("Error subiendo capítulo: " . $e->getMessage());
                $mensaje_Capitulo = "Error al subir capítulo: " . htmlspecialchars($e->getMessage());
            }
        }
    }
}

// 3. Obtener el listado de novelas para el desplegable (usando alias para PostgreSQL)
try {
    $stmtNovelas = $conexion->query('SELECT id, "Titulo" AS titulo FROM novela ORDER BY "Titulo" ASC');
    $listado_novela = $stmtNovelas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $listado_novela = [];
    error_log("Error al consultar novelas: " . $e->getMessage());
}

$nombre_usuario = $_SESSION['usuario_nombre'] ?? $_SESSION['nombre'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovelaFox -- Dashboard Administrativo</title>
    <link rel="stylesheet" href="Style.css">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked-footnote/dist/index.umd.min.js"></script>
</head>

<body>
    <?php include 'header.php'; ?>
    
    <div class="container form-container">
        <aside class="filter-sidebar form-sidebar">
            <h2 class="form-title">Panel de Control</h2>
            <p>Bienvenido, <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong></p>

            <div class="tabs-nav">
                <button class="tab-btn active" onclick="switchTab('tab-novelas')">Registrar Novela</button>
                <button class="tab-btn" onclick="switchTab('tab-capitulo')">Subir Capítulos</button>
                <button class="tab-btn" onclick="switchTab('tab-edita')">Edita Novela</button>
                <button class="tab-btn" onclick="switchTab('tab-editacap')">Edita Capitul</button>
                <button class="tab-btn" onclick="switchTab('tab-creator')">sistemas de Creador</button>
            </div>

            <!-- Pestaña: Registrar Novela -->
            <div id="tab-novelas" class="tab-content active">
                <?php if ($mensaje_novela): ?>
                    <p id="mensajedash" style="color: green; font-weight: bold;"><?php echo $mensaje_novela; ?></p>
                <?php endif; ?>
                
                <form action="dashboard.php" method="post">
                    <input type="hidden" name="accion" value="guardar_novela">
                    
                    <div class="form-group">
                        <h3>Título:</h3>
                        <input type="text" name="titulo" class="finder-input" required>
                    </div>

                    <div class="form-group">
                        <h3>Género:</h3>
                        <div class="generos-grid">
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Accion">Accion</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Aventura">Aventura</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Artes Marciales">Artes Marciales</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Supervivencia">Supervivencia</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Militar">Militar</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Fantasia">Fantasia</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Alta Fantasia">Alta Fantasia</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Isekai">Isekai</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Reencarnacion">Reencarnacion</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Transmigracion">Transmigracion</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Xianxia">Xianxia</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Xuanhuan">Xuanhuan</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Wuxia">Wuxia</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Fantasía Urbana">Fantasía Urbana</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Magia">Magia</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Sistema">Sistema</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="LitRPG">LitRPG</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Videojuegos">Videojuegos</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Ciencia Ficcion">Ciencia Ficcion</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Cyberpunk">Cyberpunk</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Mecha">Mecha</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Apocalptico">Apocalptico</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="GenderBender">Gender Bender</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Yuri">Yuri</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Shoujo Ai">Shoujo Ai</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Yaoi">Yaoi</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Shounen Ai">Shounen Ai</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Romance">Romance</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Comedia Romantica">Comedia Romantica</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Harem">Harem</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Harem Inverso">Harem Inverso</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Drama">Drama</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Tragedia">Tragedia</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Misterio">Misterio</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Psicologico">Psicologico</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Horror">Horror</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Sobrenatural">Sobrenatural</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Slice of Life">Slice of Life</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Comedia">Comedia</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Vida Escolar">Vida Escolar</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Historico">Historico</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Realeza">Realeza</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Deportes">Deportes</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Mature">Mature</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Ecchi">Ecchi</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <h3>URL Portada:</h3>
                        <input type="text" name="portada" class="finder-input" required>
                    </div>

                    <div class="form-group">
                        <h3>Estado:</h3>
                        <select name="estado" class="finder-input">
                            <option value="Pendiente">Pendiente</option>
                            <option value="Completado">Completado</option>
                            <option value="Pausado">Pausado</option>
                            <option value="Finalizado">Finalizado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <h3>Descripción:</h3>
                        <textarea id="texa" name="descripcion" class="finder-input" required></textarea>
                    </div>

                    <input type="submit" value="Guardar Novela" class="btn-leer form-btn">
                </form>
            </div>

            <!-- Pestaña: Subir Capítulos -->
            <div id="tab-capitulo" class="tab-content">
                <?php if ($mensaje_Capitulo): ?> 
                    <p style="color: green; font-weight: bold;"><?php echo $mensaje_Capitulo; ?></p>
                <?php endif; ?>
                
                <form action="dashboard.php" method="post">
                    <input type="hidden" name="accion" value="subir_capitulo">
                    
                    <div class="form-group">
                        <h3>Seleccionar Novela:</h3>
                        <select name="novela_id" class="finder-input" required>
                            <option value="">-- Seleccionar --</option>
                            <?php foreach ($listado_novela as $n): ?>
                                <option value="<?php echo $n['id']; ?>">
                                    <?php echo htmlspecialchars($n['titulo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <h3>Título del Capítulo:</h3>
                        <input type="text" name="titulo_capitulo" class="finder-input" required>
                    </div>

                    <div class="editor-split">
                        <div class="form-group">
                            <h3>Contenido (Markdown)</h3>
                            <textarea name="contenido_markdown" class="finder-input" id="md-editor"></textarea>
                        </div>
                        <div>
                            <h3>Vista previa</h3>
                            <div id="md-preview" class="box-preview"></div>
                        </div>
                    </div>

                    <input type="submit" value="Publicar Capítulo" class="btn-leer form-btn">
                </form>
            </div>
        </aside>
    </div>

    <script src="Novela.js"></script>
</body>
</html>