<?php 
include 'HHH/Conexion.php';
if ($_SERVER['REQUEST_METHOD']=== 'POST'){
    $nombre=trim($_POST['Nombre']??'');
    $apellido=trim($_POST['Apellido']??'');
    $correo=trim($_POST['Correo']??'');
    $Contrasena=$_POST['Contrasena']??'';
    $VContrasena=$_POST['VerificaContrasena']??'';
    if(!empty($nombre) && !empty($apellido)&& !empty($correo) && !empty($Contrasena) &&($Contrasena===$VContrasena)){
        $password_hash= password_hash($Contrasena,PASSWORD_BCRYPT);
        $stmt=$coon->prepare("INSERT INTO usuarios (Nombre,Apellido,Correo,Contrasena) VALUES(?,?,?,?)");
        if($stmt){
            $stmt->bind_param("ssss",$nombre,$apellido,$correo,$password_hash);
            $stmt->execute();
            $stmt->close();
            header("Location:Login.php");
            exit();
        }
    }

}
header("Location: regristaces.php");
exit();
?>