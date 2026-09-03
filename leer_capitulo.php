<?php
session_start();
include 'HHH/Conexion.php';

$id_capitulo = isset($_GET['id']) ? intval($_GET['id']) : 0;

$capitulo = null;
$cap_anterior = null;
$cap_siguiente = null;

if ($id_capitulo > 0) {
    // 1. Obtener los datos del capítulo actual
    $stmt = $coon->prepare("SELECT id, novela_id, Capitulo, Titulo, Contenido_markdown FROM Capitulos WHERE id = ?");
    $stmt->bind_param("i", $id_capitulo);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows > 0) {
        $capitulo = $res->fetch_assoc();
        $novela_id = $capitulo['novela_id'];
        $num_cap = $capitulo['Capitulo'];

        // 2. Buscar Capítulo Anterior
        $stmt_ant = $coon->prepare("SELECT id FROM Capitulos WHERE novela_id = ? AND Capitulo < ? ORDER BY Capitulo DESC LIMIT 1");
        $stmt_ant->bind_param("ii", $novela_id, $num_cap);
        $stmt_ant->execute();
        $res_ant = $stmt_ant->get_result();
        if ($row_ant = $res_ant->fetch_assoc()) {
            $cap_anterior = $row_ant['id'];
        }
        $stmt_ant->close();

        // 3. Buscar Capítulo Siguiente
        $stmt_sig = $coon->prepare("SELECT id FROM Capitulos WHERE novela_id = ? AND Capitulo > ? ORDER BY Capitulo ASC LIMIT 1");
        $stmt_sig->bind_param("ii", $novela_id, $num_cap);
        $stmt_sig->execute();
        $res_sig = $stmt_sig->get_result();
        if ($row_sig = $res_sig->fetch_assoc()) {
            $cap_siguiente = $row_sig['id'];
        }
        $stmt_sig->close();
    }
    $stmt->close();
}

if (!$capitulo) {
    header("Location: biblioteca.php");
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
    <!-- CDN para convertir Markdown a HTML en tiempo real -->
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

            <!-- Sección de Comentarios (Caja básica / Sistema externo) -->
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

    <!-- Script para renderizar el texto Markdown de la BD -->
    <script>
        const markdownTexto = <?php echo json_encode($capitulo['Contenido_markdown']); ?>;
        document.getElementById('contenido-markdown').innerHTML = marked.parse(markdownTexto);
    </script>
</body>
</html>