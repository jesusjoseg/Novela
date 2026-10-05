<?php
session_start();
require_once 'HHH/Conexion.php'; // Cambiar a 'hhh/Conexion.php' según la estructura de carpetas de tu servidor

$id_novela = isset($_GET['id']) ? intval($_GET['id']) : 0;
$novela = null;
$primer_capitulo_id = null;
$lista_capitulo = [];
$lista_comentarios = [];

if ($id_novela <= 0) {
    header("Location: index.php");
    exit();
}

// 1. Obtener los datos de la novela actual a través de la API
$res_novela = supabase_request("novela?id=eq.{$id_novela}&select=*");

if (!empty($res_novela) && !isset($res_novela['error'])) {
    $novela = $res_novela[0];
} else {
    header("Location: index.php");
    exit();
}

// 2. Incrementar visitas (vía API REST con PATCH)
if (isset($_GET['accion']) && $_GET['accion'] === 'incrementar') {
    $visitas_actuales = intval($novela['Visitas'] ?? 0) + 1;
    supabase_request("novela?id=eq.{$id_novela}", 'PATCH', ['Visitas' => $visitas_actuales]);
    $novela['Visitas'] = $visitas_actuales; // Actualizar localmente para mostrar el valor nuevo
}

// 3. Verificar estado de suscripción VIP del usuario
$es_vip = $_SESSION['usuario_vip'] ?? $_SESSION['es_vip'] ?? false;

// 4. Obtener la lista de capítulos ordenados por número de capítulo
$res_capitulos = supabase_request("capitulos?novela_id=eq.{$id_novela}&select=id,Capitulo,Titulo,fecha_Publicacion&order=Capitulo.asc");

if (!empty($res_capitulos) && !isset($res_capitulos['error'])) {
    $lista_capitulo = $res_capitulos;
    $primer_capitulo_id = $lista_capitulo[0]['id'];
}

// 5. Procesar el envío de un nuevo comentario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_comentario'])) {
    $comentario_texto = trim($_POST['nuevo_comentario']);
    $usuario_id = $_SESSION['usuario_id'] ?? $_SESSION['id'] ?? null;

    if (!empty($comentario_texto) && $usuario_id !== null) {
        $nuevo_registro = [
            'usuario_id' => $usuario_id,
            'novela_id'  => $id_novela,
            'comentario' => $comentario_texto
        ];

        supabase_request('comentarios', 'POST', $nuevo_registro);
        header("Location: ver_novela.php?id=" . $id_novela);
        exit();
    }
}

// 6. Obtener la lista de comentarios uniendo con la tabla de usuarios
$res_comentarios = supabase_request("comentarios?novela_id=eq.{$id_novela}&select=id,comentario,fecha,usuarios(nombre)&order=fecha.desc");

if (!empty($res_comentarios) && !isset($res_comentarios['error'])) {
    $lista_comentarios = $res_comentarios;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($novela['Titulo'] ?? 'Novela'); ?> - Lectura Novela</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="shortcut icon" href="src/image/gemini-svg (1).ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
</head>
<body>
    <?php include 'Header.php'; ?>
    <div class="container">
       <div class="novela-detalle-layout">
        <aside class="novela-sidebar">
            <img src="<?php echo htmlspecialchars($novela['Portada'] ?? ''); ?>" class="novela-portada" alt="<?php echo htmlspecialchars($novela['Titulo'] ?? ''); ?>">
            <div class="novela-ficha">
                <p><strong>Autor: </strong> <?php $usuario_creador = $novela['usuario']?? null;
                $usuario_id_creador =$novela['usuario_id']??($usuario_creador['id']??null);
                if (!empty($usuario_id_creador)):
                    $nombre_creador=$usuario_creador['nombre']??$novela['autor_original']?? 'ver Pefil';
                ?>
                <a href="PefilCreador.php?id=<?php echo $usuario_id_creador ?>" class="link-autor"> <?php echo htmlspecialchars($nombre_creador) ?></a>
                <?php else: ?>
                    <span><?php echo htmlspecialchars($novela['autor_original']) ?></span>
                <?php endif; ?>
                <p><strong>Género: </strong> <?php echo htmlspecialchars($novela['Genero'] ?? ''); ?></p>
                <p><strong>Estado: </strong> <?php echo htmlspecialchars($novela['Estado'] ?? ''); ?></p>
                <p><strong>Visitas: </strong> <?php echo htmlspecialchars($novela['Visitas'] ?? 0); ?></p>
            </div>
        </aside>
        <main class="novela-contenido">
            <h1 class="novela-titulo-principal"><?php echo htmlspecialchars($novela['Titulo'] ?? ''); ?></h1>
            <h3>Sinopsis</h3>
            <p class="novela-sinopsis"><?php echo htmlspecialchars($novela['Descripcion'] ?? ''); ?></p>
            
            <div class="novela-acciones">
                <?php if ($primer_capitulo_id): ?>
                    <a href="leer_capitulo.php?id=<?php echo $primer_capitulo_id; ?>&novela_id=<?php echo $id_novela; ?>" class="btn-leer">Comenzar a Leer</a>
                <?php else: ?>
                    <span class="btn-disabled">Sin capítulos disponibles</span>
                <?php endif; ?>
            </div>
            
            <div class="seccion-Capitulos">
                <h3>Lista de Capítulos</h3>
                <?php if (!empty($lista_capitulo)): ?>
                    <ul class="lista_capitulos">
                        <?php 
                        $fecha_actual = new DateTime();
                        foreach ($lista_capitulo as $cap): 
                            $num_cap = intval($cap['Capitulo'] ?? 0);
                            
                            $bloqueado = false;
                            $diferencia_dias = 0;
                            if ($num_cap > 15 && !$es_vip) {
                                $fecha_cap = new DateTime($cap['fecha_Publicacion'] ?? 'now');
                                $diferencia_dias = $fecha_actual->diff($fecha_cap)->days;
                                if ($diferencia_dias < 15) {
                                    $bloqueado = true;
                                }
                            }
                        ?>
                            <li class="<?php echo $bloqueado ? 'capitulo-bloqueado' : ''; ?>">
                                <?php if ($bloqueado): ?>
                                    <span class="cap-link deshabilitado">
                                        <i class="fa-solid fa-lock"></i> Capítulo <?php echo $num_cap; ?>: <?php echo htmlspecialchars($cap['Titulo'] ?? ''); ?> 
                                        <small>(Disponible con VIP o en <?php echo (15 - $diferencia_dias); ?> días)</small>
                                    </span>
                                <?php else: ?>
                                    <a href="leer_capitulo.php?id=<?php echo $cap['id']; ?>&novela_id=<?php echo $id_novela; ?>">
                                        Capítulo <?php echo $num_cap; ?>: <?php echo htmlspecialchars($cap['Titulo'] ?? ''); ?>
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>  
                    </ul>
                <?php else: ?>
                    <p>Aún no hay capítulos subidos para esta novela.</p>
                <?php endif; ?>
            </div>
            <?php include 'anuncio2.php'; ?>
            <!-- Sección de Comentarios -->
            <div class="seccion-comentarios">
                <h3>Comentarios (<?php echo count($lista_comentarios); ?>)</h3>

                <?php if (isset($_SESSION['usuario_id']) || isset($_SESSION['id'])): ?>
                    <form method="POST" class="form-comentario">
                        <textarea name="nuevo_comentario" placeholder="Escribe tu opinión sobre esta novela..." required></textarea>
                        <button type="submit" class="btn-comentar">Publicar Comentario</button>
                    </form>
                <?php else: ?>
                    <p><em>Debes <a href="login.php">iniciar sesión</a> para publicar comentarios.</em></p>
                <?php endif; ?>

                <div class="lista-comentarios">
                    <?php if (!empty($lista_comentarios)): ?>
                        <?php foreach ($lista_comentarios as $com): ?>
                            <div class="comentario-card">
                                <div class="comentario-header">
                                    <strong class="comentario-usuario"><?php echo htmlspecialchars($com['usuarios']['nombre'] ?? 'Usuario'); ?></strong>
                                    <span class="comentario-fecha"><?php echo $com['fecha'] ?? ''; ?></span>
                                </div>
                                <p class="comentario-texto"><?php echo nl2br(htmlspecialchars($com['comentario'] ?? '')); ?></p>
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
    <?php include 'footer.php'; ?>
</body>
</html>