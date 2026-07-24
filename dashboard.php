<?php
session_start();
include 'HHH/Conexion.php';
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol']!=='admin'){
    header('Location:index.php');
    exit();
}

?>