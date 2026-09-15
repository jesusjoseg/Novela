<?php
session_start();
require_once 'HHH/Conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo     = trim($_POST['Correo'] ?? '');
    $contrasena = trim($_POST['Contrasena'] ?? '');

    if (!empty($correo) && !empty($contrasena)) {
        try {
            // Consulta adaptada a PDO y nombres en minúscula de PostgreSQL
            $stmt = $conexion->prepare("SELECT id, nombre, apellido, contrasena, rol FROM usuario WHERE email = :email");
            $stmt->execute([':email' => $correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar si el usuario existe y si la contraseña es correcta
            if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
                session_regenerate_id(true); // Previene fijación de sesión

                $_SESSION['usuario_id']     = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol']    = $usuario['rol'];

                header('Location: index.php');
                exit();
            } else {
                // Credenciales incorrectas
                header("Location: Login.php?error=credenciales");
                exit();
            }

        } catch (PDOException $e) {
            error_log("Error de login: " . $e->getMessage());
            die("<h3 style='color:red;'>Error al iniciar sesión:</h3> " . htmlspecialchars($e->getMessage()));
        }
    }
}

header("Location: Login.php?error=campos");
exit();
?>