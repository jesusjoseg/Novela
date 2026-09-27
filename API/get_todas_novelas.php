<?php
// API/get_todas_novelas.php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../HHH/Conexion.php';

// Hacer la petición GET a la tabla 'novela' ordenada por id descendente
$novelas = supabase_request('novela?select=*&order=id.desc', 'GET');

// Si Supabase devuelve un error en el cURL
if (isset($novelas['error'])) {
    http_response_code($novelas['status'] ?? 500);
    echo json_encode(["error" => "Error de Supabase REST", "detalle" => $novelas['message']]);
    exit();
}

// Retornar los datos limpios en JSON
echo json_encode($novelas, JSON_UNESCAPED_UNICODE);
?>