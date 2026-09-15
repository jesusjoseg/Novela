<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$ruta_conexion = __DIR__ . '/../HHH/Conexion.php';
require_once $ruta_conexion;

$texto = isset($_GET['texto']) ? trim($_GET['texto']) : '';
$orden = isset($_GET['orden']) ? trim($_GET['orden']) : '';

// Manejar géneros tanto si vienen como array o como cadena separada por comas
$genero = [];
if (isset($_GET['Genero'])) {
    if (is_array($_GET['Genero'])) {
        $genero = $_GET['Genero'];
    } else {
        $genero = array_filter(explode(',', $_GET['Genero']));
    }
}

$sql = "SELECT id, Titulo, Genero, Portada, link FROM novela WHERE 1=1";
$params = [];
$types = "";

if (!empty($texto)) {
    $sql .= " AND Titulo LIKE ?";
    $params[] = '%' . $texto . '%';
    $types .= "s";
}

if (!empty($genero)) {
    foreach ($genero as $g) {
        $sql .= " AND Genero LIKE ?";
        $params[] = '%' . trim($g) . '%';
        $types .= "s";
    }
}

if ($orden === 'titulo_asc') {
    $sql .= " ORDER BY Titulo ASC";
} elseif ($orden === 'titulo_desc') {
    $sql .= " ORDER BY Titulo DESC";
} else {
    $sql .= " ORDER BY id DESC";
}

$stmt = $coon->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$res = $stmt->get_result();
$resultados = [];

while ($row = $res->fetch_assoc()) {
    $resultados[] = [
        'id'      => (int)$row['id'],
        'Titulo'  => $row['Titulo'],
        'Genero'  => $row['Genero'],
        'Portada' => $row['Portada'],
        'Link'    => $row['link']
    ];
}

$stmt->close();

if (ob_get_length()) ob_clean();
echo json_encode($resultados, JSON_UNESCAPED_UNICODE);
?>