<?php
include 'HHH/Conexion.php';
header('Content-Type: application/json; charset=utf-8');
$texto =isset($_GET['texto'])? trim($_GET['texto']):'';
$orden= isset($_GET['orden'])? trim($_GET['orden']):'';
$genero= isset($_GET['Genero']) && is_array($_GET['Genero'])? $_GET['Genero']:[];
$sql ="SELECT id,Titulo,Genero,Portada,link FROM novela WHERE 1=1";
$params=[];
$types = "";
if(!empty($texto)){
    $sql.= " AND Titulo LIKE ?";
    $params[] = '%' . $texto .'%';
    $types.="s";
} 
if(!empty($genero)){
    foreach ($genero as $g){
        $sql.= " AND Genero LIKE ?";
        $params[]= '%' . $g .'%';
        $types.="s";
    }
}
if ($orden ==='titulo_asc'){
    $sql.=" ORDER BY Titulo ASC";
}
elseif($orden ==='titulo_desc'){
    $sql.= " ORDER BY Titulo DESC";
}
else{
    $sql.= " ORDER BY id DESC";
}
$stmt = $coon->prepare($sql);
if(!empty($params)){
    $stmt->bind_param($types,...$params);
}
$stmt->execute();
$res=$stmt->get_result();
$resultados=[];
while($row=$res->fetch_assoc()){
    $resultados[] = [
        'id'      => $row['id'],
        'Titulo'  => htmlspecialchars($row['Titulo']),
        'Genero'  => htmlspecialchars($row['Genero']),
        'Portada' => htmlspecialchars($row['Portada']),
        'Link'    => htmlspecialchars($row['link'])];
}
$stmt->close();
echo json_encode($resultados, JSON_UNESCAPED_UNICODE);
?>