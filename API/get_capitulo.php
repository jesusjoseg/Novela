<?php
error_reporting(0);
ini_set('display_errors',0);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
if ($_SERVER['REQUEST_METHOD']==='OPTIONS'){
    http_response_code(200);
    exit();
}
$ruta_conexion=__DIR__ .'/../HHH/Conexion.php';
if (!file_exists($ruta_conexion)){

}
require_once $ruta_conexion;

?>