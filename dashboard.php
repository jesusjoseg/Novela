<?php
session_start();
require_once __DIR__ . '/HHH/Conexion.php';

// 1. Verificación de Seguridad (Solo Administradores)
$rol_actual = $_SESSION['usuario_rol'] ?? $_SESSION['rol'] ?? null;

// Obtener rol actualizado de Supabase si existe sesión
if (isset($_SESSION['usuario_id'])) {
    $resRol = supabase_request('usuario?select=rol&id=eq.' . $_SESSION['usuario_id'], 'GET');
    if (isset($resRol[0]['rol'])) {
        $rol_actual = $resRol[0]['rol'];
    }
}

if (!$rol_actual || strtolower($rol_actual) !== 'admin') {
    header('Location: index.php');
    exit();
}

$mensaje_novela = "";
$mensaje_capitulo = "";
$mensaje_creador = "";
$mensaje_aprobar = "";

// 2. Procesamiento de Formularios POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    
    // --- ACCIÓN: GUARDAR NUEVA NOVELA (ADMIN) ---
    if ($_POST['accion'] === 'guardar_novela') {
        $titulo      = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $portada     = trim($_POST['portada'] ?? '');
        $estado      = trim($_POST['estado'] ?? 'Pendiente');

        $genero = (isset($_POST['Genero']) && is_array($_POST['Genero'])) 
            ? implode(', ', $_POST['Genero']) 
            : 'sin Genero';

        if (!empty($titulo)) {
            $dataNovela = [
                'Titulo' => $titulo,
                'Descripcion' => $descripcion,
                'Genero' => $genero,
                'Portada' => $portada,
                'link' => '',
                'Estado' => $estado,
                'Visitas' => 0,
                'es_oficial' => true,
                'estado_revision' => 'aprobado'
            ];

            $res = supabase_request('novela', 'POST', $dataNovela);

            if (!isset($res['error']) && is_array($res) && isset($res[0]['id'])) {
                $id_novela = $res[0]['id'];
                
                // Slug dinámico
                $nombre_limpio = strtolower($titulo);
                $nombre_limpio = preg_replace('/[^a-z0-9 -]/', '', $nombre_limpio);
                $nombre_limpio = str_replace(' ', '-', $nombre_limpio);
                $link_dinamico = "ver_novela.php?id=" . $id_novela . "&nombre=" . $nombre_limpio;

                // Actualizar link de novela
                supabase_request('novela?id=eq.' . $id_novela, 'PATCH', ['link' => $link_dinamico]);

                $mensaje_novela = "¡Novela registrada con éxito!";
            } else {
                $mensaje_novela = "Error al registrar la novela en Supabase.";
            }
        }
    }

    // --- ACCIÓN: EDITAR NOVELA EXISTENTE ---
    if ($_POST['accion'] === 'editar_novela') {
        $novela_id   = intval($_POST['edit_novela_id'] ?? 0);
        $titulo      = trim($_POST['edit_titulo'] ?? '');
        $descripcion = trim($_POST['edit_descripcion'] ?? '');
        $portada     = trim($_POST['edit_portada'] ?? '');
        $estado      = trim($_POST['edit_estado'] ?? 'Pendiente');

        $genero = (isset($_POST['edit_Genero']) && is_array($_POST['edit_Genero'])) 
            ? implode(', ', $_POST['edit_Genero']) 
            : 'sin Genero';

        if ($novela_id > 0 && !empty($titulo)) {
            $dataUpdate = [
                'Titulo' => $titulo,
                'Descripcion' => $descripcion,
                'Genero' => $genero,
                'Portada' => $portada,
                'Estado' => $estado
            ];

            $res = supabase_request('novela?id=eq.' . $novela_id, 'PATCH', $dataUpdate);
            if (!isset($res['error'])) {
                $mensaje_novela = "¡Novela actualizada correctamente!";
            } else {
                $mensaje_novela = "Error al actualizar la novela.";
            }
        }
    }

    // --- ACCIÓN: GESTIONAR NOVELA DE USUARIOS (APROBAR / RECHAZAR / ELIMINAR) ---
    if ($_POST['accion'] === 'gestionar_novela_usuario') {
        $novela_id = intval($_POST['novela_id'] ?? 0);
        $sub_accion = $_POST['sub_accion'] ?? '';

        if ($novela_id > 0) {
            if ($sub_accion === 'aprobar') {
                $res = supabase_request('novela?id=eq.' . $novela_id, 'PATCH', ['estado_revision' => 'aprobado']);
                if (!isset($res['error'])) {
                    $mensaje_aprobar = "¡Novela aprobada correctamente!";
                } else {
                    $mensaje_aprobar = "Error al aprobar la novela.";
                }
            } elseif ($sub_accion === 'rechazar') {
                $res = supabase_request('novela?id=eq.' . $novela_id, 'PATCH', ['estado_revision' => 'rechazado']);
                if (!isset($res['error'])) {
                    $mensaje_aprobar = "La novela ha sido marcada como rechazada.";
                } else {
                    $mensaje_aprobar = "Error al rechazar la novela.";
                }
            } elseif ($sub_accion === 'eliminar') {
                // Eliminar primero capítulos asociados
                supabase_request('capitulos?novela_id=eq.' . $novela_id, 'DELETE');
                // Eliminar la novela
                $res = supabase_request('novela?id=eq.' . $novela_id, 'DELETE');
                if (!isset($res['error'])) {
                    $mensaje_aprobar = "La novela ha sido eliminada permanentemente.";
                } else {
                    $mensaje_aprobar = "Error al eliminar la novela.";
                }
            }
        }
    }

    // --- ACCIÓN: SUBIR CAPÍTULO ---
    if ($_POST['accion'] === 'subir_capitulo') {
        $novela_id       = intval($_POST['novela_id'] ?? 0);
        $titulo_capitulo = trim($_POST['titulo_capitulo'] ?? '');
        $markdown        = $_POST['contenido_markdown'] ?? '';

        if ($novela_id > 0 && !empty($markdown) && !empty($titulo_capitulo)) {
            // Obtener último número de capítulo
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
                $mensaje_capitulo = "¡Capítulo publicado con éxito!";
            } else {
                $mensaje_capitulo = "Error al subir capítulo.";
            }
        }
    }

    // --- ACCIÓN: EDITAR CAPÍTULO EXISTENTE ---
    if ($_POST['accion'] === 'editar_capitulo') {
        $capitulo_id     = intval($_POST['edit_capitulo_id'] ?? 0);
        $titulo_capitulo = trim($_POST['edit_titulo_capitulo'] ?? '');
        $markdown        = $_POST['edit_contenido_markdown'] ?? '';

        if ($capitulo_id > 0 && !empty($titulo_capitulo)) {
            $dataUpdate = [
                'Titulo' => $titulo_capitulo,
                'Contenido_markdown' => $markdown
            ];

            $res = supabase_request('capitulos?id=eq.' . $capitulo_id, 'PATCH', $dataUpdate);
            if (!isset($res['error'])) {
                $mensaje_capitulo = "¡Capítulo actualizado con éxito!";
            } else {
                $mensaje_capitulo = "Error al actualizar capítulo.";
            }
        }
    }

    // --- ACCIÓN: GESTIONAR SOLICITUD DE CREADOR ---
    if ($_POST['accion'] === 'gestionar_creador') {
        $usuario_id_solicitud = intval($_POST['usuario_id_solicitud'] ?? 0);
        $estado_nuevo        = $_POST['nuevo_rol'] ?? '';

        if ($usuario_id_solicitud > 0 && in_array($estado_nuevo, ['creador', 'lector'])) {
            $res = supabase_request('usuario?id=eq.' . $usuario_id_solicitud, 'PATCH', ['rol' => $estado_nuevo]);
            if (!isset($res['error'])) {
                $mensaje_creador = "¡Estado de usuario actualizado correctamente!";
            } else {
                $mensaje_creador = "Error al procesar la solicitud.";
            }
        }
    }
}

// 3. Consultas para los select y listados
$listado_novelas = [];
$resNovelas = supabase_request('novela?select=id,Titulo,Descripcion,Genero,Portada,Estado&order=Titulo.asc', 'GET');
if (!isset($resNovelas['error']) && is_array($resNovelas)) {
    $listado_novelas = $resNovelas;
}

// Consultar novelas pendientes de revisión (Filtra por estado_revision=pendiente)
$novelas_pendientes = [];
$resNovelasPend = supabase_request('novela?select=id,Titulo,Descripcion,Genero,Portada,Estado,estado_revision,usuario_id,usuario(nombre,email)&estado_revision=eq.pendiente&order=id.desc', 'GET');

// Si la relación con usuario falla por falta de Foreign Key, intentamos traerla sin join
if (isset($resNovelasPend['error'])) {
    $resNovelasPend = supabase_request('novela?select=id,Titulo,Descripcion,Genero,Portada,Estado,estado_revision,usuario_id&estado_revision=eq.pendiente&order=id.desc', 'GET');
}

if (!isset($resNovelasPend['error']) && is_array($resNovelasPend)) {
    $novelas_pendientes = $resNovelasPend;
}

// Consultar usuarios pendientes de verificación
$solicitudes_creador = [];
$resCreadores = supabase_request('usuario?select=id,nombre,email,rol,fecha_registro&rol=eq.pendiente_creador', 'GET');
if (!isset($resCreadores['error']) && is_array($resCreadores)) {
    $solicitudes_creador = $resCreadores;
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
    
    <div class="container form-container" style="max-width: 950px;">
        <aside class="filter-sidebar form-sidebar">
            <h2 class="form-title">Panel de Control</h2>
            <p style="text-align:center; color:#ccc;">Bienvenido, <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong></p>

            <div class="tabs-nav">
                <button class="tab-btn active" onclick="switchTab('tab-novelas')">Registrar Novela</button>
                <button class="tab-btn" onclick="switchTab('tab-aprobar')">Aprobar Novelas</button>
                <button class="tab-btn" onclick="switchTab('tab-capitulo')">Subir Capítulos</button>
                <button class="tab-btn" onclick="switchTab('tab-edita')">Editar Novela</button>
                <button class="tab-btn" onclick="switchTab('tab-editacap')">Editar Capítulo</button>
                <button class="tab-btn" onclick="switchTab('tab-creator')">Sistema de Creadores</button>
            </div>

            <!-- Pestaña 1: Registrar Novela -->
            <div id="tab-novelas" class="tab-content active">
                <?php if ($mensaje_novela): ?>
                    <p style="color: #4ade80; font-weight: bold;"><?php echo $mensaje_novela; ?></p>
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
                        <textarea name="descripcion" class="finder-input" required></textarea>
                    </div>

                    <input type="submit" value="Guardar Novela" class="btn-leer form-btn">
                </form>
            </div>

            <!-- Pestaña 2: Aprobar / Revisar Novelas de Creadores -->
            <div id="tab-aprobar" class="tab-content">
                <h3 style="color:#ff6b35; margin-bottom: 15px;">Revisiones de Novelas Publicadas por Usuarios</h3>
                <?php if ($mensaje_aprobar): ?>
                    <p style="color: #4ade80; font-weight: bold;"><?php echo $mensaje_aprobar; ?></p>
                <?php endif; ?>

                <?php if (count($novelas_pendientes) > 0): ?>
                    <div style="overflow-x: auto;">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Autor</th>
                                    <th>Género</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($novelas_pendientes as $np): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($np['Titulo']); ?></strong></td>
                                        <td>
                                            <?php 
                                                $autor = $np['usuario'] ?? null;
                                                echo htmlspecialchars($autor['nombre'] ?? 'Creador');
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($np['Genero'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge badge-warning">
                                                <?php echo htmlspecialchars($np['estado_revision'] ?? 'pendiente'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <!-- Aprobar -->
                                            <form action="dashboard.php" method="post" style="display:inline-block;">
                                                <input type="hidden" name="accion" value="gestionar_novela_usuario">
                                                <input type="hidden" name="novela_id" value="<?php echo $np['id']; ?>">
                                                <input type="hidden" name="sub_accion" value="aprobar">
                                                <button type="submit" class="btn-approve">Aprobar</button>
                                            </form>

                                            <!-- Rechazar -->
                                            <form action="dashboard.php" method="post" style="display:inline-block;">
                                                <input type="hidden" name="accion" value="gestionar_novela_usuario">
                                                <input type="hidden" name="novela_id" value="<?php echo $np['id']; ?>">
                                                <input type="hidden" name="sub_accion" value="rechazar">
                                                <button type="submit" class="btn-reject">Rechazar</button>
                                            </form>

                                            <!-- Eliminar -->
                                            <form action="dashboard.php" method="post" style="display:inline-block;" onsubmit="return confirm('¿Estás seguro de eliminar esta novela definitivamente?');">
                                                <input type="hidden" name="accion" value="gestionar_novela_usuario">
                                                <input type="hidden" name="novela_id" value="<?php echo $np['id']; ?>">
                                                <input type="hidden" name="sub_accion" value="eliminar">
                                                <button type="submit" class="btn-delete">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="color:#a0a0a0;">No hay novelas publicadas para revisar.</p>
                <?php endif; ?>
            </div>

            <!-- Pestaña 3: Subir Capítulos -->
            <div id="tab-capitulo" class="tab-content">
                <?php if ($mensaje_capitulo): ?> 
                    <p style="color: #4ade80; font-weight: bold;"><?php echo $mensaje_capitulo; ?></p>
                <?php endif; ?>
                
                <form action="dashboard.php" method="post">
                    <input type="hidden" name="accion" value="subir_capitulo">
                    
                    <div class="form-group">
                        <h3>Seleccionar Novela:</h3>
                        <select name="novela_id" class="finder-input" required>
                            <option value="">-- Seleccionar --</option>
                            <?php foreach ($listado_novelas as $n): ?>
                                <option value="<?php echo $n['id']; ?>">
                                    <?php echo htmlspecialchars($n['Titulo']); ?>
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

            <!-- Pestaña 4: Editar Novela -->
            <div id="tab-edita" class="tab-content">
                <form action="dashboard.php" method="post">
                    <input type="hidden" name="accion" value="editar_novela">
                    
                    <div class="form-group">
                        <h3>Seleccionar Novela a Editar:</h3>
                        <select name="edit_novela_id" id="select_edit_novela" class="finder-input" required>
                            <option value="">-- Seleccionar Novela --</option>
                            <?php foreach ($listado_novelas as $n): ?>
                                <option value="<?php echo $n['id']; ?>" 
                                        data-titulo="<?php echo htmlspecialchars($n['Titulo']); ?>" 
                                        data-portada="<?php echo htmlspecialchars($n['Portada']); ?>"
                                        data-estado="<?php echo htmlspecialchars($n['Estado']); ?>"
                                        data-descripcion="<?php echo htmlspecialchars($n['Descripcion']); ?>">
                                    <?php echo htmlspecialchars($n['Titulo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <h3>Título:</h3>
                        <input type="text" id="edit_titulo" name="edit_titulo" class="finder-input" required>
                    </div>

                    <div class="form-group">
                        <h3>URL Portada:</h3>
                        <input type="text" id="edit_portada" name="edit_portada" class="finder-input" required>
                    </div>

                    <div class="form-group">
                        <h3>Estado:</h3>
                        <select id="edit_estado" name="edit_estado" class="finder-input">
                            <option value="Pendiente">Pendiente</option>
                            <option value="Completado">Completado</option>
                            <option value="Pausado">Pausado</option>
                            <option value="Finalizado">Finalizado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <h3>Descripción:</h3>
                        <textarea id="edit_descripcion" name="edit_descripcion" class="finder-input" required></textarea>
                    </div>

                    <input type="submit" value="Actualizar Novela" class="btn-leer form-btn">
                </form>
            </div>

            <!-- Pestaña 5: Editar Capítulo -->
            <div id="tab-editacap" class="tab-content">
                <div class="form-group">
                    <h3>1. Seleccionar Novela:</h3>
                    <select id="cap_novela_select" class="finder-input">
                        <option value="">-- Seleccionar Novela --</option>
                        <?php foreach ($listado_novelas as $n): ?>
                            <option value="<?php echo $n['id']; ?>"><?php echo htmlspecialchars($n['Titulo']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <form action="dashboard.php" method="post">
                    <input type="hidden" name="accion" value="editar_capitulo">
                    
                    <div class="form-group">
                        <h3>2. Seleccionar Capítulo:</h3>
                        <select name="edit_capitulo_id" id="edit_capitulo_id" class="finder-input" required>
                            <option value="">-- Primero selecciona una novela --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <h3>Título del Capítulo:</h3>
                        <input type="text" name="edit_titulo_capitulo" id="edit_titulo_capitulo" class="finder-input" required>
                    </div>

                    <div class="form-group">
                        <h3>Contenido (Markdown):</h3>
                        <textarea name="edit_contenido_markdown" id="edit_contenido_markdown" class="finder-input"></textarea>
                    </div>

                    <input type="submit" value="Actualizar Capítulo" class="btn-leer form-btn">
                </form>
            </div>

            <!-- Pestaña 6: Sistema de Creadores -->
            <div id="tab-creator" class="tab-content">
                <h3 style="color:#ff6b35; margin-bottom: 15px;">Solicitudes de Creador Pendientes</h3>
                <?php if ($mensaje_creador): ?>
                    <p style="color: #4ade80; font-weight: bold;"><?php echo $mensaje_creador; ?></p>
                <?php endif; ?>

                <?php if (count($solicitudes_creador) > 0): ?>
                    <div style="overflow-x: auto;">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Fecha Solicitud</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($solicitudes_creador as $sol): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($sol['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($sol['email']); ?></td>
                                        <td><?php echo htmlspecialchars($sol['fecha_registro'] ?? 'N/A'); ?></td>
                                        <td>
                                            <form action="dashboard.php" method="post" style="display:inline-block;">
                                                <input type="hidden" name="accion" value="gestionar_creador">
                                                <input type="hidden" name="usuario_id_solicitud" value="<?php echo $sol['id']; ?>">
                                                <input type="hidden" name="nuevo_rol" value="creador">
                                                <button type="submit" class="btn-approve">Aprobar</button>
                                            </form>
                                            <form action="dashboard.php" method="post" style="display:inline-block;">
                                                <input type="hidden" name="accion" value="gestionar_creador">
                                                <input type="hidden" name="usuario_id_solicitud" value="<?php echo $sol['id']; ?>">
                                                <input type="hidden" name="nuevo_rol" value="lector">
                                                <button type="submit" class="btn-reject">Rechazar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="color:#a0a0a0;">No hay solicitudes pendientes de creadores en este momento.</p>
                <?php endif; ?>
            </div>

        </aside>
    </div>

    <!-- Carga del archivo JS externo -->
    <script src="Novela.js"></script>
</body>
</html>