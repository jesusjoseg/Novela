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
if(!isset($coon)|| !$coon){
    echo json_encode(["error" => "No se pudo conectar a la base de datos"]);
    exit();
}
$id_capitulo =isset($_GET['id'])? intval($_GET['id']):0;
if($id_capitulo===0){
    echo json_encode(null);
    exit();
}
$stmt =$coon->prepare("SELECT id, novela_id, Capitulo,Titulo,Contenido_markdown,fecha_Publicacion FROM capitulos WHERE id=?");
$stmt->bind_param("i",$id_capitulo);
$stmt->execute();
$res=$stmt->get_result();

if(!$res || $res->num_rows===0){
    echo json_encode(null);
    exit();
}
$capituloActual =$res->fetch_assoc();
$stmt->close();

$novela_id=$capituloActual['novela_id'];
$num_capitulo =$capituloActual['Capitulo'];

$stmt_ant =$coon->prepare("SELECT id FROM capitulos WHERE novela_id = ? AND Capitulo< ? ORDER BY Capitulo DESC LIMIT 1");
$stmt_ant->bind_param('ii', $novela_id,$num_capitulo);
$stmt_ant->execute();
$res_ant = $stmt_ant->get_result();
$anterior = $res_ant->fetch_assoc();
$anterior_id = $anterior ? (int)$anterior['id']:null;
$stmt_ant -> close();

$stmt_sig= $coon->prepare("SELECT id FROM capitulos WHERE novela_id =? AND Capitulo >? ORDER BY Capitulo ASC LIMIT 1");
$stmt_sig->bind_param('ii',$novela_id,$num_capitulo);
$stmt_sig->execute();
$res_sig=$stmt_sig->get_result();
$siguiete=$res_sig->fetch_assoc();
$siguiente_id=$siguiete ? (int)$siguiete['id']:null;
$stmt_sig->close();

if(ob_get_length())ob_clean();

echo json_encode([
    "capitulo"=>[
        "id"=>(int)$capituloActual['id'],
        "Capitulo"=>(int)$capituloActual['Capitulo'],
        "Titulo"=>$capituloActual['Titulo'],
        "Contenido_markdown"=>$capituloActual['Contenido_markdown'],
        "fecha_Publicacion"=>$capituloActual['fecha_Publicacion']
    ],"anterior_id"=>$anterior_id,
    "siguiente_id"=>$siguiente_id
    ],JSON_UNESCAPED_UNICODE);
?>