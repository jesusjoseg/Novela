<?php
// verificar_expiraciones.php
$token_secreto = "MiClaveSuperSecreta123";

if (!isset($_GET['token']) || $_GET['token'] !== $token_secreto) {
    http_response_code(403);
    echo json_encode(["status" => "error", "mensaje" => "Acceso no autorizado."]);
    exit;
}

require_once 'HHH/Conexion.php'; 

try {
    // Obtenemos la fecha actual en formato ISO 8601 (compatible con Supabase)
    $fechaActual = gmdate('Y-m-d\TH:i:s\.000\Z');

    // 1. Consultar los usuarios que son premium pero cuya fecha fin_suscripcion ya pasó
    // Seleccionamos únicamente la columna 'id' que sí existe en tu tabla
    $endpointConsulta = "usuario?es_premium=eq.true&fin_suscripcion=lt.{$fechaActual}&select=id";
    $usuariosVencidos = supabase_request($endpointConsulta, 'GET');

    if (isset($usuariosVencidos['error'])) {
        throw new Exception("Error al consultar usuarios vencidos: " . json_encode($usuariosVencidos));
    }

    $afectados = 0;

    if (!empty($usuariosVencidos) && is_array($usuariosVencidos)) {
        foreach ($usuariosVencidos as $usuario) {
            $idUsuario = $usuario['id'];

            // 2. Actualizar cada usuario vencido usando su 'id' primario para quitarle el VIP y limpiar fechas
            $endpointUpdate = "usuario?id=eq.{$idUsuario}";
            $resultadoUpdate = supabase_request($endpointUpdate, 'PATCH', [
                'es_premium' => false,
                'inicio_suscripcion' => null,
                'fin_suscripcion' => null
            ]);

            if (!isset($resultadoUpdate['error'])) {
                $afectados++;
            }
        }
    }
    
    echo json_encode([
        "status" => "success",
        "mensaje" => "Expiraciones procesadas correctamente a través de Supabase API.",
        "usuarios_desactivados" => $afectados
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error", 
        "mensaje" => $e->getMessage()
    ]);
}
?>