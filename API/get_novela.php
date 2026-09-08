<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Ruta hacia Conexion.php
$rutaConexion = __DIR__ . '/../HHH/Conexion.php';

if (!file_exists($rutaConexion)) {
    echo json_encode(["error" => "No se encontró el archivo Conexion.php"]);
    exit();
}

require_once $rutaConexion;

// Verificamos si la variable $coon existe
if (!isset($coon) || !$coon) {
    echo json_encode(["error" => "No se pudo conectar a la base de datos con \$coon"]);
    exit();
}

// Consulta usando la variable $coon
$query = "SELECT id, Titulo, Descripcion FROM novela ORDER BY id DESC";
$resultado = $coon->query($query);

$novelas = array();

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $novelas[] = array(
            "id" => (int)$fila['id'],
            "titulo" => $fila['Titulo'],
            "sinopsis" => $fila['Descripcion']
        );
    }
}

if (ob_get_length()) ob_clean();

echo json_encode($novelas, JSON_UNESCAPED_UNICODE);
?>