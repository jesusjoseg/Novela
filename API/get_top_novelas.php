<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../HHH/Conexion.php';

try {
    // Ordenamos por "Visitas" descendentemente para armar el top
    $sql = 'SELECT 
                id, 
                "Titulo" AS titulo, 
                "Descripcion" AS sinopsis, 
                "Genero" AS genero, 
                "Portada" AS portada, 
                "Visitas" AS visitas 
            FROM novela 
            ORDER BY "Visitas" DESC 
            LIMIT 10';

    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $topNovelas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($topNovelas);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
}
?>