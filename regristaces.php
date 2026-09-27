<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regrista usuario</title>
    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <?php include 'Header.php'?>
    <div class="container form-contrainer">
        
            <h2 class="form-title">Crear Cuenta</h2>
            <form action="regristo.php" method="post">
                <div class="form-group">
                    <h3>Nombre:</h3>
                    <input type="text" name="Nombre" id="Nombre"class="finder-input"required>
                </div>
                <div class="form-group">
                    <h3>Apellido:</h3>
                    <input type="text" name="Apellido" id="Apellido"class="finder-input"required>
                </div>
                <div class="form-group">
                    <h3>Correo:</h3>
                    <input type="email" name="Correo" id="Correo"class="finder-input"required>
                </div>
                <div class="form-group">
                    <h3>Contraseña:</h3>
                    <input type="password" name="Contrasena" id="Contrasena"class="finder-input"required>
                </div>
                <div class="form-group">
                    <h3>Verifica Contraseña:</h3>
                    <input type="password" name="VerificaContrasena" id="VerificaContrasena" class="finder-input"required>
                </div>
                <div>
                    <input type="submit" value="Regritaces" class="btn-leer form-btn">
                </div>
            </form>
        <hr/>
        <div>
            <h2>
                Authentication
            </h2>
        </div>
        
    </div>
    <?php include'footer.php'?>
</body>
</html>