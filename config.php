<?php
// Configuración de la base de datos
$host = 'localhost:3306';
$dbname = 'task_manager';
$usuario = 'prueba_daw';  // Asegúrate de que el usuario es correcto
$contraseña = 'prueba_daw'; // Verifica la contraseña

try {
    // Conexión a la base de datos con charset utf8 para evitar problemas de codificación
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $contraseña, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Habilita los errores de PDO como excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Configura el modo de obtención de datos
        PDO::ATTR_EMULATE_PREPARES => false // Desactiva la emulación de consultas preparadas por seguridad
    ]);

} catch (PDOException $e) {
    // Si hay un error, se muestra un mensaje sin detalles sensibles por seguridad
    die("Error de conexión a la base de datos. Verifica tus credenciales.");
}
?>
