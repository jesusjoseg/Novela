<?php 
include 'NovelaData.php';
$query =isset($_GET['q'])? strtolower(trim($_GET['q'])):'';
$resultados =[];
if ($query !==''){
    foreach ($Novelas as $Novela ) {
        if (str_contains(strtolower($Novela['Titulo']),$query)){
            $resultados[] =$Novela;
        }
    }
}
header('Content-Type: application/json');
echo json_encode($resultados);
?>