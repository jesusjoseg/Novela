<?php
session_start();
require_once 'HHH/Conexion.php';

// 1. Configuración de paginación
$limite = 30; // Mostrar 30 capítulos por página
$pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina_actual - 1) * $limite;

// 2. Consulta de la lista de capítulos con offset y limit mediante Supabase REST
$resCapitulos = supabase_request("capitulos?select=id,Capitulo,Titulo,fecha_Publicacion,novela_id,novela(id,Titulo,Portada)&order=fecha_Publicacion.desc,id.desc&limit={$limite}&offset={$offset}");

$ultimos_capitulos = (is_array($resCapitulos) && !isset($resCapitulos['error'])) ? $resCapitulos : [];

// 3. Rango de botones de paginación (de la página 1 a la 20)
$total_paginas_mostrar = 20; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Últimos Capítulos Subidos - Foxnovel</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php include 'Header.php'; ?>

    <div class="container" style="padding-top: 20px; padding-bottom: 40px;">
        <h1 class="seccion-titulo" style="margin-bottom: 25px;">Últimos Capítulos Subidos</h1>
        <?php include 'anuncio.php'; ?>
        <div class="grid-capitulos" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
            <?php if (!empty($ultimos_capitulos)): ?>
                <?php foreach ($ultimos_capitulos as $item): 
                    $datosNovela = $item['novela'] ?? [];
                    $novelaId = $datosNovela['id'] ?? $item['novela_id'];
                    $novelaTitulo = $datosNovela['Titulo'] ?? 'Novela';
                    $novelaPortada = $datosNovela['Portada'] ?? '';
                    $fechaPublicacion = isset($item['fecha_Publicacion']) ? date('d/m/Y', strtotime($item['fecha_Publicacion'])) : '';
                ?>
                    <div class="card-capitulo" style="display: flex; flex-direction: column; align-items: center; background: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px; text-align: center; height: 100%;">
    
    <!-- Portada Arriba -->
    <a href="ver_novela.php?id=<?php echo $novelaId; ?>">
        <img src="<?php echo htmlspecialchars($novelaPortada, ENT_QUOTES, 'UTF-8'); ?>" alt="" style="width: 80px; height: 110px; object-fit: cover; border-radius: 6px; margin-bottom: 10px;">
    </a>

    <!-- Información Abajo -->
    <div style="width: 100%;">
        <!-- Título de Novela (Naranja): Se ajusta en varias líneas sin cortarse -->
        <strong style="display: block; color: #ff6b35; font-size: 14px; line-height: 1.3; word-break: break-word; overflow-wrap: break-word; margin-bottom: 8px;">
            <?php echo htmlspecialchars($novelaTitulo, ENT_QUOTES, 'UTF-8'); ?>
        </strong>

        <!-- Título del Capítulo (Azul): Visible completo en varias líneas -->
        <a href="leer_capitulo.php?id=<?php echo $item['id']; ?>" style="color: #4ea8de; font-size: 13px; text-decoration: none; display: block; word-break: break-word; overflow-wrap: break-word; line-height: 1.4;">
            Capítulo <?php echo $item['Capitulo']; ?>: <?php echo htmlspecialchars($item['Titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
        </a>

        <small style="font-size: 11px; opacity: 0.6; display: block; margin-top: 6px;">
            <?php echo $fechaPublicacion; ?>
        </small>
    </div>
</div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay capítulos recientes publicados.</p>
            <?php endif; ?>
        </div>

        <!-- SECCIÓN DE PAGINACIÓN (1 a 20) -->
        <div class="paginacion" style="display: flex; justify-content: center; gap: 8px; margin-top: 35px; flex-wrap: wrap;">
            <?php if ($pagina_actual > 1): ?>
                <a href="?pagina=<?php echo $pagina_actual - 1; ?>" class="btn-pagina" style="padding: 8px 12px; background: #2b2b36; color: #fff; text-decoration: none; border-radius: 4px;">&laquo; Anterior</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_paginas_mostrar; $i++): ?>
                <a href="?pagina=<?php echo $i; ?>" 
                   class="btn-pagina <?php echo ($i === $pagina_actual) ? 'activa' : ''; ?>" 
                   style="padding: 8px 14px; background: <?php echo ($i === $pagina_actual) ? '#ff6b35' : '#2b2b36'; ?>; color: #fff; text-decoration: none; border-radius: 4px; font-weight: bold;">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($pagina_actual < $total_paginas_mostrar): ?>
                <a href="?pagina=<?php echo $pagina_actual + 1; ?>" class="btn-pagina" style="padding: 8px 12px; background: #2b2b36; color: #fff; text-decoration: none; border-radius: 4px;">Siguiente &raquo;</a>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>