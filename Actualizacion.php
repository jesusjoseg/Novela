<?php
session_start();
require_once 'HHH/Conexion.php';

$ultimos_capitulos = [];

try {
    // Consulta JOIN para obtener datos del capítulo y de la novela
    $sql = 'SELECT 
                c.id AS capitulo_id,
                c."Capitulo" AS num_capitulo,
                c."Titulo" AS titulo_capitulo,
                c."fecha_Publicacion" AS fecha,
                n.id AS novela_id,
                n."Titulo" AS novela_titulo,
                n."Portada" AS novela_portada
            FROM capitulos c
            INNER JOIN novela n ON c.novela_id = n.id
            ORDER BY c."fecha_Publicacion" DESC, c.id DESC
            LIMIT 30';

    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $ultimos_capitulos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error al consultar últimos capítulos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Últimos Capítulos Subidos - Foxnovel</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h1 class="seccion-titulo">Últimos Capítulos Subidos</h1>

        <div class="grid-capitulos">
            <?php if (!empty($ultimos_capitulos)): ?>
                <?php foreach ($ultimos_capitulos as $item): ?>
                    <div class="card-capitulo">
                        <a href="ver_novela.php?id=<?php echo $item['novela_id']; ?>">
                            <img src="<?php echo htmlspecialchars($item['novela_portada']); ?>" alt="<?php echo htmlspecialchars($item['novela_titulo']); ?>" class="card-portada">
                        </a>
                        <div class="card-info">
                            <span class="card-novela-titulo"><?php echo htmlspecialchars($item['novela_titulo']); ?></span>
                            <a href="leer_capitulo.php?id=<?php echo $item['capitulo_id']; ?>" class="card-capitulo-link">
                                Capítulo <?php echo $item['num_capitulo']; ?>: <?php echo htmlspecialchars($item['titulo_capitulo']); ?>
                            </a>
                            <span class="card-fecha">
                                <?php echo date('d/m/Y', strtotime($item['fecha'] ?? 'now')); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay capítulos recientes publicados.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>