<?php
session_start();
require_once __DIR__ . '/HHH/Conexion.php';
$creador_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$creador = null;
$novelas = [];
$error_mensaje = "";
if ($creador_id > 0) {
    $resUser = supabase_request('usuario?select=id,nombre,username,link_donacion,avatar&id=eq.' . $creador_id, 'GET');
    if (!isset($resUser['error']) && is_array($resUser) && count($resUser) > 0) {
        $creador = $resUser[0];
    } else {
        $error_mensaje = "No se encronto el pefil del creador.";
    }
    $resnovela = supabase_request('novela?select=id,Titulo,Portada,Genero&usuario_id=eq.' . $creador_id . '&order=id.desc', 'GET');
    if (!isset($resnovela['error']) && is_array($resnovela)) {
        $novelas = $resnovela;
    }
} else {
    $error_mensaje = "no se encroto ID por nigun lado";
}
$link_donacion = $creador['link_donacion'] ?? '';
if (!empty($link_donacion) && !preg_match("~^(?:f|ht)tps?://~i", $link_donacion)) {
    $link_donacion = "https://" . $link_donacion;
}
$palabra = explode(' ', preg_replace('/\s+/', ' ', trim($creador['nombre'])));
$inicial='';
if(isset($palabra[0])){
    $inicial.= mb_substr($palabra[0],0,1,'UTF-8');
}
if(isset($palabra[1])){
    $inicial .= mb_substr($palabra[1],0,1,'UTF-8');
}
$textoinicia=urlencode(strtoupper($inicial));
$avata2 =!empty($creador['avatar']) ? $creador['avatar']: 'https://placehold.co/80/ff6b35/FFFFFF?text=' .$textoinicia;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta name="author" content="">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pefil de <?php echo $creador ? htmlspecialchars($creador['nombre']) : 'Creador' ?></title>
    <link rel="stylesheet" href="Style.css">
    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
</head>

<body>
    <?php include 'Header.php' ?>
    <div class="main-container">
        <?php  include 'anuncio.php'?>
        <?php if($creador): ?>
            <div class="card-creador">
                <img src="<?php echo htmlspecialchars(!empty($creador['avatar'])?$creador['avatar']:$avata2) ?>" class="avatar-creador"/>
                <h1 class="nombre-creador"><?php echo htmlspecialchars($creador['nombre']) ?></h1>
                <?php if(!empty($creador['username'])): ?>
                    <span class="username-creador">@<?php echo htmlspecialchars($creador['username']); ?></span>
                <?php endif; ?>
                <div class="badge-creador">
                    <span class="badge-text">AUTHOR / CREADOR OFICIAL</span>
                </div>
            </div>
            <?php  if (!empty($link_donacion)): ?>
                <div class="donaciones-card">
                    <div class="donaciones-titulo">
                        <p>apoya directamente al creador</p>
                    </div>
                    <div class="donaciones-sub">
                        <p>Las contribuciones van directamente al autor <strong style="color: #ff6b35;">sin intermediarios</strong>.</p>
                    </div>
                    <a href="<?php echo htmlspecialchars($link_donacion) ?>" target="_blank" rel="noopener noreferrer" class="btn-donar">Apoyar/ Donar al creador</a>
                </div>
            <?php else: ?>
                <div class="donaciones-card">
                    <div class="donaciones-titulo">
                        <h3>Sin fuente de donación</h3>
                    </div>
                    <div class="donaciones-sub">
                        <p style="color:#ccc">El creador no ha registrado una fuente de donación.</p>
                    </div>
                </div>
            <?php endif; ?>
            <div class="section-title">
                <h3>Obras publicadas</h3>
            </div>
            <?php if (count($novelas)>0): ?>
                <div class="grid-novelas">
                    <?php foreach($novelas as $novela): ?>
                        <a href="ver_novela.php?id=<?php echo $novela['id']; ?>" class="card-novela">
                            <img src="<?php echo htmlspecialchars($novela['Portada']); ?>" alt="Portada de <?php echo htmlspecialchars($novela['Titulo']) ;?>" class="img-portada">
                            <div class="info-novela">
                                <h2 class="titulo-novela"><?php echo htmlspecialchars($novela['Titulo']); ?></h2>
                                <span class="genero-novela"><?php echo htmlspecialchars($novela['Genero']); ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-card">
                    <p>Este creador no tiene obras públicas por el momento.</p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="center-error">
                <p style="font-size: 16px;"><?php echo htmlspecialchars($error_mensaje) ?></p>
                <a href="index.php">Volver a Inicio</a>
            </div>
        <?php endif; ?>
    </div>
    <?php include 'footer.php' ?>
</body>

</html>