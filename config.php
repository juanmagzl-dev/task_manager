<?php
$host = getenv('mysql.railway.internal');
$dbname = getenv('railway');
$user = getenv('root');
$password = getenv('HCpgRfXCDwtalklAkzyoMPCsjSzfMnDK');
$port = getenv('3306');

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
?>
