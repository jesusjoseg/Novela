<?php
error_reporting(0);
ini_set('display_errors', 0);

// Incluir tu archivo de conexión
require_once __DIR__ . '/HHH/Conexion.php';

// Indicar al navegador y a Google que la respuesta es un documento XML
header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    
    <!-- Páginas principales -->
    <url>
        <loc>https://foxnovel.ct.ws/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>https://foxnovel.ct.ws/Actualizacion.php</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

<?php
// 1. Obtener todas las novelas desde Supabase
$novelas = supabase_request('novela?select=id', 'GET');

if (is_array($novelas) && !isset($novelas['error'])) {
    foreach ($novelas as $novela) {
        $idNovela = $novela['id'];
        echo "    <url>\n";
        echo "        <loc>https://foxnovel.ct.ws/ver_novela.php?id={$idNovela}</loc>\n";
        echo "        <changefreq>weekly</changefreq>\n";
        echo "        <priority>0.8</priority>\n";
        echo "    </url>\n";
    }
}

// 2. Obtener todos los capítulos desde Supabase
$capitulos = supabase_request('capitulos?select=id', 'GET');

if (is_array($capitulos) && !isset($capitulos['error'])) {
    foreach ($capitulos as $capitulo) {
        $idCapitulo = $capitulo['id'];
        echo "    <url>\n";
        echo "        <loc>https://foxnovel.ct.ws/leer_capitulo.php?id={$idCapitulo}</loc>\n";
        echo "        <changefreq>monthly</changefreq>\n";
        echo "        <priority>0.6</priority>\n";
        echo "    </url>\n";
    }
}
?>
</urlset>