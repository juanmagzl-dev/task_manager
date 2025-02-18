<?php
$host = 'localhost'; // Cambiar si es necesario
$dbname = 'task_manager';
$username = 'prueba_daw'; // Cambiar si usas otro usuario
$password = 'prueba_daw'; // Cambiar si usas una contraseña

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}
?>
