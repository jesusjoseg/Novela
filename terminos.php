<?php
// terminos.php
session_start();
require_once 'HHH/Conexion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
    <title>Términos y Condiciones de Uso - FoxNovel</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-dark text-light">
    <?php include 'Header.php' ?>
    <div class="container my-5" style="line-height: 1.8; text-align: justify;">
        <h1 style="color: #ff6b35;">Términos y Condiciones de Uso Global</h1>
        <hr class="border-secondary">

        <p>El presente documento establece las cláusulas legales y normativas que regulan el acceso, navegación y uso del sitio web y la aplicación móvil de FoxNovel. Al registrar una cuenta, contratar una suscripción VIP o navegar por la biblioteca, el usuario acepta de forma implícita los presentes términos en su totalidad.</p>

        <h3>1. Naturaleza y Enfoque del Catálogo Técnico</h3>
        <p>FoxNovel es un ecosistema digital especializado en la catalogación, indexación y traducción técnica de novelas web procedentes de <strong>Japón, China y Corea</strong>. Toda obra asiática integrada en el sistema es subida y controlada <strong>exclusivamente por la Administración de FoxNovel</strong>. Queda estrictamente prohibido el uso de herramientas automatizadas, bots o raspadores de datos (scrapers) para minar, copiar o redistribuir el contenido de los capítulos publicados en nuestras interfaces.</p>

        <h3>2. Reglas Estrictas para Creadores Externos y Sanciones</h3>
        <p>Los usuarios registrados tienen la opción de solicitar la aprobación del rol de "Creador" o "Autor" a través del Dashboard técnico. Este rol está destinado <strong>única y exclusivamente para la publicación de novelas de autoría propia y contenido original inédito</strong>. Los creadores externos tienen terminantemente prohibido subir novelas de origen japonés, coreano o chino, material protegido por derechos de terceros o copias raspadas de otras webs. Cualquier infracción a esta directiva resultará en la <strong>eliminación inmediata del contenido infractor de la base de datos de Supabase y en la suspensión o veto técnico definitivo de la cuenta del usuario responsable</strong>.</p>

        <h3>3. Enlaces de Donación Propios de Creadores</h3>
        <p>Los usuarios que posean el rol verificado de "Creador" o "Autor" tienen permitida la inclusión opcional de sus propios enlaces y pasarelas de donación independientes (tales como PayPal, Ko-fi o Patreon) de forma exclusiva en sus respectivas páginas de perfil público (<em>PefilCreador.php</em>). FoxNovel actúa únicamente como un medio técnico de visualización de dichos enlaces y no asume ninguna responsabilidad, procesamiento, comisión ni gestión sobre las transacciones financieras directas que los lectores decidan realizar a los creadores de forma autónoma.</p>

        <h3>4. Sistema de Acceso Freemium, Membresía VIP y Política de Cancelación</h3>
        <p>La plataforma implementa un modelo de visualización híbrido (Freemium) regulado bajo las siguientes condiciones:</p>
        <ul style="margin-left: 20px; margin-bottom: 15px;">
            <li><strong>Catálogo Gratuito:</strong> Los primeros 15 capítulos de cualquier novela son completamente gratuitos y abiertos para todos los usuarios.</li>
            <li><strong>Acceso Anticipado VIP ($85.00 MXN / mes):</strong> A partir del capítulo 16 en adelante, el contenido se considera VIP (Acceso Anticipado). Los usuarios pueden desbloquear estos capítulos de forma inmediata adquiriendo un Pase VIP por una tarifa fija de <strong>$85.00 MXN mensuales</strong> procesada a través de la pasarela segura de <strong>PayPal</strong>. De lo contrario, dichos capítulos se liberarán de forma gratuita transcurridos 15 días naturales desde su fecha de publicación en el sistema.</li>
            <li><strong>Renovación y Cancelación:</strong> Los pagos de suscripción o contribuciones recurrentes gestionados mediante PayPal dependen directamente de los términos del proveedor. El usuario puede cancelar su membresía en cualquier momento desde su sección de perfil en FoxNovel o directamente en su cuenta de PayPal, conservando sus beneficios VIP hasta que concluya el periodo de 30 días previamente pagado.</li>
            <li><strong>Política de No Reembolso:</strong> Debido a que el acceso a los contenidos digitales de lectura es de entrega inmediata tras confirmarse el pago, las cuotas de suscripción procesadas no son reembolsables, salvo fallos técnicos imputables a la plataforma donde el servicio no haya sido provisto.</li>
        </ul>

        <h3>5. Donaciones y Financiamiento Externo Seguro de la Plataforma</h3>
        <p>El soporte del servidor y la traducción de obras se financian mediante publicidad, la venta del Pase VIP y las contribuciones voluntarias de la comunidad. Las transacciones financieras de la plataforma se realizan mediante pasarelas de pago certificadas: <strong>PayPal, Patreon y Ko-fi</strong>. FoxNovel no almacena números de tarjeta de crédito, débito ni credenciales bancarias sensibles en sus servidores ni en la base de datos de Supabase.</p>

        <h3>6. Transparencia en Banners Promocionales y Enlaces de Terceros</h3>
        <p>Con el fin de ofrecer una experiencia visual limpia y profesional dentro del sitio web y la aplicación móvil, algunos espacios publicitarios integran diseños e imágenes promocionales pertenecientes a la identidad gráfica de FoxNovel. No obstante, la acción interactiva o clic en dichos elementos puede ejecutar enlaces de redirección patrocinados (<em>Direct Links</em>) administrados por la red publicitaria <strong>Adsterra Network</strong>.</p>
        <p>Al presionar cualquier elemento gráfico identificado como contenido patrocinado o publicidad, el usuario reconoce y acepta explícitamente que la imagen mostrada sirve como recurso gráfico de maquetación, pero el destino final lo redirigirá a sitios web, productos o servicios de anunciantes externos ajenos al control de FoxNovel. FoxNovel no asume responsabilidad alguna sobre las ofertas, seguridad, contenidos o políticas de privacidad de dichos sitios web de terceros.</p>

        <h3>7. Enlaces de Donación Propios de Creadores:</h3>
        <p>Los usuarios con el rol verificado de "Creador" o "Autor" tienen permitida la inclusión opcional de sus enlaces de donación y contribución personal (tales como PayPal, Ko-fi o Patreon) de forma exclusiva en sus respectivas páginas de perfil público. FoxNovel actúa únicamente como un medio técnico de visualización de dichos enlaces y no asume ninguna responsabilidad, procesamiento, comisión ni gestión sobre las transacciones financieras directas que los lectores decidan realizar a los creadores de forma independiente.</p>


        <h3>8. Transparencia de Desarrollo y Código</h3>
        <p>FoxNovel fue ideado estéticamente con el apoyo técnico de Inteligencia Artificial para agilizar el diseño y las hojas de estilo unificadas de Style.css, pero declaramos transparentemente que el 100% del código de programación (PHP, JavaScript y React Native) ha sido escrito e integrado carácter por carácter por el desarrollador principal, asegurando el control absoluto y la seguridad del software.</p>
    </div>
    <?php include 'footer.php' ?>
</body>

</html>