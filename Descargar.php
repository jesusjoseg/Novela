<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Meta Etiquetas SEO -->
    <title>Descargar FoxNovel APK v1.0.0 para Android - App Oficial Gratis</title>
    <meta name="description" content="Descarga la aplicación oficial de FoxNovel APK para Android. Lee novelas ligeras y web novels sin interrupciones desde tu móvil.">
    <meta name="keywords" content="descargar foxnovel apk, app de novelas ligeras, foxnovel android, apk gratis novelas">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://foxnovel.ct.ws/descargar_apk.php">

    <!-- Open Graph -->
    <meta property="og:title" content="Descargar FoxNovel APK Oficial">
    <meta property="og:description" content="Lleva tus novelas favoritas a todas partes descargando la app de FoxNovel para Android.">
    <meta property="og:image" content="https://foxnovel.ct.ws/icon.png">
    <meta property="og:url" content="https://foxnovel.ct.ws/descargar_apk.php">

    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
    <link rel="stylesheet" href="Style.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #121212;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px 10px;
        }

        .download-container {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }

        .card {
            background-color: #1e1e1e;
            border-radius: 16px;
            padding: 24px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
            text-align: center;
            border: 1px solid #2a2a2a;
        }

        .app-icon {
            width: 90px;
            height: 90px;
            border-radius: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
            object-fit: cover;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 5px;
        }

        .version {
            color: #ff6b35;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .info-box {
            background-color: #262626;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: left;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #333;
            font-size: 14px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #a0a0a0;
        }

        .info-val {
            color: #ffffff;
            font-weight: 500;
        }

        .ad-banner {
            margin: 15px 0;
            min-height: 60px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .btn-download {
            display: block;
            width: 100%;
            padding: 14px;
            background-color: #333333;
            color: #888888;
            font-weight: bold;
            font-size: 16px;
            text-decoration: none;
            border-radius: 10px;
            cursor: not-allowed;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-download.active {
            background-color: #ff6b35;
            color: #ffffff;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(255, 107, 53, 0.4);
        }

        .btn-download.active:hover {
            background-color: #e05a2b;
        }

        .subtext {
            color: #777;
            font-size: 12px;
            margin-top: 12px;
            line-height: 1.4;
        }
    </style>
</head>
<body>

    <!-- Encabezado / Navbar -->
    <?php include 'Header.php'; ?>

    <main class="download-container">
        <div class="card">
            <!-- Logo de la Aplicación -->
            <img src="icon.png" alt="FoxNovel Logo" class="app-icon">
            <h1 class="title">FoxNovel</h1>
            <p class="version">Versión Oficial v1.0.0</p>

            <!-- Información Técnica del Archivo -->
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Archivo:</span>
                    <span class="info-val">FoxNovel_v1.0.0.apk</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Requisito:</span>
                    <span class="info-val">Android 6.0 o superior</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Seguridad:</span>
                    <span class="info-val" style="color: #4caf50;">✓ Escaneado (Seguro)</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tamaño:</span>
                    <span class="info-val">109 MB</span>
                </div>
            </div>

            <!-- Espacio reservado para Banners limpios (Google AdSense o Banners estáticos) -->
            <div class="ad-banner">
                <!-- Aquí puedes colocar tus banners sin redirecciones automáticas -->
            </div>

            <!-- Botón con Temporizador -->
            <button id="downloadBtn" class="btn-download" disabled>
                Esperar <span id="timer">10</span> segundos...
            </button>

            <p class="subtext">Descarga directa y segura desde nuestros servidores en MediaFire.</p>
        </div>
    </main>

    <script>
        let seconds = 10;
        const timerElement = document.getElementById('timer');
        const downloadBtn = document.getElementById('downloadBtn');
        
        // Reemplaza esta URL con tu enlace directo de MediaFire
        const downloadUrl = "https://www.mediafire.com/file/qjrdpy90lqvs1nd/FoxNovel_V1.0.0.apk/file";

        const countdown = setInterval(() => {
            seconds--;
            if (timerElement) {
                timerElement.textContent = seconds;
            }

            if (seconds <= 0) {
                clearInterval(countdown);
                downloadBtn.classList.add('active');
                downloadBtn.removeAttribute('disabled');
                downloadBtn.innerHTML = "📥 Descargar APK desde MediaFire";
                
                // Redirección directa al hacer clic
                downloadBtn.onclick = function() {
                    window.open(downloadUrl, '_blank');
                };
            }
        }, 1000);
    </script>
</body>
</html>