<?php
session_start();
include 'HHH/Conexion.php';
$query_novelas = "SELECT id, Titulo,Descripcion,Genero,Portada,link FROM novela ORDER BY id DESC";
$resultado_novela =$coon->query($query_novelas);

$destacada = null;
if($resultado_novela && $resultado_novela->num_rows>0){
    $destacada =$resultado_novela->fetch_assoc();
}

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
       <?php if ($destacada):?>
        <section class="hero-banner"> 
            <img class="hero-img" src="<?php echo htmlspecialchars($destacada['Portada']);?>"  alt="Destacado">
            <div class="hero-content">
                <span id="cambio" >Novela En Tendecias</span>
                <h1><?php echo htmlspecialchars($destacada['Titulo']) ;?></h1>
                <p><?php echo htmlspecialchars($destacada['Descripcion']) ;?></p>
                <a href="<?php echo $destacada['link'];?>" class="btn-leer">Empezar a Leer</a>
            </div>
        </section>
        <?php endif;?>
        <div class="main-layout">
            <main class="content-area">
                <h2>Catalago de Traducciones</h2>
                <div class="novelas-grid">
                    <?php  if ($resultado_novela && $resultado_novela->num_rows>0):
                        $resultado_novela->data_seek(0);
                        while ($Novela = $resultado_novela->fetch_assoc()):
                            if (empty($Novela['Titulo']))continue;?>
                        <a href="<?php echo htmlspecialchars($Novela['link']) ;?>" class="novela-card">
                            <img src="<?php echo htmlspecialchars($Novela['Portada']);?>" alt="">
                            <div class="novela-info">
                                <span class="novela-titulo"><?php echo htmlspecialchars($Novela['Titulo']) ;?></span>
                                <small class="novela-genero"><?php echo htmlspecialchars($Novela['Genero']) ;?></small>
                            </div>
                        </a>
                    <?php endwhile;
                    endif;?>
                </div>
            </main>
            <aside class="sidebar-area">
                <h2>Mas Populares</h2>
                <div class="top-list">
                    <?php
                    if($resultado_novela && $resultado_novela->num_rows>0):
                        $ranking=1;
                        $resultado_novela->data_seek(0);
                        while($Novela=$resultado_novela->fetch_assoc()):
                            if (empty($Novela['Titulo']))continue;
                    ?>
                <a href="<?php echo htmlspecialchars($Novela['link']) ;?>" class="top-item">
                    <div class="top-rank">#<?php echo $ranking++;?></div>
                    <img class="top-img" src="<?php echo htmlspecialchars($Novela['Portada']);?>" alt="">
                    <div style="overflow: hidden;">
                        <div style="font-size:13px;font-weight:bold;white-space:nowrap;overflow: hidden;"><?php echo htmlspecialchars($Novela['Titulo']) ;?></div>
                        <small style="font-size:11px;"><?php echo htmlspecialchars($Novela['Genero']) ;?></small>
                    </div>
                </a>
                <?php endwhile;
                endif;?>
                </div>
            </aside>
        </div>
    </div>
    <?php include'footer.php'?>
</body>
</html>