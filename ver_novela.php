<?php
session_start();
include 'HHH/Conexion.php';
$id_novela = isset($_GET['id'])? intval($_GET['id']):0;
$novela= null;
$stmt = $coon->prepare("SELECT id,Titulo,Descripcion,Genero,Estado,Portada,link,Visitas FROM novela WHERE ID =?");
$stmt->bind_param("i",$id_novela);
$stmt->execute();
$res=$stmt->get_result();
if ($res && $res->num_rows>0){
    $novela =$res->fetch_assoc();
}
$stmt->close();
if(!$novela){
    header("Location: biblioteca.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($novela['Titulo']); ?>- Lectura Novela</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
       <div class="novela-detalle-layout">
        <aside class="novela-sidebar">
            <img src="<?php echo htmlspecialchars($novela['Portada']) ;?>" class="novela-portada" alt="<?php echo htmlspecialchars($novela['Titulo']) ?>">
            <div class="novela-ficha">
                <p><strong>Genero: </strong> <?php echo htmlspecialchars($novela['Genero']) ;?></p>
                <p><strong>Estado: </strong> <?php echo htmlspecialchars($novela['Estado']) ;?></p>
                <p><strong>visitas: </strong> <?php echo htmlspecialchars($novela['Visitas']) ;?></p>
            </div>
        </aside>
        <main class="novela-contenido">
            <h1 class="novela-titulo-principal"><?php echo htmlspecialchars($novela['Titulo']); ?></h1>
            <h3>Sinopsis</h3>
            <p class="novela-sinopsis"><?php echo htmlspecialchars($novela['Descripcion']) ;?></p>
        </main>
       </div> 
    </div>
</body>
</html>