<?php
require_once 'HHH/Conexion.php';

header('Content-Type: application/json; charset=utf-8');

$texto  = isset($_GET['texto']) ? trim($_GET['texto']) : '';
$orden  = isset($_GET['orden']) ? trim($_GET['orden']) : '';
$genero = isset($_GET['Genero']) && is_array($_GET['Genero']) ? $_GET['Genero'] : [];

// Construcción de parámetros para PostgREST (Supabase)
$params = [
    'select' => 'id,Titulo,Genero,Portada,link'
];

// 1. Filtro por Título (ilike en PostgREST)
if (!empty($texto)) {
    $params['Titulo'] = 'ilike.*' . rawurlencode($texto) . '*';
}

// 2. Filtro por Géneros
if (!empty($genero)) {
    // Si hay un solo género, usamos ilike. Si hay varios, aplicamos un operador 'and' o múltiples condiciones.
    if (count($genero) === 1) {
        $params['Genero'] = 'ilike.*' . rawurlencode(trim($genero[0])) . '*';
    } else {
        $condicionesGeneros = array_map(function($g) {
            return 'Genero.ilike.*' . rawurlencode(trim($g)) . '*';
        }, $genero);
        $params['and'] = '(' . implode(',', $condicionesGeneros) . ')';
    }
}

// 3. Ordenamiento
if ($orden === 'titulo_asc') {
    $params['order'] = 'Titulo.asc';
} elseif ($orden === 'titulo_desc') {
    $params['order'] = 'Titulo.desc';
} else {
    $params['order'] = 'id.desc';
}

// Construcción del Endpoint HTTP
$queryPath = 'novela?' . http_build_query($params);

// Petición cURL usando supabase_request de Conexion.php
$response = supabase_request($queryPath, 'GET');

$resultados = [];

if (is_array($response) && !isset($response['error'])) {
    foreach ($response as $row) {
        $resultados[] = [
            'id'      => $row['id'] ?? 0,
            'Titulo'  => htmlspecialchars($row['Titulo'] ?? ''),
            'Genero'  => htmlspecialchars($row['Genero'] ?? ''),
            'Portada' => htmlspecialchars($row['Portada'] ?? ''),
            'Link'    => htmlspecialchars($row['link'] ?? '#')
        ];
    }
} else {
    error_log("Error al consultar Supabase en filtrar_biblioteca.php: " . json_encode($response));
}

echo json_encode($resultados, JSON_UNESCAPED_UNICODE);
?>