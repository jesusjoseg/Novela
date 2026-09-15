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
            try {
                $password_hash = password_hash($contrasena, PASSWORD_BCRYPT);

                // Consulta limpia apuntando a la tabla 'usuarios'
                $sql = "INSERT INTO usuario (nombre, apellido, email, contrasena, auth_provider, es_premium, rol, fecha_registro) 
                        VALUES (:nombre, :apellido, :email, :contrasena, 'local', FALSE, 'usuario'::rol_usuario, NOW())";
                
                $stmt = $conexion->prepare($sql);
                
                // Pasamos exactamente todos los parámetros definidos en la SQL
                $stmt->execute([
                    ':nombre'     => $nombre,
                    ':apellido'   => $apellido,
                    ':email'      => $correo,
                    ':contrasena' => $password_hash
                ]);

                header("Location: Login.php?registro=exito");
                exit();

            } catch (PDOException $e) {
                error_log("Error PDO en registro: " . $e->getMessage());
                die("<h3 style='color:red;'>Error al registrar en la base de datos:</h3> " . htmlspecialchars($e->getMessage()));
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