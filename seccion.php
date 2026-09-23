<?php
session_start();
require_once 'HHH/Conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo     = trim($_POST['Correo'] ?? '');
    $contrasena = trim($_POST['Contrasena'] ?? '');

    if (!empty($correo) && !empty($contrasena)) {
        try {
            // Consulta a la API REST de Supabase filtrando por el email
            // Equivalente a: SELECT id, nombre, apellido, contrasena, rol FROM usuario WHERE email = :email
            $endpoint = 'usuario?email=eq.' . urlencode($correo) . '&select=id,nombre,apellido,contrasena,rol';
            $respuesta = supabase_request($endpoint, 'GET');

            // Verificar si ocurrió un error en la petición cURL
            if (is_array($respuesta) && isset($respuesta['error']) && $respuesta['error'] === true) {
                error_log("Error de Supabase: " . $respuesta['message']);
                header("Location: Login.php?error=servidor");
                exit();
            }

            // Supabase REST devuelve un arreglo JSON con las filas encontradas
            if (!empty($respuesta) && is_array($respuesta)) {
                $usuario = $respuesta[0]; // Tomamos el primer registro encontrado

                // Verificar si la contraseña ingresada coincide con el hash almacenado
                if (isset($usuario['contrasena']) && password_verify($contrasena, $usuario['contrasena'])) {
                    session_regenerate_id(true); // Previene ataques de fijación de sesión

                    $_SESSION['usuario_id']     = $usuario['id'];
                    $_SESSION['usuario_nombre'] = $usuario['nombre'];
                    $_SESSION['usuario_rol']    = $usuario['rol'];

                    header('Location: index.php');
                    exit();
                }
            }

            // Credenciales incorrectas o usuario no encontrado
            header("Location: Login.php?error=credenciales");
            exit();

        } catch (Exception $e) {
            error_log("Error general en login: " . $e->getMessage());
            die("<h3 style='color:red;'>Error al iniciar sesión:</h3> " . htmlspecialchars($e->getMessage()));
        }
    }
}

// Redirección si los campos requeridos estaban vacíos
header("Location: login.php?error=campos");
exit();
?>