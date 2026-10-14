<?php
// Pendiente.php
session_start();
require_once 'HHH/Conexion.php'; // Asegúrate de que coincida con la ruta de tu conexión

// 1. Consultar a Supabase las novelas cuyo estado sea 'En emisión' o 'Pendiente'
// Puedes cambiar 'En emision' por el texto exacto que guardes en tu columna 'Estado' (ej. 'En Emision' o 'Pendiente')
$res_novelas = supabase_request("novela?Estado=eq.Pendiente&select=*&order=Titulo.asc");

$lista_novelas = [];
if (!empty($res_novelas) && !isset($res_novelas['error'])) {
    $lista_novelas = $res_novelas;
}

$tituloSEO = "Novelas en Emisión y Pendientes - FoxNovel";
$descripcionSEO = "Explora las novelas en emisión de FoxNovel. Sigue de cerca las historias en desarrollo con nuevos capítulos cada semana.";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $tituloSEO; ?></title>
    <meta name="description" content="<?php echo $descripcionSEO; ?>">
    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
    <link rel="stylesheet" href="Style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-dark text-light">
    <?php include 'Header.php'; ?>

    <div class="container my-5">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #ff6b35; font-size: 2.2rem; margin-bottom: 10px;">
                <i class="fa-solid fa-clock-rotate-left" style="color: #ff9946;"></i> Novelas en Emisión (Pendientes)
            </h1>
            <p style="color: #8e8e93; font-size: 1.05rem;">
                Historias en desarrollo activo. ¡Sigue el ritmo de publicación de los nuevos capítulos!
            </p>
        </div>

        <?php if (!empty($lista_novelas)): ?>
            <div class="catalogo-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px;">
                <?php foreach ($lista_novelas as $nov): ?>
                    <div class="novela-card" style="background: #1a1613; border: 1px solid #26201b; border-radius: 10px; overflow: hidden; display: flex; flex-direction: column; transition: transform 0.2s;">
                        <a href="ver_novela.php?id=<?php echo $nov['id']; ?>" style="text-decoration: none; color: inherit;">
                            <img src="<?php echo htmlspecialchars($nov['Portada'] ?? 'src/image/default.png'); ?>" alt="<?php echo htmlspecialchars($nov['Titulo']); ?>" style="width: 100%; height: 250px; object-fit: cover;">
                            <div style="padding: 12px;">
                                <h3 style="font-size: 1rem; color: #fff; margin-bottom: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?php echo htmlspecialchars($nov['Titulo']); ?>
                                </h3>
                                <p style="font-size: 0.85rem; color: #ff9946; margin-bottom: 8px;">
                                    <?php echo htmlspecialchars($nov['Genero'] ?? 'Novela'); ?>
                                </p>
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: #8e8e93;">
                                    <span><i class="fa-solid fa-eye"></i> <?php echo intval($nov['Visitas'] ?? 0); ?></span>
                                    <span style="background: #3d2e00; color: #ffd700; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 0.75rem;">Pendiente</span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 50px 20px; background: #1a1613; border-radius: 10px; border: 1px solid #26201b;">
                <i class="fa-solid fa-book-bookmark" style="font-size: 50px; color: #8e8e93; margin-bottom: 15px;"></i>
                <h3 style="color: #fff; margin-bottom: 10px;">No hay novelas pendientes en este momento</h3>
                <p style="color: #8e8e93;">Todas nuestras obras se encuentran al día o han finalizado su publicación.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>