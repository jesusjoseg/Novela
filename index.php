<?php
session_start();
include 'NovelaData.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lectura NOVELA</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <?php include 'header.php'?>
    <div class="container">
       <?php if (isset($Novelas[1])):?>
        <section class="hero-banner"> 
            <img class="hero-img" src="<?php echo $Novelas[1]['Portada'];?>"  alt="Destacado">
            <div class="hero-content">
                <span id="cambio" >Novela En Tendecias</span>
                <h1><?php echo htmlspecialchars($Novelas[1]['Titulo']) ;?></h1>
                <p><?php echo htmlspecialchars($Novelas[1]['Descricion']) ;?></p>
                <a href="<?php echo $Novelas[1]['Link'];?>" class="btn-leer">Empezar a Leer</a>
            </div>
        </section>
        <?php endif;?>
        <div class="main-layout">
            <main class="content-area">
                <h2>Catalago de Traducciones</h2>
                <div class="novelas-grid">
                    <?php foreach($Novelas as $Novela):?>
                        <?php if (empty($Novela['Titulo']))continue;?>
                        <a href="<?php echo $Novela['Link'];?>" class="novela-card">
                            <img src="<?php echo $Novela['Portada'];?>" alt="">
                            <small><?php echo htmlspecialchars($Novela['Genero']) ;?></small>
                        </a>
                    <?php endforeach;?>
                </div>
            </main>
            <aside class="sidebar-area">
                <h2>Mas Oulares</h2>
                <div class="to-list">
                    <?php
                    $ranking=1;
                    foreach($Novelas as $Novela):
                        if (empty($Novela['Titulo'])) continue;
                    ?>
                <a href="<?php echo $Novela['Link'];?>" class="to-item">
                    <div class="to-rank">#<?php echo $ranking++;?></div>
                    <img class="to-img" src="<?php echo $Novela['Portada'];?>" alt="">
                    <div style="overflow: hidden;"></div>
                    <div style="font-size:13px;font-weight:bold;white-space:nowrap;overflow: hidden;"><?php echo htmlspecialchars($Novela['Titulo']) ;?></div>
                    <small style="font-size:11px;"><?php echo htmlspecialchars($Novela['Genero']) ;?></small>
                    </div>
                </a>
                </div>
                <?php endforeach;?>
            </aside>
        </div>
    </div>

</body>
</html>