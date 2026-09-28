<?php
// privacidad.php
session_start();
require_once 'HHH/Conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
    <title>Política de Privacidad Integral - FoxNovel</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body class="bg-dark text-light">
<?php include 'Header.php' ?>
<div class="container my-5" style="line-height: 1.8; text-align: justify;">
    <h1 style="color: #ff6b35;">Política de Privacidad y Protección de Datos</h1>
    <hr class="border-secondary">
    
    <p>La presente Política de Privacidad describe de manera detallada y transparente los criterios de recopilación, almacenamiento, tratamiento y seguridad de los datos personales que usted proporciona al registrarse en el ecosistema digital de FoxNovel, que abarca tanto el sitio web como la aplicación móvil nativa.</p>

    <h3>1. Datos de Registro Obligatorios</h3>
    <p>Para la creación de una cuenta de usuario en nuestro sistema, recopilamos única y estrictamente los datos esenciales para la autenticación e identificación en la base de datos: <strong>Nombre, Apellido, Dirección de Correo Electrónico y Contraseña</strong>. No solicitamos, bajo ninguna circunstancia, datos biométricos, números de identificación oficial ni información financiera sensible.</p>
    
    <h3>2. Almacenamiento e Infraestructura Técnica (Supabase)</h3>
    <p>Toda la información recopilada se almacena y procesa de forma descentralizada utilizando la infraestructura en la nube de <strong>Supabase</strong>. 
    Las contraseñas de los usuarios no son legibles por el personal técnico de FoxNovel; son transformadas de forma irreversible mediante algoritmos de encriptación hash avanzados (Bcrypt) directamente en los servidores de Supabase antes de ser guardadas. Las subidas de avatares de perfil y portadas se realizan de forma binaria al servicio de Supabase Storage en buckets aislados y protegidos.</p>
    
    <h3>3. Preferencias de Lectura y Datos de Actividad</h3>
    <p>Con el único propósito de proveer las funciones interactivas que usted utiliza, registramos en la base de datos sus marcadores de novelas favoritas, los comentarios públicos que decide redactar, los ajustes personalizados del lector (tamaño y tipo de fuente) y el progreso de lectura asíncrono. Este último registra el último capítulo visualizado para que pueda reanudar su lectura en cualquier dispositivo de forma fluida.</p>

    <h3>4. Política Estricta de "No Cookies" de Rastreo</h3>
    <p>FoxNovel mantiene un compromiso ético con la privacidad de sus usuarios: <strong>no utilizamos cookies de rastreo publicitario, píxeles espía ni scripts comerciales para perfilar su comportamiento</strong> o rastrear sus hábitos de navegación en otros sitios web. 
    En la plataforma web empleamos exclusivamente sesiones técnicas PHP estándar que expiran al cerrar el navegador. En la aplicación móvil, los tokens de autenticación se almacenan de manera local y aislada en el dispositivo mediante almacenamiento persistente (AsyncStorage) con el único fin de mantener su perfil abierto.</p>

    <h3>5. Redes Publicitarias de Terceros (Adsterra)</h3>
    <p>El financiamiento técnico y el coste de los servidores de la plataforma se sostienen mediante la inclusión de anuncios publicitarios proveídos por la red externa de <strong>Adsterra</strong>. Estos anuncios se inyectan a través de scripts y contenedores iframe totalmente independientes del código interno de la web. FoxNovel no comparte su nombre, correo ni datos de perfil con Adsterra. Cualquier interacción que realice con dichos banners publicitarios queda sujeta de forma exclusiva a las políticas de privacidad particulares de Adsterra.</p>
</div>
<?php include 'footer.php' ?>
</body>
</html>
