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

$rutaConexion = __DIR__ . '/../HHH/Conexion.php';

if (!file_exists($rutaConexion)) {
    echo json_encode(["error" => "No se encontró el archivo Conexion.php"]);
    exit();
}

require_once $rutaConexion;

if (!isset($coon) || !$coon) {
    echo json_encode(["error" => "No se pudo conectar a la base de datos"]);
    exit();
}

$id_novela = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_novela === 0) {
    echo json_encode(["novela" => null, "capitulos" => []]);
    exit();
}

// 1. Obtener la novela solicitada
$stmt = $coon->prepare("SELECT id, Titulo, Descripcion, Genero, Estado, Portada, Visitas FROM novela WHERE id = ?");
$stmt->bind_param("i", $id_novela);
$stmt->execute();
$res = $stmt->get_result();

$novela = null;
if ($res && $res->num_rows > 0) {
    $fila = $res->fetch_assoc();
    $novela = array(
        "id" => (int)$fila['id'],
        "Titulo" => $fila['Titulo'],
        "Descripcion" => $fila['Descripcion'],
        "Genero" => $fila['Genero'] ?? 'General',
        "Estado" => $fila['Estado'] ?? 'En emisión',
        "Portada" => $fila['Portada'],
        "Visitas" => (int)$fila['Visitas']
    );
}
$stmt->close();

// 2. Obtener lista de capítulos
$capitulos = array();
if ($novela) {
    $stmt_cap = $coon->prepare("SELECT id, Capitulo, Titulo FROM Capitulos WHERE novela_id = ? ORDER BY Capitulo ASC");
    $stmt_cap->bind_param("i", $id_novela);
    $stmt_cap->execute();
    $res_cap = $stmt_cap->get_result();
    
    while ($row = $res_cap->fetch_assoc()) {
        $capitulos[] = array(
            "id" => (int)$row['id'],
            "Capitulo" => (int)$row['Capitulo'],
            "Titulo" => $row['Titulo']
        );
    }
    $stmt_cap->close();
}

if (ob_get_length()) ob_clean();

echo json_encode([
    "novela" => $novela,
    "capitulos" => $capitulos
], JSON_UNESCAPED_UNICODE);
?>