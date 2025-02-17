<?php
$host = 'mysql.railway.internal'; // Proporcionado por Railway
$dbname = 'railway'; // Proporcionado por Railway
$username = 'root'; // Proporcionado por Railway
$password = 'BrVsmcAbUxVbYaBtFqPMjGnKkwuRhqxj'; // Proporcionado por Railway

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}
?>
