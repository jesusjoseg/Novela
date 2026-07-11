<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regrista usuario</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <?php include 'header.php'?>
    <main>
        <form action="regristo.php" method="post">
            <div>
                <label for="">Nombre</label>
                <input type="text" name="Nombre" id="Nombre"placeholder="Nombre">
            </div>
            <div>
                <label for="">Apellido</label>
                <input type="text" name="Apellido" id="apellido" placeholder="Apellido">
            </div>
        </form>
    </main>
</body>
</html>