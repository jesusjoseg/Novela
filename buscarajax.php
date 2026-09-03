<?php 
include 'HHH/Conexion.php';

header('Content-Type: application/json; charset=utf-8');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$resultados = [];

if (strlen($query) >= 2) {
    // Consulta con LIKE para buscar coincidencias por Título o Género
    $param = '%' . $query . '%';
    $stmt = $coon->prepare("SELECT id, Titulo, Genero, Portada, link FROM novela WHERE Titulo LIKE ? OR Genero LIKE ? LIMIT 6");
    $stmt->bind_param("ss", $param, $param);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $resultados[] = [
            'id'      => $row['id'],
            'Titulo'  => htmlspecialchars($row['Titulo']),
            'Genero'  => htmlspecialchars($row['Genero']),
            'Portada' => htmlspecialchars($row['Portada']),
            'Link'    => htmlspecialchars($row['link']) // 'Link' en mayúscula para coincidir con tu JS
        ];
    }
    $stmt->close();
}

echo json_encode($resultados, JSON_UNESCAPED_UNICODE);
?>