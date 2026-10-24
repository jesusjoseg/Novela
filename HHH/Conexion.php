<?php
// HHH/Conexion.php

define('SUPABASE_URL', 'https://ahprflxvnrovrwxaojrw.supabase.co/rest/v1');
define('SUPABASE_KEY', 'sb_publishable_W0IkvLXPpoLZ0fNBk_RENg_iNcnFRuf');

/**
 * Función global para realizar peticiones a la API REST de Supabase
 */
function supabase_request($endpoint, $method = 'GET', $body = null) {
    $url = SUPABASE_URL . '/' . $endpoint;
    
    $headers = [
        'apikey: ' . SUPABASE_KEY,
        'Authorization: Bearer ' . SUPABASE_KEY,
        'Content-Type: application/json',
        'Prefer: return=representation'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($body !== null && in_array($method, ['POST', 'PATCH', 'PUT'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 400) {
        return ['error' => true, 'status' => $httpCode, 'message' => $response];
    }

    return json_decode($response, true);
}

// Variable bandera para validar la inclusión correcta del archivo
$conexion = true; 
?>