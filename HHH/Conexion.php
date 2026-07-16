<?php 
$host = "localhost";
$db ="FoxNovela";
$user= "root";
$Pass="";
$coon = new mysqli($host,$user,$Pass,$db);
if($coon->connect_error){
    die("conexion fallida: " . $coon->connect_error);
}
echo "conexio completa";
?>