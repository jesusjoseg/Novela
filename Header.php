
<header>
    <ul>
        <li>
            <a href="index.php"><img src="#" alt="Logo"></a>
        </li>
        <li>
            <a href="index.php">Inicio</a>
        </li>
        <li>
             Estados
            <ul>
                <li><a href="Compleatado.php">Compleatado</a></li>
                <li><a href="Pendiente.php">Pendiente</a></li>
                <li><a href="Pausado.php">Pausado</a></li>
                <li><a href="Finalizado.php">Finalizado</a></li>
            </ul>
        </li>
        <li>
            <a href="Actulizacion.php">Actulizacion</a>
        </li>
        <li>
            <a href="biblioteca.php">Biblioteca</a>
        </li>
        <li>
            <div>
                <input type="text" name="query" id="buscador-input" placeholder="Buscar Novela" autocomplete="off" required>
                <button type="submit" >🔍</button>
                <div id="resultados-busqueda"></div>
            </div>

        </li>
        <script src="Buscador.js"></script>
        <li style="margin-left:auto;">
            <?php if(isset($_SESSION['usuario_nombre'])): ?>
                Hola <?php echo htmlspecialchars($_SESSION['usuario_nombre']);?>
                <ul>
                    <?php if ($_SESSION['usuario_rol']&& $_SESSION['usuario_rol'] === 'admin'):?>
                        <li>
                            <a href="#" id="dash">Dashboard</a>
                            /
                            <a href="#">usuario</a>
                        </li>
                    <?php else:?>
                        <li>
                            <a href="#">usuario</a>
                        </li>
            <?php endif;?>
                    <li>
                        <a href="#">Cerra sesion</a>
                    </li>
                </ul>
            <?php else:?>
                <a href="Login.php">Iniciar Sesion</a>
                /
                <a href="regristaces.php">Regristase</a>
            <?php endif;?>
        </li>
    </ul>
</header>