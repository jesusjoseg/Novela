<?php
session_start();

// 1. Incluir la nueva conexión centralizada a Supabase (PDO)
require_once 'HHH/Conexion.php';

try {
    // 2. Consulta para obtener las novelas ordenadas por ID descendente
    $stmt = $conexion->prepare("SELECT id, Titulo, Descripcion, Genero, Portada, link FROM novela ORDER BY id DESC");
    $stmt->execute();
    
    // Obtenemos todos los resultados en un arreglo asociativo
    $novelas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Asignamos la primera novela como destacada (si existen novelas)
    $destacada = !empty($novelas) ? $novelas[0] : null;

} catch (PDOException $e) {
    // En caso de error de BD, inicializamos un arreglo vacío para evitar romper el HTML
    $novelas = [];
    $destacada = null;
    error_log("Error en index.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lectura NOVELA - Foxnovel</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
       <?php if ($destacada): ?>
        <section class="hero-banner"> 
            <img class="hero-img" src="<?php echo htmlspecialchars($destacada['Portada'], ENT_QUOTES, 'UTF-8'); ?>" alt="Destacado">
            <div class="hero-content">
                <span id="cambio">Novela En Tendencias</span>
                <h1><?php echo htmlspecialchars($destacada['Titulo'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <p><?php echo htmlspecialchars($destacada['Descripcion'], ENT_QUOTES, 'UTF-8'); ?></p>
                <a href="<?php echo htmlspecialchars($destacada['link'], ENT_QUOTES, 'UTF-8'); ?>" class="btn-leer">Empezar a Leer</a>
            </div>
        </section>
        <?php endif; ?>

        <div class="main-layout">
            <main class="content-area">
                <h2>Catálogo de Traducciones</h2>
                <div class="novelas-grid">
                    <?php if (!empty($novelas)): ?>
                        <?php foreach ($novelas as $Novela): ?>
                            <?php if (empty($Novela['Titulo'])) continue; ?>
                            <a href="<?php echo htmlspecialchars($Novela['link'], ENT_QUOTES, 'UTF-8'); ?>" class="novela-card">
                                <img src="<?php echo htmlspecialchars($Novela['Portada'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($Novela['Titulo'], ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="novela-info">
                                    <span class="novela-titulo"><?php echo htmlspecialchars($Novela['Titulo'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <small class="novela-genero"><?php echo htmlspecialchars($Novela['Genero'], ENT_QUOTES, 'UTF-8'); ?></small>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No hay novelas disponibles por el momento.</p>
                    <?php endif; ?>
                </div>
            </main>

            <aside class="sidebar-area">
                <h2>Más Populares</h2>
                <div class="top-list">
                    <?php if (!empty($novelas)): ?>
                        <?php 
                        $ranking = 1;
                        foreach ($novelas as $Novela): 
                            if (empty($Novela['Titulo'])) continue;
                        ?>
                            <a href="<?php echo htmlspecialchars($Novela['link'], ENT_QUOTES, 'UTF-8'); ?>" class="top-item">
                                <div class="top-rank">#<?php echo $ranking++; ?></div>
                                <img class="top-img" src="<?php echo htmlspecialchars($Novela['Portada'], ENT_QUOTES, 'UTF-8'); ?>" alt="">
                                <div style="overflow: hidden;">
                                    <div style="font-size:13px;font-weight:bold;white-space:nowrap;overflow: hidden;"><?php echo htmlspecialchars($Novela['Titulo'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <small style="font-size:11px;"><?php echo htmlspecialchars($Novela['Genero'], ENT_QUOTES, 'UTF-8'); ?></small>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>