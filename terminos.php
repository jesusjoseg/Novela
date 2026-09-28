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
</head>
<body class="bg-dark text-light">
<?php include 'Header.php' ?>
<div class="container my-5" style="line-height: 1.8; text-align: justify;">
    <h1 style="color: #ff6b35;">Términos y Condiciones de Uso Global</h1>
    <hr class="border-secondary">
    
    <p>El presente documento establece las cláusulas legales y normativas que regulan el acceso, navegación y uso del sitio web y la aplicación móvil de FoxNovel. Al registrar una cuenta o navegar por la biblioteca, el usuario acepta de forma implícita los presentes términos en su totalidad.</p>
    
    <h3>1. Naturaleza y Enfoque del Catálogo Técnico</h3>
    <p>FoxNovel es un ecosistema digital especializado en la catalogación, indexación y traducción técnica de novelas web procedentes de <strong>Japón, China y Corea</strong>. Toda obra asiática integrada en el sistema es subida y controlada <strong>exclusivamente por la Administración de FoxNovel</strong>. Queda estrictamente prohibido el uso de herramientas automatizadas, bots o raspadores de datos (scrapers) para minar, copiar o redistribuir el contenido de los capítulos publicados en nuestras interfaces.</p>
    
    <h3>2. Reglas Estrictas para Creadores Externos y Sanciones</h3>
    <p>Los usuarios registrados tienen la opción de solicitar la aprobación del rol de "Creador" o "Autor" a través del Dashboard técnico. Este rol está destinado **única y exclusivamente para la publicación de novelas de autoría propia y contenido original inédito**. 
    Los creadores externos tienen terminantemente prohibido subir novelas de origen japonés, coreano o chino, material protegido por derechos de terceros o copias raspadas de otras webs. Cualquier infracción a esta directiva resultará en la <strong>eliminación inmediata del contenido infractor de la base de datos de Supabase y en la suspensión o veto técnico definitivo de la cuenta del usuario responsable</strong>.</p>
    
    <h3>3. Sistema de Acceso Freemium y Restricciones VIP</h3>
    <p>La plataforma implementa un modelo de visualización híbrido o Freemium controlado de la siguiente manera:
    Los primeros <strong>15 capítulos de cualquier novela integrada en el catálogo son completamente gratuitos</strong> y abiertos para todos los lectores. A partir del capítulo 16 en adelante, el contenido se considera VIP (Acceso Anticipado). Para acceder a estos capítulos de forma inmediata, el usuario debe poseer un rol con privilegios premium o, en su defecto, **esperar un período de 15 días naturales** contados a partir de la fecha exacta de su publicación en el sistema para que se libere de forma automática al catálogo gratuito.
    *   *Nota de Transparencia:* Los mecanismos de cobro automatizados, tarifas fijas y pasarelas de pago recurrentes se encuentran actualmente en **fase de planeación y desarrollo arquitectónico**. FoxNovel no realiza cobros forzados ni solicita datos bancarios dentro del sistema hasta que esta sección sea actualizada oficialmente.</p>
    
    <h3>4. Donaciones y Financiamiento Externo Seguro</h3>
    <p>El soporte del servidor se financia mediante publicidad y las contribuciones voluntarias de la comunidad de lectores. El sistema técnico de donaciones se gestiona de forma externa a través de tres plataformas seguras e independientes: <strong>PayPal, Patreon y Ko-fi</strong>. FoxNovel no almacena tarjetas de crédito ni gestiona datos bancarios en su base de datos.</p>

    <h3>5. Transparencia en el Desarrollo y Autoría del Código</h3>
    <p>En cumplimiento con las buenas prácticas de honestidad en el software, declaramos abiertamente que se utilizaron herramientas de Inteligencia Artificial (IA) como asistentes técnicos de apoyo para idear esquemas visuales, optimizar las hojas de estilo unificadas de <code>Style.css</code> y estructurar la lógica de integración con Supabase. Sin embargo, para blindar el entorno contra vulnerabilidades, **el 100% del código de programación (PHP, JavaScript y React Native) ha sido escrito carácter por carácter por el desarrollador principal**, asegurando el control absoluto del software.</p>
</div>
<?php include 'footer.php' ?>
</body>
</html>
