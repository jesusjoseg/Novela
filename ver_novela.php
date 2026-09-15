<?php
session_start();
include 'HHH/Conexion.php';

$id_novela = isset($_GET['id']) ? intval($_GET['id']) : 0;
$novela = null;
$primer_capitulo_id = null;
$lista_capitulo = [];
$lista_comentarios = [];

// 1. Obtener la novela
$stmt = $coon->prepare("SELECT id, Titulo, Descripcion, Genero, Estado, Portada, link, Visitas FROM novela WHERE ID = ?");
$stmt->bind_param("i", $id_novela);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $res->num_rows > 0) {
    $novela = $res->fetch_assoc();
}
$stmt->close();

if (!$novela) {
    header("Location: biblioteca.php");
    exit();
}

// 2. Obtener lista de capítulos
$stmt_cap = $coon->prepare("SELECT id, Capitulo, Titulo FROM Capitulos WHERE novela_id = ? ORDER BY Capitulo ASC");
$stmt_cap->bind_param("i", $id_novela);
$stmt_cap->execute();
$res_cap = $stmt_cap->get_result();
while ($row = $res_cap->fetch_assoc()) {
    $lista_capitulo[] = $row;
}
$stmt_cap->close();

if (!empty($lista_capitulo)) {
    $primer_capitulo_id = $lista_capitulo[0]['id'];
}

// 3. Procesar el envío de un nuevo comentario (Web)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_comentario'])) {
    $comentario_texto = trim($_POST['nuevo_comentario']);
    $usuario_id = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : 1; // ID temporal de prueba si no hay sesión

    if (!empty($comentario_texto)) {
        $stmt_ins = $coon->prepare("INSERT INTO comentarios (usuario_id, novela_id, comentario) VALUES (?, ?, ?)");
        $stmt_ins->bind_param("iis", $usuario_id, $id_novela, $comentario_texto);
        $stmt_ins->execute();
        $stmt_ins->close();

        // Redirigir para evitar reenvío de formulario al recargar
        header("Location: ver_novela.php?id=" . $id_novela);
        exit();
    }
}

// 4. Obtener la lista de comentarios
$stmt_com = $coon->prepare("
    SELECT c.id, c.comentario AS texto, c.fecha, u.nombre AS usuario 
    FROM comentarios c 
    JOIN usuarios u ON c.usuario_id = u.id 
    WHERE c.novela_id = ? 
    ORDER BY c.fecha DESC
");
$stmt_com->bind_param("i", $id_novela);
$stmt_com->execute();
$res_com = $stmt_com->get_result();
while ($row = $res_com->fetch_assoc()) {
    $lista_comentarios[] = $row;
}
$stmt_com->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($novela['Titulo']); ?> - Lectura Novela</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
       <div class="novela-detalle-layout">
        <aside class="novela-sidebar">
            <img src="<?php echo htmlspecialchars($novela['Portada']); ?>" class="novela-portada" alt="<?php echo htmlspecialchars($novela['Titulo']); ?>">
            <div class="novela-ficha">
                <p><strong>Genero: </strong> <?php echo htmlspecialchars($novela['Genero']); ?></p>
                <p><strong>Estado: </strong> <?php echo htmlspecialchars($novela['Estado']); ?></p>
                <p><strong>Visitas: </strong> <?php echo htmlspecialchars($novela['Visitas']); ?></p>
            </div>
        </aside>
        <main class="novela-contenido">
            <h1 class="novela-titulo-principal"><?php echo htmlspecialchars($novela['Titulo']); ?></h1>
            <h3>Sinopsis</h3>
            <p class="novela-sinopsis"><?php echo htmlspecialchars($novela['Descripcion']); ?></p>
            
            <div class="novela-acciones">
                <?php if ($primer_capitulo_id): ?>
                    <a href="leer_capitulo.php?id=<?php echo $primer_capitulo_id; ?>" class="btn-leer">Comenzar a Leer</a>
                <?php else: ?>
                    <span class="btn-disabled">Sin capítulos disponibles</span>
                <?php endif; ?>
            </div>
            
            <div class="seccion-Capitulos">
                <h3>Lista de Capítulos</h3>
                <?php if (!empty($lista_capitulo)): ?>
                    <ul class="lista_capitulos">
                        <?php foreach ($lista_capitulo as $cap): ?>
                            <li>
                                <a href="leer_capitulo.php?id=<?php echo $cap['id']; ?>">
                                    Capítulo <?php echo $cap['Capitulo']; ?>: <?php echo htmlspecialchars($cap['Titulo']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>  
                    </ul>
                <?php else: ?>
                    <p>Aún no hay capítulos subidos para esta novela.</p>
                <?php endif; ?>
            </div>

            <!-- Sección de Comentarios -->
            <div class="seccion-comentarios">
                <h3>Comentarios (<?php echo count($lista_comentarios); ?>)</h3>

                <!-- Formulario para publicar comentarios -->
                <form method="POST" class="form-comentario">
                    <textarea name="nuevo_comentario" placeholder="Escribe tu opinión sobre esta novela..." required></textarea>
                    <button type="submit" class="btn-comentar">Publicar Comentario</button>
                </form>

                <!-- Listado de Comentarios -->
                <div class="lista-comentarios">
                    <?php if (!empty($lista_comentarios)): ?>
                        <?php foreach ($lista_comentarios as $com): ?>
                            <div class="comentario-card">
                                <div class="comentario-header">
                                    <strong class="comentario-usuario"><?php echo htmlspecialchars($com['usuario']); ?></strong>
                                    <span class="comentario-fecha"><?php echo $com['fecha']; ?></span>
                                </div>
                                <p class="comentario-texto"><?php echo nl2br(htmlspecialchars($com['texto'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="sin-comentarios">Sé el primero en comentar esta novela.</p>
                    <?php endif; ?>
                </div>
            </div>

        </main>
       </div> 
    </div>
    <?php include'footer.php'?>
</body>
</html>