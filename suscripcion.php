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
    <div class="container my-5 pato">
        <div class="pato-vip">
            <h1>⭐ Membresía FoxNovel</h1>
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
            <div class="principal-suc">
                <div class="beneficios">
                    <h3>Que Incluye tu Pase VIP</h3>
                    <ul>
                        <li><i class="fa-solid fa-bolt list-color1"></i> <strong> Lectura Anticiapadas</strong> Del cap. 16 en adelante sin esperar 15 días.</li>
                        <li><i class="fa-solid fa-ban list-color2"></i> <strong>Sin Interrupciones:</strong> Experiencia fluidad de lectura</li>
                        <li><i class="fa-solid fa-shield-cat list-color3"></i> <strong>Insignia Exclusiva:</strong> Distintivo VIP en tu pefil.</li>
                    </ul>
                </div>
                <div class="pago-vip">
                    <span>Recomendado</span>
                    <h2>Pase VIP Mensual</h2>
                    <div class="pago-precio">
                        <p>$85.00<small>MXN / mes</small></p>
                        
                    </div>
                    <p>\</p>
                    <?php if ($usuario_id): ?>
                        <a href="<?php echo $enlace_paypal; ?>" target="_blank" class="btn-paypal" ><i class="fa-brands fa-paypal"></i>
                    Pagar $85.00 MXN con Paypal</a>
                        <?php if (file_exists('src/imagen/Suscripcion de-qrcode.png')): ?>
                            <p></p>
                            <img src="src/image/Suscripcion de-qrcode.png" alt="">
                        <?php endif; ?>
                    <?php else: ?>
                        <p>Inicia sesión para asociar tu compra a tu cuenta.</p>
                        <a href="Login.php">Iniciar Sesión</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php include 'footer.php' ?>
</body>

</html>