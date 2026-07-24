<?php
session_start();
include 'HHH/Conexion.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $correo =trim($_POST['Correo']??'');
    $contrasena =trim($_POST['Contrasena']??'');
    if(!empty($correo)&& !empty($contrasena)){
        $stmt=$coon->prepare("SELECT id,Nombre,Apellido,Contrasena,rol FROM usuarios WHERE Correo=?");
        if($stmt){
            $stmt->bind_param("s",$correo);
            $stmt->execute();
            $resultado =$stmt->get_result();
            if($resultado->num_rows===1){
                $usuario=$resultado->fetch_assoc();
                if(password_verify($contrasena,$usuario['Contrasena'])){
                    $_SESSION['usuario_id']=$usuario['id'];
                    $_SESSION['usuario_nombre']=$usuario['Nombre'];
                    $_SESSION['usuario_rol']=$usuario['rol'];
                    header('Location:index.php');
                    exit();
                }
            }
            $stmt->close();
            
        }
    }
}
header("Location: Login.php");
?>