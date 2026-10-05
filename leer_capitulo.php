<?php
session_start();
require_once 'HHH/Conexion.php';

$id_capitulo = isset($_GET['id']) ? intval($_GET['id']) : 0;

$capitulo = null;
$cap_anterior = null;
$cap_siguiente = null;

if ($id_capitulo > 0) {
    // 1. Obtener datos del capítulo actual mediante Supabase REST
    $resCap = supabase_request("capitulos?select=id,novela_id,Capitulo,Titulo,Contenido_markdown&id=eq.{$id_capitulo}");
    
    if (is_array($resCap) && !empty($resCap) && !isset($resCap['error'])) {
        $capitulo = $resCap[0];
        $novela_id = $capitulo['novela_id'];
        $num_cap = $capitulo['Capitulo'];

        //
        $usuario_id=$_SESSION['usuario_id']??$_SESSION['id']??null;
        if ($usuario_id){
            $check_progreso = supabase_request("progreso_lectura?usuario_id=eq.{$usuario_id}&capitulo_id=eq.{$id_capitulo}&select=id");
            if(empty($check_progreso)|| isset($check_progreso['error'])){
                $nuevo_progreso=[
                    'usuario_id'=> $usuario_id,
                    'novela_id' => $novela_id,
                    'capitulo_id' =>$id_capitulo
                ];
                supabase_request('progeso_lectura','POST',$nuevo_progreso);
            }
        }
        // 2. Buscar Capítulo Anterior
        $resAnt = supabase_request("capitulos?select=id&novela_id=eq.{$novela_id}&Capitulo=lt.{$num_cap}&order=Capitulo.desc&limit=1");
        if (is_array($resAnt) && !empty($resAnt) && !isset($resAnt['error'])) {
            $cap_anterior = $resAnt[0]['id'];
        }

        // 3. Buscar Capítulo Siguiente
        $resSig = supabase_request("capitulos?select=id&novela_id=eq.{$novela_id}&Capitulo=gt.{$num_cap}&order=Capitulo.asc&limit=1");
        if (is_array($resSig) && !empty($resSig) && !isset($resSig['error'])) {
            $cap_siguiente = $resSig[0]['id'];
        }
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
    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capítulo <?php echo $capitulo['Capitulo']; ?>: <?php echo htmlspecialchars($capitulo['Titulo']); ?></title>
    <link rel="stylesheet" href="Style.css">
    <!-- CDN para convertir Markdown a HTML -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</head>
<body>
    <?php include 'Header.php'; ?>

    <div class="container">
        <main class="lector-container" style="max-width: 900px; margin: 0 auto; padding: 20px 15px;">
            
            <!-- Encabezado del Capítulo -->
            <header class="lector-header" style="margin-bottom: 25px;">
                <h1 style="word-break: break-word; overflow-wrap: break-word; white-space: normal; line-height: 1.3; font-size: 1.8rem; color: #ff6b35;">
                    Capítulo <?php echo $capitulo['Capitulo']; ?>: <?php echo htmlspecialchars($capitulo['Titulo']); ?>
                </h1>
                <a href="ver_novela.php?id=<?php echo $capitulo['novela_id']; ?>" class="btn-volver" style="display: inline-block; margin-top: 10px; color: #4ea8de; text-decoration: none;">
                    &larr; Volver a la Novela
                </a>
            </header>

            <!-- Botones de Navegación (Superior) -->
            <div class="nav-capitulos" style="display: flex; justify-content: space-between; margin-bottom: 25px;">
                <?php if ($cap_anterior): ?>
                    <a href="leer_capitulo.php?id=<?php echo $cap_anterior; ?>" class="btn-nav" style="padding: 8px 16px; background: #2b2b36; color: #fff; text-decoration: none; border-radius: 4px;">&larr; Anterior</a>
                <?php else: ?>
                    <span class="btn-nav disabled" style="padding: 8px 16px; background: #1a1a24; color: #666; border-radius: 4px;">&larr; Anterior</span>
                <?php endif; ?>

                <?php if ($cap_siguiente): ?>
                    <a href="leer_capitulo.php?id=<?php echo $cap_siguiente; ?>" class="btn-nav" style="padding: 8px 16px; background: #2b2b36; color: #fff; text-decoration: none; border-radius: 4px;">Siguiente &rarr;</a>
                <?php else: ?>
                    <span class="btn-nav disabled" style="padding: 8px 16px; background: #1a1a24; color: #666; border-radius: 4px;">Siguiente &rarr;</span>
                <?php endif; ?>
            </div>

            <!-- Contenido Renderizado de Markdown -->
            <article id="contenido-markdown" class="lector-contenido" style="line-height: 1.8; font-size: 1.1rem; word-break: break-word; overflow-wrap: break-word;"></article>

            <!-- Botones de Navegación (Inferior) -->
            <div class="nav-capitulos" style="display: flex; justify-content: space-between; margin-top: 35px; margin-bottom: 40px;">
                <?php if ($cap_anterior): ?>
                    <a href="leer_capitulo.php?id=<?php echo $cap_anterior; ?>" class="btn-nav" style="padding: 8px 16px; background: #2b2b36; color: #fff; text-decoration: none; border-radius: 4px;">&larr; Anterior</a>
                <?php else: ?>
                    <span class="btn-nav disabled" style="padding: 8px 16px; background: #1a1a24; color: #666; border-radius: 4px;">&larr; Anterior</span>
                <?php endif; ?>

                <?php if ($cap_siguiente): ?>
                    <a href="leer_capitulo.php?id=<?php echo $cap_siguiente; ?>" class="btn-nav" style="padding: 8px 16px; background: #2b2b36; color: #fff; text-decoration: none; border-radius: 4px;">Siguiente &rarr;</a>
                <?php else: ?>
                    <span class="btn-nav disabled" style="padding: 8px 16px; background: #1a1a24; color: #666; border-radius: 4px;">Siguiente &rarr;</span>
                <?php endif; ?>
            </div>

            <!-- Sección de Comentarios -->
            <section class="seccion-comentarios">
                <h3>Comentarios</h3>
                <div class="comentarios-caja" style="margin-top: 15px;">
                    <textarea class="comentario-input" placeholder="Escribe un comentario..." style="width: 100%; height: 80px; padding: 10px; border-radius: 6px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid #3b3129; margin-bottom: 10px;"></textarea>
                    <button class="btn-comentar" style="padding: 8px 18px; background: #ff6b35; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Publicar Comentario</button>
                </div>
                <div id="lista-comentarios">
                    <!-- Lista de comentarios -->
                </div>
            </section>

        </main>
    </div>

    <!-- Script para renderizar Markdown con soporte para Notas al Pie -->
    <script>
        const markdownTexto = <?php echo json_encode($capitulo['Contenido_markdown']); ?>;

        function procesarNotasPie(texto) {
            if (!texto) return '';

            // 1. Reemplazar DEFINICIONES de notas al pie [^id]: texto
            let md = texto.replace(
                /^\[\^([a-zA-Z0-9_-]+)\]:\s*(.*)$/gm,
                '<div class="footnote-item" id="fn-$1" style="margin-top: 10px; font-size: 0.9em; opacity: 0.8;"><strong>[$1]</strong> $2 <a href="#fnref-$1" onclick="event.preventDefault(); document.getElementById(\'fnref-$1\')?.scrollIntoView({behavior: \'smooth\'});">&hookleftarrow;</a></div>'
            );

            // 2. Reemplazar REFERENCIAS [^id]
            md = md.replace(
                /\[\^([a-zA-Z0-9_-]+)\](?!\:)/g,
                '<sup class="footnote-ref"><a href="#fn-$1" id="fnref-$1" style="color: #ff6b35; font-weight: bold; text-decoration: none;" onclick="event.preventDefault(); document.getElementById(\'fn-$1\')?.scrollIntoView({behavior: \'smooth\'});">[$1]</a></sup>'
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

    <?php include 'footer.php'; ?>
</body>
</html>