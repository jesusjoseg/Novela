<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apoya a Foxnovel - Donaciones ❤️</title> 
    <link rel="stylesheet" href="Style.css">
    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php include 'Header.php' ?>
    <div class="container">
        <main class="donaciones-container">
            <div class="donaciones-header">
                <h1>Apoya  al Proyecto Foxnovel</h1>
                <p>Tu apoyo nos ayuda a mantener los servidore activos, traducir más capitulos y mejora la plataforma. ¡Cualquier aportación es de gran ayuda!</p>
            </div>
            <div class="donaciones-grid">
                <div class="donacion-card patreon">
                    <div class="donacion-icon">
                        <i class="fa-brands fa-patreon"></i>
                    </div>
                    <h3>Patreon</h3>
                    <p>Subcribete mensualmente para Obtener beneficios VIP, acceso anticipado a capitulo y roles especiales.</p>
                    <a href="http://www.patreon.com/Jegamania138" target="_blank" rel="noopener noreferrer"class="btn-donar patreon-btn">Unimer a patreon</a>
                </div>
                <div class="donacion-card kofi">
                    <div class="donacion-icon">
                        <i class="fa-solid fa-mug-hot"></i>
                    </div>
                    <h3>ko-fi</h3>
                    <p>Cómpranos un café de forma rápida sin subcripciones. Ideal para donaciones  puntules y directas.</p>
                    <a href="https://ko-fi.com/jesusjosegardea" target="_blank" rel="noopener noreferrer"class="btn-donar kofi-btn">Donar  en Ko-fi</a>
                </div>
                <div class="donacion-card paypal">
                    <div class="donacion-icon">
                        <i class="fa-brands fa-paypal"></i>
                    </div>
                    <h3>Paypal</h3>
                    <p>Realiza unadonación directa y segura con tu cuentra de payapl o tarjeta de débito/crédito.</p>
                    <a href="https://paypal.me/jjgg675" target="_blank" rel="noopener noreferrer"class="btn-donar paypal-btn">Donar en Paypal</a>
                </div>
            </div>
        </main>
        <?php include 'anuncio.php'; ?>
    </div>
    <?php include'footer.php'?>
</body>
</html>