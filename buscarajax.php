<?php 
require_once 'HHH/Conexion.php';

header('Content-Type: application/json; charset=utf-8');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$resultados = [];

if (strlen($query) >= 2) {
    try {
        $param = '%' . $query . '%';

        // Usamos "ILIKE" en PostgreSQL para búsqueda insensible a mayúsculas/minúsculas
        // y encerrar los nombres de las columnas en comillas dobles
        $sql = 'SELECT id, "Titulo", "Genero", "Portada", "link" 
                FROM novela 
                WHERE "Titulo" ILIKE :param1 OR "Genero" ILIKE :param2 
                LIMIT 6';

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':param1' => $param,
            ':param2' => $param
        ]);

        $novelas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($novelas as $row) {
            $resultados[] = [
                'id'      => $row['id'],
                'Titulo'  => htmlspecialchars($row['Titulo']),
                'Genero'  => htmlspecialchars($row['Genero']),
                'Portada' => htmlspecialchars($row['Portada']),
                'Link'    => htmlspecialchars($row['link']) // Se mantiene 'Link' para tu JS
            ];
        }

    } catch (PDOException $e) {
        error_log("Error en la búsqueda: " . $e->getMessage());
    }
}

echo json_encode($resultados, JSON_UNESCAPED_UNICODE);
?>