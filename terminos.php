<?php
// terminos.php
session_start();
require_once 'HHH/Conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Términos y Condiciones - FoxNovel</title>
    <link rel="stylesheet" href="Style.css"> <!-- Ajusta a tu CSS -->
</head>
<body class="bg-dark text-light">
<?php include 'Header.php' ?>
<div class="container my-5">
    <h1>Términos y Condiciones de Uso</h1>
    <hr class="border-secondary">
    
    <p>Bienvenido a FoxNovel. Al acceder a nuestro sitio web, aceptas cumplir con los siguientes términos y condiciones:</p>
    
    <h3>1. Uso de la Plataforma</h3>
    <p>FoxNovel es una plataforma para la lectura e interacción con historias en formato web novel. Queda prohibido el uso de bots o raspadores de datos para extraer el contenido publicado.</p>
    
    <h3>2. Cuentas de Usuario</h3>
    <p>Eres responsable de mantener la confidencialidad de tu cuenta y contraseña. Nos reservamos el derecho de suspender cuentas que violen nuestras normas de convivencia o derechos de autor.</p>
    
    <h3>3. Modificaciones</h3>
    <p>Podemos actualizar estos términos en cualquier momento. Te recomendamos revisar esta página periódicamente.</p>
</div>
<?php include 'footer.php' ?>
</body>
</html>