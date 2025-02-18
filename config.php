<?php
// Obtener las credenciales de las variables de entorno de Railway
$host = getenv('shuttle.proxy.rlwy.net');  // mysql.railway.internal
$usuario = getenv('root');  // root
$contraseña = getenv('jhTsATHdKIyKOlTCYXouwDxEgsgwrbBo');  // jhTsATHdKIyKOlTCYXouwDxEgsgwrbBo
$base_de_datos = getenv('railway');  // railway
$puerto = getenv('3306');  // 3306

// Conectar a la base de datos
$conn = new mysqli($host, $usuario, $contraseña, $base_de_datos, $puerto);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Crear tabla de usuarios (si no existe)
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'users' creada exitosamente.<br>";
} else {
    echo "Error al crear la tabla 'users': " . $conn->error . "<br>";
}

// Crear tabla de tareas (si no existe)
$sql = "CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    is_completed BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'tasks' creada exitosamente.<br>";
} else {
    echo "Error al crear la tabla 'tasks': " . $conn->error . "<br>";
}

$conn->close();
?>
