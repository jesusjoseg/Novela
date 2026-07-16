<?php 
include 'HHH/Conexion.php';
if ($_SERVER['REQUEST_METHOD']=== 'POST'){
    $nombre=trim($_POST['Nombre']??'');
    $apellido=trim($_POST['Apellido']??'');
    $correo=trim($_POST['Correo']??'');
    $Contrasena=$_POST['Contrasena']??'';
    $VContrasena=$_POST['VerificaContrasena']??'';
    if(!empty($nombre)){
        $password_hash= password_hash($Contrasena,PASSWORD_BCRYPT);
    }

}
header("Location: Login.php");
exit();
?>