<?php
session_start();
require_once 'HHH/Conexion.php';

$id_capitulo = isset($_GET['id']) ? intval($_GET['id']) : 0;

$capitulo = null;
$cap_anterior = null;
$cap_siguiente = null;

if ($id_capitulo > 0) {
    try {
        // 1. Obtener los datos del capítulo actual (PostgreSQL PDO con comillas dobles)
        $stmt = $conexion->prepare('SELECT id, novela_id, "Capitulo", "Titulo", "Contenido_markdown" FROM capitulos WHERE id = :id');
        $stmt->execute([':id' => $id_capitulo]);
        $capitulo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($capitulo) {
            $novela_id = $capitulo['novela_id'];
            $num_cap = $capitulo['Capitulo'];

            // 2. Buscar Capítulo Anterior
            $stmt_ant = $conexion->prepare('SELECT id FROM capitulos WHERE novela_id = :novela_id AND "Capitulo" < :num_cap ORDER BY "Capitulo" DESC LIMIT 1');
            $stmt_ant->execute([
                ':novela_id' => $novela_id,
                ':num_cap'   => $num_cap
            ]);
            $row_ant = $stmt_ant->fetch(PDO::FETCH_ASSOC);
            if ($row_ant) {
                $cap_anterior = $row_ant['id'];
            }

            // 3. Buscar Capítulo Siguiente
            $stmt_sig = $conexion->prepare('SELECT id FROM capitulos WHERE novela_id = :novela_id AND "Capitulo" > :num_cap ORDER BY "Capitulo" ASC LIMIT 1');
            $stmt_sig->execute([
                ':novela_id' => $novela_id,
                ':num_cap'   => $num_cap
            ]);
            $row_sig = $stmt_sig->fetch(PDO::FETCH_ASSOC);
            if ($row_sig) {
                $cap_siguiente = $row_sig['id'];
            }
        }
    } catch (PDOException $e) {
        error_log("Error al consultar el capítulo: " . $e->getMessage());
    }
}

if (!$capitulo) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capítulo <?php echo $capitulo['Capitulo']; ?>: <?php echo htmlspecialchars($capitulo['Titulo']); ?></title>
    <link rel="stylesheet" href="Style.css">
    <!-- CDN para convertir Markdown a HTML -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <main class="lector-container">
            
            <!-- Encabezado del Capítulo -->
            <header class="lector-header">
                <h1>Capítulo <?php echo $capitulo['Capitulo']; ?>: <?php echo htmlspecialchars($capitulo['Titulo']); ?></h1>
                <a href="ver_novela.php?id=<?php echo $capitulo['novela_id']; ?>" class="btn-volver">Volver a la Novela</a>
            </header>

            <!-- Botones de Navegación (Superior) -->
            <div class="nav-capitulos">
                <?php if ($cap_anterior): ?>
                    <a href="leer_capitulo.php?id=<?php echo $cap_anterior; ?>" class="btn-nav">← Anterior</a>
                <?php else: ?>
                    <span class="btn-nav disabled">← Anterior</span>
                <?php endif; ?>

                <?php if ($cap_siguiente): ?>
                    <a href="leer_capitulo.php?id=<?php echo $cap_siguiente; ?>" class="btn-nav">Siguiente →</a>
                <?php else: ?>
                    <span class="btn-nav disabled">Siguiente →</span>
                <?php endif; ?>
            </div>

            <!-- Contenido Renderizado de Markdown -->
            <article id="contenido-markdown" class="lector-contenido"></article>

            <!-- Botones de Navegación (Inferior) -->
            <div class="nav-capitulos">
                <?php if ($cap_anterior): ?>
                    <a href="leer_capitulo.php?id=<?php echo $cap_anterior; ?>" class="btn-nav">← Anterior</a>
                <?php else: ?>
                    <span class="btn-nav disabled">← Anterior</span>
                <?php endif; ?>

                <?php if ($cap_siguiente): ?>
                    <a href="leer_capitulo.php?id=<?php echo $cap_siguiente; ?>" class="btn-nav">Siguiente →</a>
                <?php else: ?>
                    <span class="btn-nav disabled">Siguiente →</span>
                <?php endif; ?>
            </div>

            <!-- Sección de Comentarios -->
            <section class="seccion-comentarios">
                <h3>Comentarios</h3>
                <div class="comentarios-caja">
                    <textarea class="comentario-input" placeholder="Escribe un comentario..."></textarea>
                    <button class="btn-comentar">Publicar Comentario</button>
                </div>
                <div id="lista-comentarios">
                    <!-- Aquí se listarán los comentarios -->
                </div>
            </section>

        </main>
    </div>

    <!-- Script para renderizar Markdown con soporte para Notas al Pie -->
    <script>
        const markdownTexto = <?php echo json_encode($capitulo['Contenido_markdown']); ?>;

        function procesarNotasPie(texto) {
            if (!texto) return '';

            // 1. Reemplazar las DEFINICIONES de notas al pie [^id]: texto
            let md = texto.replace(
                /^\[\^([a-zA-Z0-9_-]+)\]:\s*(.*)$/gm,
                '<div class="footnote-item" id="fn-$1" style="margin-top: 10px; font-size: 0.9em; opacity: 0.8;"><strong>[$1]</strong> $2 <a href="#fnref-$1" onclick="event.preventDefault(); document.getElementById(\'fnref-$1\')?.scrollIntoView({behavior: \'smooth\'});">↩</a></div>'
            );

            // 2. Reemplazar las REFERENCIAS [^id] por superíndices
            md = md.replace(
                /\[\^([a-zA-Z0-9_-]+)\](?!\:)/g,
                '<sup class="footnote-ref"><a href="#fn-$1" id="fnref-$1" onclick="event.preventDefault(); document.getElementById(\'fn-$1\')?.scrollIntoView({behavior: \'smooth\'});">[$1]</a></sup>'
            );

            return md;
        }

        if (markdownTexto) {
            const textoProcesado = procesarNotasPie(markdownTexto);
            if (typeof marked !== 'undefined') {
                document.getElementById('contenido-markdown').innerHTML = marked.parse(textoProcesado);
            } else {
                document.getElementById('contenido-markdown').innerHTML = textoProcesado;
            }
        }
    </script>
</body>
</html>