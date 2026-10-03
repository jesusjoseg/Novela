<?php
session_start();
require_once __DIR__ . '/HHH/Conexion.php';
$creador_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$creador = null;
$novela = [];
$error_mensaje = "";
if ($creador_id > 0) {
    $resUser = supabase_request('usuario?select=id,nombre,username,link_donacion,avatar&id=eq' . $creador_id, 'GET');
    if (!isset($resUser['error']) && is_array($resUser) && count($resUser) > 0) {
        $creador = $resUser[0];
    } else {
        $error_mensaje = "No se encronto el pefil del creador.";
    }
    $resnovela = supabase_request('novela?select=id,Titulo,Portada,Genero&usuario_id=eq.' . $creador_id . '&order=id.desc', 'GET');
    if (!isset($resnovela['error']) && is_array($resnovela)) {
        $novela = $resnovela;
    }
} else {
    $error_mensaje = "no se encroto ID por nigun lado";
}
$link_donacion = $creador['link_donacion'] ?? '';
if (!empty($link_donacion) && !preg_match("~^(?:f|ht)tps?://~i", $link_donacion)) {
    $link_donacion = "https://" . $link_donacion;
}
?>
<!DOCTYPE html>
<html lang="en">

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
        <?php  ?>
        <?php if($creador): ?>
            <div>
                <img src="<?php echo htmlspecialchars($creador['avatar']??'') ?>"
            </div>
        <?php endif; ?>
    </div>
    <?php include 'footer.php' ?>
</body>

</html>