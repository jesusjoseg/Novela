<?php 
require_once 'HHH/Conexion.php';

header('Content-Type: application/json; charset=utf-8');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$resultados = [];

if (mb_strlen($query) >= 2) {
    $searchTerm = '%' . $query . '%';

    $params = [
        'select' => 'id,Titulo,Genero,Portada,link',
        'or'     => '(Titulo.ilike.' . $searchTerm . ',Genero.ilike.' . $searchTerm . ')',
        'limit'  => '6'
    ];

    $endpoint = 'novela?' . http_build_query($params);
    $response = supabase_request($endpoint, 'GET');

    if (is_array($response) && !isset($response['error'])) {
        foreach ($response as $row) {
            $resultados[] = [
                'id'      => $row['id'] ?? 0,
                'Titulo'  => $row['Titulo'] ?? '',
                'Genero'  => $row['Genero'] ?? '',
                'Portada' => $row['Portada'] ?? '',
                'Link'    => $row['link'] ?? '#' // Mantiene la URL original intacta
            ];
        }
    } else {
        error_log("Error en la búsqueda rápida con Supabase: " . json_encode($response));
    }
}

echo json_encode($resultados, JSON_UNESCAPED_UNICODE);
?>