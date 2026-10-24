<?php
session_start();
require_once 'HHH/Conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre      = trim($_POST['Nombre'] ?? '');
    $apellido    = trim($_POST['Apellido'] ?? '');
    $correo      = trim($_POST['Correo'] ?? '');
    $contrasena  = $_POST['Contrasena'] ?? '';
    $vContrasena = $_POST['VerificaContrasena'] ?? '';

    // Validar que ningún campo venga vacío y que las contraseñas coincidan
    if (!empty($nombre) && !empty($apellido) && !empty($correo) && !empty($contrasena) && ($contrasena === $vContrasena)) {
        
        if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            // Hash de contraseña seguro
            $password_hash = password_hash($contrasena, PASSWORD_BCRYPT);

            // Estructura del cuerpo de la petición para Supabase
            $datosUsuario = [
                'nombre'          => $nombre,
                'apellido'        => $apellido,
                'email'           => $correo,
                'contrasena'      => $password_hash,
                'auth_provider'   => 'local',
                'es_premium'      => false,
                'rol'             => 'usuario'
            ];

            // Enviar inserción a la API REST de Supabase mediante cURL
            $response = supabase_request('usuario', 'POST', $datosUsuario);

            if (is_array($response) && !isset($response['error'])) {
                header("Location: Login.php?registro=exito");
                exit();
            } else {
                error_log("Error al registrar en Supabase: " . json_encode($response));
                $mensajeError = $response['message'] ?? 'Error desconocido al registrar en Supabase.';
                die("<h3 style='color:red;'>Error al registrar en la base de datos:</h3> " . htmlspecialchars($mensajeError));
            }
        } else {
            die("Error: El correo ingresado no es válido.");
        }
    } else {
        die("Error: Por favor completa todos los campos y asegúrate de que las contraseñas coincidan.");
    }
}

header("Location: regristaces.php?error=campos");
exit();
?>