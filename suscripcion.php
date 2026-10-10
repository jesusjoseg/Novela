<?php
session_start();
require_once 'HHH/Conexion.php';
$usuario_id = $_SESSION['usuario_id'] ?? $_SESSION['id'] ?? null;
$es_vip = false;
if ($usuario_id) {
    $resUser = supabase_request("usuario?select=es_premium&id=eq.{$usuario_id}");
    if (is_array($resUser) && !empty($resUser) && !isset($resUser['error'])) {
        $es_vip = !empty($resUser[0]['es_premium']);
    }
}
$enlace_paypal = "https://www.paypal.com/ncp/payment/LWUHKENE43YWN";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripcion VIP - FoxNovel</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <?php include 'Header.php'; ?>
    <div>
        <div>
            <h1>Membresia FoxNovel</h1>
            <p>Lee antes que nadie y apoya la plataforma</p>
        </div>
        <?php if ($es_vip): ?>
            <div>
                <i></i>
                <h2></h2>
                <p></p>
                <a href="index.php">Ir al Catálogo</a>
            </div>
        <?php else: ?>
            <div>
                <div>
                    <h3>Que incluye tu Pase VIP</h3>
                    <ul>
                        <li>Lectura Anticiapadas</li>
                        <li>Experiencia fluidad de lectura</li>
                        <li>Distintivo VIP en tu pefil.</li>
                    </ul>
                </div>
                <div>
                    <span></span>
                    <h2></h2>
                    <div>
                        <small></small>
                    </div>
                    <p></p>
                    <?php if ($usuario_id): ?>
                        <a href="<?php echo $enlace_paypal ?>"><i></i>
                    Pagar $85.00 MXN con Paypal</a>
                        <?php if (file_exists('')): ?>
                            <p></p>
                            <img src="" alt="">
                        <?php endif; ?>
                    <?php else: ?>
                        <p></p>
                        <a href=""></a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php include 'footer.php' ?>
</body>

</html>