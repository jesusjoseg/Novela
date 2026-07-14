<?php
include 'NovelaData.php';
$texto= isset($_GET['texto'])?strtolower(trim($_GET['texto'])):'';
$generos_selecionado= isset($_GET['Genero'])? $_GET['Genero']:[];
$orden= isset($_GET['orden'])? $_GET['orden']:'';
$resultados=[];
foreach($Novelas as $Novela){
    if (empty($Novela['Titulo']))continue;
    $cumple_filtro= true;
    if ($texto!==''){
        if(strpos(strtolower($Novela['Titulo']),$texto)===false){
            $cumple_filtro= false;
        }
    }
    if(!empty($generos_selecionado)&&$cumple_filtro){
        $generos_novela=array_map('trim', explode('/',strtolower($Novela['Genero'])));
        foreach($generos_selecionado as $gen_buscado){
            if (!in_array(strtolower($gen_buscado),$generos_novela)){
                $cumple_filtro=false;
                break;
            }
        }
    }
    if($cumple_filtro){
        $resultados[]= $Novela;
    }
}
if($orden==='titulo_asc'){
    usort($resultados,function ($a, $b) {
        return strcmp($a['Titulo'],$b['Titulo']);
    });
}
elseif($orden==='titulo_desc'){
    usort($resultados,function ($a, $b) {
        return strcmp($b['Titulo'],$a['Titulo']);
    });
}
header('Content-Type: application/json');
echo json_encode($resultados);
?>