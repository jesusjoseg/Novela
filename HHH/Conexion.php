<?php
// conexion.php - Conexión global a la base de datos Supabase PostgreSQL

$host     = 'aws-0-us-east-2.pooler.supabase.com'; // Dirección de tu Pooler
$port     = '6543';                                 // Puerto del Pooler IPv4
$dbname   = 'postgres';
$user     = 'postgres.ahprflxvnrovrwxaojrw';       // Usuario con referencia del proyecto
$password = '2P9YTrOLrDXvP7FW';                             // Reemplaza por tu contraseña real

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";

try {
    $conexion = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // En producción es recomendable registrar el error en un log y no exponer detalles sensibles
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>