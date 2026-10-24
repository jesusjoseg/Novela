<?php
// completado.php
session_start();
require_once 'HHH/Conexion.php'; // Asegúrate de que coincida con la ruta de tu conexión

// 1. Consultar a Supabase todas las novelas cuyo estado sea 'Completado' (o 'Terminada')
// Puedes ajustar el filtro según el texto exacto que guardes en tu columna 'Estado' (ej. eq.Completado o eq.Finalizada)
$res_novelas = supabase_request("novela?Estado=eq.Completado&select=*&order=Titulo.asc");

$lista_novelas = [];
if (!empty($res_novelas) && !isset($res_novelas['error'])) {
    $lista_novelas = $res_novelas;
}

$tituloSEO = "Novelas Completadas - Leer Online Gratis | FoxNovel";
$descripcionSEO = "Explora el catálogo completo de novelas finalizadas en FoxNovel. Disfruta de historias con todos sus capítulos disponibles de principio a fin.";
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
                <i class="fa-solid fa-circle-check" style="color: #22c55e;"></i> Novelas Completadas
            </h1>
            <p style="color: #8e8e93; font-size: 1.05rem;">
                Disfruta de historias 100% finalizadas, sin esperas ni capítulos pendientes.
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
                                    <span style="background: #1e3a29; color: #4ade80; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 0.75rem;">Completada</span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 50px 20px; background: #1a1613; border-radius: 10px; border: 1px solid #26201b;">
                <i class="fa-solid fa-book-open" style="font-size: 50px; color: #8e8e93; margin-bottom: 15px;"></i>
                <h3 style="color: #fff; margin-bottom: 10px;">Aún no hay novelas completadas</h3>
                <p style="color: #8e8e93;">Estamos trabajando para finalizar y subir nuevas historias muy pronto.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>