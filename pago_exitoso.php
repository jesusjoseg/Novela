<?php
require_once 'HHH/Conexion.php';

$usuario_id = $_SESSION['usuario_id'] ?? $_SESSION['id'] ?? null;

if ($usuario_id) {
    // Calculamos la fecha actual y la fecha límite a 30 días en formato UTC/ISO o DATETIME de SQL
    $fecha_inicio = date('Y-m-d H:i:s');
    $fecha_fin = date('Y-m-d H:i:s', strtotime('+30 days'));

    // Realizamos la petición PATCH a Supabase actualizando estatus y los límites de las columnas
    $resUpdate = supabase_request("usuario?id=eq.{$usuario_id}", 'PATCH', [
        'es_premium' => true,
        'inicio_suscripcion' => $fecha_inicio,
        'fin_suscripcion' => $fecha_fin
    ]);

    if (!isset($resUpdate['error'])) {
        $_SESSION['es_vip'] = true;
        $_SESSION['usuario_vip'] = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Pago Exitoso! - FoxNovel</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
</head>
<body>
    <?php include 'Header.php'; ?>
    <div class="felicidades">
        <div class="feliz">🎉🎉🎉</div>
        <h1>¡Gracias por tu compra!</h1>
        <p>Tu pago ha sido procesado con éxito y tu <strong>Membresía VIP por 30 días</strong> ha sido activada en tu cuenta. Ya puedes disfrutar de acceso anticipado a todos los capítulos.</p>
        <a href="index.php">Ir a Leer Novelas</a>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>