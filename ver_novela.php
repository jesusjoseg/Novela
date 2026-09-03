<?php
session_start();
include 'HHH/Conexion.php';
$id_novela = isset($_GET['id'])? intval($_GET['id']):0;
$novela= null;
$primer_capitulo_id=null;
$lista_capitulo=[];
$stmt = $coon->prepare("SELECT id,Titulo,Descripcion,Genero,Estado,Portada,link,Visitas FROM novela WHERE ID =?");
$stmt->bind_param("i",$id_novela);
$stmt->execute();
$res=$stmt->get_result();
if ($res && $res->num_rows>0){
    $novela =$res->fetch_assoc();
}
$stmt->close();
$stmt_cap= $coon->prepare("SELECT id, Capitulo, Titulo FROM Capitulos WHERE novela_id = ? ORDER BY Capitulo ASC");
$stmt_cap->bind_param("i",$id_novela);
$stmt_cap->execute();
$res_cap= $stmt_cap->get_result();
while ($row =$res_cap->fetch_assoc()){
    $lista_capitulo[]=$row;
}
$stmt_cap->close();
if(!empty($lista_capitulo)){
    $primer_capitulo_id= $lista_capitulo[0]['id'];
}
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
            <div class="novela-acciones">
                <?php if($primer_capitulo_id): ?>
                    <a href="leer_capitulo.php?id=<?php echo $primer_capitulo_id; ?>"class="btn-leer">Comenzar a Leer</a>
                    <?php else: ?>
                        <span class="btn-disabled">Sin capítulos disponibles</span>
                    <?php endif; ?>
            </div>
            
            <div class="seccion-Capitulos">
                <h3>Lista de Capitulos</h3>
                <?php if (!empty($lista_capitulo)): ?>
                    <ul class="lista_capitulos">
                        <?php foreach ($lista_capitulo as $cap): ?>
                            <li><a href="leer_capitulo.php?id=<?php echo $cap['id'] ?>"> Capitulo <?php echo $cap['Capitulo']; ?>: <?php echo $cap['Titulo']; ?></a></li>
                            <?php endforeach; ?>  
                    </ul>
                    <?php else: ?>
                        <p>Aún no hay subido capítulos para esta novela</p>
                        <?php endif; ?>
            </div>
        </main>
       </div> 
    </div>
</body>
</html>