<?php
require_once 'HHH/Conexion.php';

header('Content-Type: application/json; charset=utf-8');

$texto  = isset($_GET['texto']) ? trim($_GET['texto']) : '';
$orden  = isset($_GET['orden']) ? trim($_GET['orden']) : '';
$genero = isset($_GET['Genero']) && is_array($_GET['Genero']) ? $_GET['Genero'] : [];

$sql = 'SELECT id, "Titulo", "Genero", "Portada", "link" FROM novela WHERE 1=1';
$params = [];

// 1. Filtro por Título (insensible a mayúsculas/minúsculas)
if (!empty($texto)) {
    $sql .= ' AND "Titulo" ILIKE ?';
    $params[] = '%' . $texto . '%';
} 

// 2. Filtro por Géneros (múltiples tags)
if (!empty($genero)) {
    foreach ($genero as $g) {
        $sql .= ' AND "Genero" ILIKE ?';
        $params[] = '%' . trim($g) . '%';
    }
}

// 3. Ordenamiento
if ($orden === 'titulo_asc') {
    $sql .= ' ORDER BY "Titulo" ASC';
} elseif ($orden === 'titulo_desc') {
    $sql .= ' ORDER BY "Titulo" DESC';
} else {
    $sql .= ' ORDER BY id DESC';
}

$resultados = [];

try {
    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    $novelas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($novelas as $row) {
        $resultados[] = [
            'id'      => $row['id'],
            'Titulo'  => htmlspecialchars($row['Titulo']),
            'Genero'  => htmlspecialchars($row['Genero']),
            'Portada' => htmlspecialchars($row['Portada']),
            'Link'    => htmlspecialchars($row['link'])
        ];
    }
} catch (PDOException $e) {
    error_log("Error en la filtración de novelas: " . $e->getMessage());
}

echo json_encode($resultados, JSON_UNESCAPED_UNICODE);
?>