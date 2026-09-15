<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../HHH/Conexion.php';

try {
    // Usamos alias (AS) para que el JSON devuelva llaves en minúsculas (titulo, sinopsis, etc.) 
    // tal como las espera la interfaz Novela de React Native.
    $sql = 'SELECT 
                id, 
                "Titulo" AS titulo, 
                "Descripcion" AS sinopsis, 
                "Genero" AS genero, 
                "Portada" AS portada, 
                "Visitas" AS visitas 
            FROM novela 
            ORDER BY id DESC';

    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $novelas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($novelas);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
}
?>