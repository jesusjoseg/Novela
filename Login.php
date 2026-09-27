
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <?php include 'Header.php' ?>
    <div class="container form-container">
        <aside class="filter-sidebar form-sidebar">
            <h2 class="form-title">Iniciar Sesión</h2>
            <form action="seccion.php" method="post">
                <div class="form-group">
                    <label for="">Correo:</label>
                    <input type="email" name="Correo" id="Correo"class="finder-input"required>
                </div>
                <div>
                    <label for="">Contraseña:</label>
                    <input type="password" name="Contrasena" id="Contrasena"class="finder-input"required>
                </div>
                <div>
                    <input type="submit" value="Inicia"class="btn-leer form-btn">
                </div>
            </form>
        </aside>
    </div>
</body>
</html>