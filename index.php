<?php
session_start();
require_once __DIR__ . '/HHH/Conexion.php';

// 1. Catálogo reciente (24 novelas)
$resNovelas = supabase_request('novela?select=id,Titulo,Descripcion,Genero,Portada,link&order=id.desc&limit=24');

if (is_array($resNovelas) && isset($resNovelas['error'])) {
    die('<h3 style="color:red; text-align:center;">Error en tabla Novela: ' . htmlspecialchars($resNovelas['message'] ?? 'Error desconocido') . '<br>Revisa las políticas RLS en Supabase.</h3>');
}

$novelas = is_array($resNovelas) ? $resNovelas : [];
$destacada = $novelas[0] ?? null;

// 2. Más populares (10 novelas)
$resPopulares = supabase_request('novela?select=id,Titulo,Genero,Portada,link&order=Visitas.desc&limit=10');
$populares = (is_array($resPopulares) && !isset($resPopulares['error'])) ? $resPopulares : [];

// 3. Últimos capítulos (Sintaxis limpia sin comillas dobles en la URL)
$resUltimosCapitulos = supabase_request('capitulos?select=id,Capitulo,Titulo,fecha_Publicacion,novela_id,novela(Titulo,Portada,link)&order=id.desc&limit=6');
$ultimasActualizaciones = (is_array($resUltimosCapitulos) && !isset($resUltimosCapitulos['error'])) ? $resUltimosCapitulos : [];
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
    <?php include 'Header.php'; ?>

    <div class="container">
        <?php if ($destacada): ?>
            <section class="hero-banner">
                <img class="hero-img" src="<?php echo htmlspecialchars($destacada['Portada'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="Destacado">
                <div class="hero-content">
                    <span id="cambio">Novela En Tendencias</span>
                    <h1><?php echo htmlspecialchars($destacada['Titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p><?php echo htmlspecialchars($destacada['Descripcion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    <a href="<?php echo htmlspecialchars($destacada['link'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="btn-leer">Empezar a Leer</a>
                </div>
            </section>
        <?php endif; ?>

       

        <div class="main-layout" style="margin-bottom: 30px;">
            <main class="content-area">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h2>Última Actualización</h2>
                    <a href="Actualizacion.php" class="btn-leer" style="padding: 6px 14px; font-size: 13px;">Ver todas las actualizaciones</a>
                </div>

                <div class="actualizaciones-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                    <?php if (!empty($ultimasActualizaciones)): ?>
                        <?php foreach ($ultimasActualizaciones as $cap):
                            $datosNovela = $cap['novela'] ?? [];
                            $tituloNovela = $datosNovela['Titulo'] ?? 'Novela';
                            $portada = $datosNovela['Portada'] ?? '';
                            $numCapitulo = $cap['Capitulo'] ?? '0';
                            $tituloCapitulo = $cap['Titulo'] ?? '';
                            $fecha = isset($cap['fecha_Publicacion']) ? date('d/m/Y', strtotime($cap['fecha_Publicacion'])) : '';
                        ?>
                            <div class="actualizacion-card" style="display: flex; background: rgba(255,255,255,0.05); padding: 10px; border-radius: 8px; gap: 12px; align-items: center;">
                                <img src="<?php echo htmlspecialchars($portada, ENT_QUOTES, 'UTF-8'); ?>" alt="" style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px;">
                                <div style="overflow: hidden; flex: 1;">
                                    <strong style="display: block; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($tituloNovela, ENT_QUOTES, 'UTF-8'); ?>
                                    </strong>
                                    <a href="leer_capitulo.php?id=<?php echo $cap['id']; ?>&novela_id=<?php echo $cap['novela_id']; ?>" style="color: #4ea8de; font-size: 13px; text-decoration: none; display: block; margin-top: 4px;">
                                        Capítulo <?php echo $numCapitulo; ?>: <?php echo htmlspecialchars($tituloCapitulo, ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                    <small style="font-size: 11px; opacity: 0.7; display: block; margin-top: 4px;"><?php echo $fecha; ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #aaa;">No hay capítulos recientes publicados.</p>
                    <?php endif; ?>
                </div>
            </main>
        </div>

        

        <div class="main-layout">
            <main class="content-area">
                <h2>Catálogo de Traducciones</h2>
                <div class="novelas-grid">
                    <?php if (!empty($novelas)): ?>
                        <?php foreach ($novelas as $Novela): ?>
                            <?php if (empty($Novela['Titulo'])) continue; ?>
                            <a href="<?php echo htmlspecialchars($Novela['link'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="novela-card">
                                <img src="<?php echo htmlspecialchars($Novela['Portada'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($Novela['Titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
                                <div class="novela-info">
                                    <span class="novela-titulo"><?php echo htmlspecialchars($Novela['Titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                    <small class="novela-genero"><?php echo htmlspecialchars($Novela['Genero'] ?? '', ENT_QUOTES, 'UTF-8'); ?></small>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #aaa;">No hay novelas disponibles por el momento.</p>
                    <?php endif; ?>
                </div>
            </main>

            <aside class="sidebar-area">
                <h2>Más Populares</h2>
                <div class="top-list">
                    <?php if (!empty($populares)): ?>
                        <?php
                        $ranking = 1;
                        foreach ($populares as $Novela):
                            if (empty($Novela['Titulo'])) continue;
                        ?>
                            <a href="<?php echo htmlspecialchars($Novela['link'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="top-item">
                                <div class="top-rank">#<?php echo $ranking++; ?></div>
                                <img class="top-img" src="<?php echo htmlspecialchars($Novela['Portada'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="" loading="lazy">
                                <div style="overflow: hidden;">
                                    <div style="font-size:13px;font-weight:bold;white-space:nowrap;overflow: hidden;"><?php echo htmlspecialchars($Novela['Titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                    <small style="font-size:11px;"><?php echo htmlspecialchars($Novela['Genero'] ?? '', ENT_QUOTES, 'UTF-8'); ?></small>
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