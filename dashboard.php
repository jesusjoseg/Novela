<?php
session_start();
include 'HHH/Conexion.php';
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location:index.php');
    exit();
}
function reordenaCapitulo($coon, $novela_id)
{
    $stmt = $coon->prepare("SELECT id FROM Capitulos WHERE novela_id =? ORDER BY id ASC");
    $stmt->bind_param("i", $novela_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $nuevo_numero = 1;
    while ($cap = $resultado->fetch_asoc()) {
        $update = $coon->prepare("UPDATE Capitulos set Capitulo = ? WHERE id = ?");
        $update->bind_param("i", $nuevo_numero, $cap['id']);
        $update->execute();
        $update->close();
        $nuevo_numero++;
    }
    $stmt->close();
}
$mensaje_novela = "";
$mensaje_Capitulo = "";
if ($_SERVER['REQUEST_METHOD']==='POST'){
    if(isset($_POST['accion'])&& $_POST['accion']==='guardar_novela'){
        
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovelaFox--Dashboard Admintrativo</title>
    <link rel="stylesheet" href="Style.css">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</head>

<body>
    <?php include 'header.php' ?>
    <div class="container form-container">
        <aside class="filter-sidebar form-sidebar">
            <h2 class="form-title">Panel de Control</h2>
            <p>Bienvenido <?php echo htmlspecialchars($_SESSION['usuario_nombre']) ?></p>
            <div class="tabs-nav">
                <button class="tab-btn active" onclick="switchTab('tab-novelas')">Registrar Novela</button>
                <button class="tab-btn" onclick="switchTab('tab-capitulo')">Subir Capitulos</button>
            </div>
            <div id="tab-novelas" class="tab-content active">
                <?php if ($mensaje_novela): ?>
                    <p id="mensajedash"><?php echo $mensaje_novela; ?></p>
                <?php endif; ?>
                <form action="dashboard.php" method="post">
                    <input type="hidden" name="accion" value="guardar_novela">
                    <div class="form-group">
                        <h3>Titulo:</h3>
                        <input type="text" name="Titulo" class="finder-input" required>
                    </div>
                    <div class="form-group">
                        <h3>Genero:</h3>
                        <div class="generos-grid">
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Accion">Accion</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Aventura">Aventura</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Artes Marciales">Artes Marciales</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Supervivencia">Supervivencia</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Militar">Militar</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Fantasia">Fantasia</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Alta Fantasia">Alta Fantasia</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Isekai">Isekai</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Reencarnacion">Reencarnacion</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Transmigracion">Transmigracion</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Xianxia">Xianxia</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Xuanhuan">Xuanhuan</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Wuxia">Wuxia</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Fantasía Urbana">Fantasía Urbana</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Magia">Magia</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Sistema">Sistema</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="LitRPG">LitRPG</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Videojuegos">Videojuegos</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Ciencia Ficcion">Ciencia Ficcion</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Cyberpunk">Cyberpunk</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Mecha">Mecha</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Apocalptico">Apocalptico</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="GenderBender">Gender Bender</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Yuri">Yuri</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Shoujo Ai">Shoujo Ai</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Yaoi">Yaoi</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Shounen Ai">Shounen Ai</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Romance">Romance</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Comedia Romantica">Comedia Romantica</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Harem">Harem</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Harem Inverso">Harem Inverso</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Drama">Drama</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Tragedia">Tragedia</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Misterio">Misterio</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Psicologico">Psicologico</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Horror">Horror</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Sobrenatural">Sobrenatural</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Slice of Life">Slice of Life</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Comedia">Comedia</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Vida Escolar">Vida Escolar</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Historico">Historico</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Realeza">Realeza</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Deportes">Deportes</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Mature">Mature</label>
                            <label><input class="checkbox-chip" type="checkbox" name="Genero[]" value="Ecchi">Ecchi</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <h3>URL Portada:</h3>
                        <input type="text" name="portada" class="finder-input" required>
                    </div>
                    <div class="form-group">
                        <h3>Estados:</h3>
                        <select name="estado" id="">
                            <option value="Pediente">Pediente</option>
                            <option value="Completado">Completado</option>
                            <option value="Pausado">Pausado</option>
                            <option value="Finalizado">Finalizado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <h3>Descripcion:</h3>
                        <textarea id="texa" name="descripcion" class="finder-input" required></textarea>
                    </div>
                    <input type="submit" value="Guardar Novela" class="btn-leer form-btn">
                </form>
            </div>
            <div id="tab-capitulo" class="tab-content">
                <?php if ($mensaje_Capitulo): ?> <p>
                        <?php echo $mensaje_Capitulo; ?>
                    </p>
                <?php endif; ?>
                <form action="dashboard.php" method="post">
                    <div class="form-group">
                        <h3>Seleciona Novela:</h3>
                        <select name="novela_id" class="finder-input" required>
                            <option value="--Selecionar">--Selecionar--</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <h3>Título del Capitulo</h3>
                        <input type="text" name="titulo_capitulo" class="finder-input" required>
                    </div>
                    <div class="editor-split">
                        <div class="form-group">
                            <h3>Contenido</h3>
                            <textarea name="contenido_markdown" class="finder-input" id="md-editor"></textarea>
                        </div>
                        <div>
                            <h3>Vista previa</h3>
                            <div id="md-preview" class="box-preview"></div>
                        </div>
                    </div>
                    <input type="submit" value="Publicar Capítulo" class="btn-leer form-btn">
                </form>
            </div>
        </aside>
    </div>
    <script src="Novela.js"></script>
</body>

</html>