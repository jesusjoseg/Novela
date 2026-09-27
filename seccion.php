<?php
session_start();
require_once 'HHH/Conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo     = trim($_POST['Correo'] ?? '');
    $contrasena = trim($_POST['Contrasena'] ?? '');

    if (!empty($correo) && !empty($contrasena)) {
        
        // Petición GET a Supabase filtrando por el campo 'email'
        $endpoint = 'usuario?email=eq.' . rawurlencode($correo) . '&select=id,nombre,apellido,contrasena,rol';
        $response = supabase_request($endpoint, 'GET');

        if (is_array($response) && !isset($response['error']) && count($response) > 0) {
            $usuario = $response[0]; // Primer registro coincidente

            // Verificar la contraseña contra el hash de la base de datos
            if (password_verify($contrasena, $usuario['contrasena'])) {
                session_regenerate_id(true); // Previene fijación de sesión

                // Guardar datos clave del usuario en sesión
                $_SESSION['usuario_id']     = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol']    = $usuario['rol']; // 'admin', 'traductor' o 'usuario'

                header('Location: index.php');
                exit();
            } else {
                // Contraseña incorrecta
                header("Location: Login.php?error=credenciales");
                exit();
            }
        } else {
            // Usuario no encontrado o error en consulta
            header("Location: Login.php?error=credenciales");
            exit();
        }
    }
}

header("Location: Login.php?error=campos");
exit();
?>