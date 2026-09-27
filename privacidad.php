<?php
// privacidad.php
session_start();
require_once 'HHH/Conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
    <title>Política de Privacidad - FoxNovel</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body class="bg-dark text-light">
<?php include 'Header.php' ?>
<div class="container my-5">
    <h1>Política de Privacidad</h1>
    <hr class="border-secondary">
    
    <h3>1. Información que Recopilamos</h3>
    <p>Guardamos información básica como tu nombre de usuario, correo electrónico y tus preferencias de lectura (favoritos y comentarios) para mejorar tu experiencia en la plataforma.</p>
    
    <h3>2. Protección de Datos</h3>
    <p>No vendemos ni compartimos tus datos personales con terceros. Tu contraseña está encriptada en nuestras bases de datos de forma segura.</p>
    
    <h3>3. Cookies</h3>
    <p>Utilizamos sesiones PHP estándar para mantener tu sesión iniciada mientras navegas por la web.</p>
</div>
<?php include 'footer.php' ?>
</body>
</html>