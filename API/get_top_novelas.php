<?php
// Permitir peticiones desde cualquier origen (React Native / Expo)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../HHH/Conexion.php';

// Consulta equivalente en REST Supabase con alias y ordenamiento:
$endpoint = 'novela?select=id,titulo:Titulo,sinopsis:Descripcion,genero:Genero,portada:Portada,visitas:Visitas&order=Visitas.desc&limit=10';

$topNovelas = supabase_request($endpoint, 'GET');

if (isset($topNovelas['error'])) {
    http_response_code($topNovelas['status'] ?? 500);
    echo json_encode(["error" => "Error de Supabase REST", "detalle" => $topNovelas['message']]);
    exit();
}

echo json_encode($topNovelas, JSON_UNESCAPED_UNICODE);
?>