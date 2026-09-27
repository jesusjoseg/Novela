<?php
session_start();
include 'HHH/Conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoxNovel--Biblioteca</title>
    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <?php include 'Header.php'?>
    <div class="container">
        <div class="finder-layout">
            <aside class="filter-sidebar">
                <form id="finder-form">
                    <div>
                        <h3>Buscar por titulo</h3>
                        <input type="text" name="texto" class="finder-input">
                    </div>
                    <div>
                        <h3>Ordena por:</h3>
                        <select name="orden" class="finder-select">
                            <option value="">Ultimo Actulizacion</option>
                            <option value="titulo_asc">Titulo (A-Z)</option>
                            <option value="titulo_desc">Titulo (Z-A)</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <h3>Generos</h3>
                        <div class="genre-checkboxes">
                            <label><input type="checkbox" name="Genero[]" value="Accion">Accion</label>
                            <label><input type="checkbox" name="Genero[]" value="Aventura">Aventura</label>
                            <label><input type="checkbox" name="Genero[]" value="Artes Marciales">Artes Marciales</label>
                            <label><input type="checkbox" name="Genero[]" value="Supervivencia">Supervivencia</label>
                            <label><input type="checkbox" name="Genero[]" value="Militar">Militar</label>
                            <label><input type="checkbox" name="Genero[]" value="Fantasia">Fantasia</label>
                            <label><input type="checkbox" name="Genero[]" value="Alta Fantasia">Alta Fantasia</label>
                            <label><input type="checkbox" name="Genero[]" value="Isekai">Isekai</label>
                            <label><input type="checkbox" name="Genero[]" value="Reencarnacion">Reencarnacion</label>
                            <label><input type="checkbox" name="Genero[]" value="Transmigracion">Transmigracion</label>
                            <label><input type="checkbox" name="Genero[]" value="Xianxia">Xianxia</label>
                            <label><input type="checkbox" name="Genero[]" value="Xuanhuan">Xuanhuan</label>
                            <label><input type="checkbox" name="Genero[]" value="Wuxia">Wuxia</label>
                            <label><input type="checkbox" name="Genero[]" value="Fantasía Urbana">Fantasía Urbana</label>
                            <label><input type="checkbox" name="Genero[]" value="Magia">Magia</label>
                            <label><input type="checkbox" name="Genero[]" value="Sistema">Sistema</label>
                            <label><input type="checkbox" name="Genero[]" value="LitRPG">LitRPG</label>
                            <label><input type="checkbox" name="Genero[]" value="Videojuegos">Videojuegos</label>
                            <label><input type="checkbox" name="Genero[]" value="Ciencia Ficcion">Ciencia Ficcion</label>
                            <label><input type="checkbox" name="Genero[]" value="Cyberpunk">Cyberpunk</label>
                            <label><input type="checkbox" name="Genero[]" value="Mecha">Mecha</label>
                            <label><input type="checkbox" name="Genero[]" value="Apocalptico">Apocalptico</label>
                            <label><input type="checkbox" name="Genero[]" value="GenderBender">Gender Bender</label>
                            <label><input type="checkbox" name="Genero[]" value="Yuri">Yuri</label>
                            <label><input type="checkbox" name="Genero[]" value="Shoujo Ai">Shoujo Ai</label>
                            <label><input type="checkbox" name="Genero[]" value="Yaoi">Yaoi</label>
                            <label><input type="checkbox" name="Genero[]" value="Shounen Ai">Shounen Ai</label>
                            <label><input type="checkbox" name="Genero[]" value="Romance">Romance</label>
                            <label><input type="checkbox" name="Genero[]" value="Comedia Romantica">Comedia Romantica</label>
                            <label><input type="checkbox" name="Genero[]" value="Harem">Harem</label>
                            <label><input type="checkbox" name="Genero[]" value="Harem Inverso">Harem Inverso</label>
                            <label><input type="checkbox" name="Genero[]" value="Drama">Drama</label>
                            <label><input type="checkbox" name="Genero[]" value="Tragedia">Tragedia</label>
                            <label><input type="checkbox" name="Genero[]" value="Misterio">Misterio</label>
                            <label><input type="checkbox" name="Genero[]" value="Psicologico">Psicologico</label>
                            <label><input type="checkbox" name="Genero[]" value="Horror">Horror</label>
                            <label><input type="checkbox" name="Genero[]" value="Sobrenatural">Sobrenatural</label>
                            <label><input type="checkbox" name="Genero[]" value="Slice of Life">Slice of Life</label>
                            <label><input type="checkbox" name="Genero[]" value="Comedia">Comedia</label>
                            <label><input type="checkbox" name="Genero[]" value="Vida Escolar">Vida Escolar</label>
                            <label><input type="checkbox" name="Genero[]" value="Historico">Historico</label>
                            <label><input type="checkbox" name="Genero[]" value="Realeza">Realeza</label>
                            <label><input type="checkbox" name="Genero[]" value="Deportes">Deportes</label>
                            <label><input type="checkbox" name="Genero[]" value="Mature">Mature</label>
                            <label><input type="checkbox" name="Genero[]" value="Ecchi">Ecchi</label>
                        </div>
                    </div>
                </form>
            </aside>
            <main class="result-area">
                <div class="novelas-grid" id="biblioteca-grid">

                </div>
            </main>
            <script src="biblioteca.js"></script>
        </div>
    </div>
    <?php include'footer.php'?>
</body>
</html>