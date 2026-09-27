<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$ruta_conexion = __DIR__ . '/../HHH/Conexion.php';

if (!file_exists($ruta_conexion)) {
    echo json_encode(["error" => "No se encontró Conexion.php"]);
    exit();
}

require_once $ruta_conexion;

$capitulo_id = isset($_GET['capitulo_id']) ? intval($_GET['capitulo_id']) : 0;
$novela_id   = isset($_GET['novela_id']) ? intval($_GET['novela_id']) : 0;

$comentarios = [];

try {
    if ($capitulo_id > 0) {
        // Obtenemos los comentarios específicos de un capítulo
        $stmt = $coon->prepare("
            SELECT c.id, c.comentario AS texto, c.fecha, u.nombre AS usuario 
            FROM comentarios c
            JOIN usuarios u ON c.usuario_id = u.id
            WHERE c.capitulo_id = ?
            ORDER BY c.fecha DESC
        ");
        $stmt->bind_param("i", $capitulo_id);
    } elseif ($novela_id > 0) {
        // Obtenemos los comentarios generales de una novela
        $stmt = $coon->prepare("
            SELECT c.id, c.comentario AS texto, c.fecha, u.nombre AS usuario 
            FROM comentarios c
            JOIN usuario u ON c.usuario_id = u.id
            WHERE c.novela_id = ?
            ORDER BY c.fecha DESC
        ");
        $stmt->bind_param("i", $novela_id);
    } else {
        echo json_encode([]);
        exit();
    }

    $stmt->execute();
    $resultado = $stmt->get_result();

    while ($fila = $resultado->fetch_assoc()) {
        $comentarios[] = [
            "id" => (int)$fila['id'],
            "usuario" => $fila['usuario'],
            "texto" => $fila['texto'],
            "fecha" => $fila['fecha']
        ];
    }

    $stmt->close();

    if (ob_get_length()) ob_clean();
    echo json_encode($comentarios, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>