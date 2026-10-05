<?php
session_start();
require_once 'HHH/Conexion.php';
header('Content-Type: application/json');
$id_novela =isset($_POST['novela_id']) ? intval($_POST['novela_id']):0;

if($id_novela<=0){
    echo json_encode(['success'=> false,'message'=>'ID invalido']);
    exit();
}
if(!isset($_SESSION['novela_visitadas'])){
    $_SESSION['novela_visitadas']=[];
}
if(in_array($id_novela,$_SESSION['novela_visitadas'])){
    echo json_encode(['success' => true,'message'=>'Visita ya contadar previamente']);
    exit();
}
$res_novela= supabase_request("novela?id=eq.{$id_novela}&select=Visitas");
if(!empty($res_novela)&& !isset($res_novela['error'])){
    $visitas_actuales = intval($res_novela[0]['Visitas']??0)+1;
    supabase_request("novela?id=eq.{$id_novela}",'PATCH',['Visitas'=> $visitas_actuales]);
    $_SESSION['novela_visitadas'][]=$id_novela;
    echo json_encode(['success'=>true,'nuevas_visitas'=>$visitas_actuales]);
}
else{
    echo json_encode(['success' => false,'message'=>'Error al consultarla base de datos']);
}
?>