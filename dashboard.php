<?php
session_start();
include 'HHH/Conexion.php';
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol']!=='admin'){
    header('Location:index.php');
    exit();
}
function reordenaCapitulo($coon,$novela_id){
    $stmt= $coon->prepare("SELECT id FROM Capitulos WHERE novela_id =? ORDER BY id ASC");
    $stmt -> bind_param("i",$novela_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovelaFox--Dashboard Admintrativo</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <?php include 'header.php' ?>
    <div class="container form-container">
        <aside class="filter-sidebar form-sidebar">
            <h2 class="form-title">Panel de Control</h2>
            <p>Bienvenido <?php echo htmlspecialchars($_SESSION['usuario_nombre']) ?></p>
            <div class="tabs-nav">
                <button class="tab-btn active" onclick="switchTab('tab-novelas')">Registrar Novela</button>
                <button class="tab-btn">Subir Capitulos</button>
            </div>
            <div id="tab-novelas" class="tab-content active">
                
            </div>
        </aside>
    </div>
    <script src="Novela.js"></script>
</body>
</html>