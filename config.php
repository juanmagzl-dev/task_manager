<?php
$host = "switchyard.proxy.rlwy.net";  // Host de Railway
$port = "16312";  // Puerto de Railway
$dbname = "railway";  // Nombre de la base de datos
$user = "root";  // Usuario de Railway
$password = "HCpgRfXCDwtalklAkzyoMPCsjSzfMnDK";  // Contraseña de Railway

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
?>
