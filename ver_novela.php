<?php
session_start();
require_once 'HHH/Conexion.php';

$id_novela = isset($_GET['id']) ? intval($_GET['id']) : 0;
$novela = null;
$primer_capitulo_id = null;
$lista_capitulo = [];
$lista_comentarios = [];

// 1. Acumular visita SOLO si el usuario hace clic en "Comenzar a Leer"
/*if (isset($_GET['accion']) && $_GET['accion'] === 'incrementar' && $id_novela > 0) {
    try {
        $stmt_visita = $conexion->prepare('UPDATE novela SET "Visitas" = COALESCE("Visitas", 0) + 1 WHERE id = :id');
        $stmt_visita->execute([':id' => $id_novela]);
    } catch (PDOException $e) {
        error_log("Error actualizando visitas: " . $e->getMessage());
    }
}
*/
// 2. Obtener la novela desde la base de datos (PostgreSQL/Supabase)
try {
    $stmt = $conexion->prepare('SELECT id, "Titulo", "Descripcion", "Genero", "Estado", "Portada", "link", "Visitas" FROM novela WHERE id = :id');
    $stmt->execute([':id' => $id_novela]);
    $novela = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error obteniendo novela: " . $e->getMessage());
}

if (!$novela) {
    header("Location: index.php");
    exit();
}

// 3. Verificar estado de suscripción del usuario en sesión
$es_vip = $_SESSION['usuario_vip'] ?? $_SESSION['es_vip'] ?? false;

// 4. Obtener la lista de capítulos (incluyendo fecha de creación para la regla de 15 días)
try {
    $stmt_cap = $conexion->prepare('SELECT id, capitulo, titulo, creado_en FROM capitulos WHERE novela_id = :novela_id ORDER BY capitulo ASC');
    $stmt_cap->execute([':novela_id' => $id_novela]);
    $lista_capitulo = $stmt_cap->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error obteniendo capítulos: " . $e->getMessage());
}

if (!empty($lista_capitulo)) {
    $primer_capitulo_id = $lista_capitulo[0]['id'];
}

// 5. Procesar el envío de un nuevo comentario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_comentario'])) {
    $comentario_texto = trim($_POST['nuevo_comentario']);
    $usuario_id = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : null;

    if (!empty($comentario_texto) && $usuario_id !== null) {
        try {
            $stmt_ins = $conexion->prepare("INSERT INTO comentarios (usuario_id, novela_id, comentario) VALUES (:usuario_id, :novela_id, :comentario)");
            $stmt_ins->execute([
                ':usuario_id' => $usuario_id,
                ':novela_id'  => $id_novela,
                ':comentario' => $comentario_texto
            ]);

            header("Location: ver_novela.php?id=" . $id_novela);
            exit();
        } catch (PDOException $e) {
            error_log("Error guardando comentario: " . $e->getMessage());
        }
    }
}

// 6. Obtener la lista de comentarios
try {
    $stmt_com = $conexion->prepare("
        SELECT c.id, c.comentario AS texto, c.creado_en AS fecha, u.nombre AS usuario 
        FROM comentarios c 
        JOIN usuarios u ON c.usuario_id = u.id 
        WHERE c.novela_id = :novela_id 
        ORDER BY c.creado_en DESC
    ");
    $stmt_com->execute([':novela_id' => $id_novela]);
    $lista_comentarios = $stmt_com->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error obteniendo comentarios: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($novela['Titulo']); ?> - Lectura Novela</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
       <div class="novela-detalle-layout">
        <aside class="novela-sidebar">
            <img src="<?php echo htmlspecialchars($novela['Portada']); ?>" class="novela-portada" alt="<?php echo htmlspecialchars($novela['Titulo']); ?>">
            <div class="novela-ficha">
                <p><strong>Género: </strong> <?php echo htmlspecialchars($novela['Genero']); ?></p>
                <p><strong>Estado: </strong> <?php echo htmlspecialchars($novela['Estado']); ?></p>
                <p><strong>Visitas: </strong> <?php echo htmlspecialchars($novela['Visitas'] ?? 0); ?></p>
            </div>
        </aside>
        <main class="novela-contenido">
            <h1 class="novela-titulo-principal"><?php echo htmlspecialchars($novela['Titulo']); ?></h1>
            <h3>Sinopsis</h3>
            <p class="novela-sinopsis"><?php echo htmlspecialchars($novela['Descripcion']); ?></p>
            
            <div class="novela-acciones">
                <?php if ($primer_capitulo_id): ?>
                    <!-- Redirige activando el parámetro accion=incrementar para sumar la visita -->
                    <a href="ver_novela.php?id=<?php echo $id_novela; ?>&accion=incrementar" class="btn-leer" onclick="window.location.href='leer_capitulo.php?id=<?php echo $primer_capitulo_id; ?>'; return false;">Comenzar a Leer</a>
                <?php else: ?>
                    <span class="btn-disabled">Sin capítulos disponibles</span>
                <?php endif; ?>
            </div>
            
            <div class="seccion-Capitulos">
                <h3>Lista de Capítulos</h3>
                <?php if (!empty($lista_capitulo)): ?>
                    <ul class="lista_capitulos">
                        <?php 
                        $fecha_actual = new DateTime();
                        foreach ($lista_capitulo as $cap): 
                            $num_cap = intval($cap['capitulo']);
                            
                            // Evaluar bloqueo por fecha (15 días de antigüedad)
                            $bloqueado = false;
                            if ($num_cap > 15 && !$es_vip) {
                                $fecha_cap = new DateTime($cap['creado_en'] ?? 'now');
                                $diferencia_dias = $fecha_actual->diff($fecha_cap)->days;
                                if ($diferencia_dias < 15) {
                                    $bloqueado = true;
                                }
                            }
                        ?>
                            <li class="<?php echo $bloqueado ? 'capitulo-bloqueado' : ''; ?>">
                                <?php if ($bloqueado): ?>
                                    <span class="cap-link deshabilitado">
                                        <i class="fa-solid fa-lock"></i> Capítulo <?php echo $num_cap; ?>: <?php echo htmlspecialchars($cap['titulo']); ?> 
                                        <small>(Disponible con Suscripción o en <?php echo (15 - $diferencia_dias); ?> días)</small>
                                    </span>
                                <?php else: ?>
                                    <a href="leer_capitulo.php?id=<?php echo $cap['id']; ?>">
                                        Capítulo <?php echo $num_cap; ?>: <?php echo htmlspecialchars($cap['titulo']); ?>
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>  
                    </ul>
                <?php else: ?>
                    <p>Aún no hay capítulos subidos para esta novela.</p>
                <?php endif; ?>
            </div>

            <!-- Sección de Comentarios -->
            <div class="seccion-comentarios">
                <h3>Comentarios (<?php echo count($lista_comentarios); ?>)</h3>

                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <form method="POST" class="form-comentario">
                        <textarea name="nuevo_comentario" placeholder="Escribe tu opinión sobre esta novela..." required></textarea>
                        <button type="submit" class="btn-comentar">Publicar Comentario</button>
                    </form>
                <?php else: ?>
                    <p><em>Debes iniciar sesión para publicar comentarios.</em></p>
                <?php endif; ?>

                <!-- Listado de Comentarios -->
                <div class="lista-comentarios">
                    <?php if (!empty($lista_comentarios)): ?>
                        <?php foreach ($lista_comentarios as $com): ?>
                            <div class="comentario-card">
                                <div class="comentario-header">
                                    <strong class="comentario-usuario"><?php echo htmlspecialchars($com['usuario']); ?></strong>
                                    <span class="comentario-fecha"><?php echo $com['fecha']; ?></span>
                                </div>
                                <p class="comentario-texto"><?php echo nl2br(htmlspecialchars($com['texto'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="sin-comentarios">Sé el primero en comentar esta novela.</p>
                    <?php endif; ?>
                </div>
            </div>

        </main>
       </div> 
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>