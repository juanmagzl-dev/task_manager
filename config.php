<?php
$host = 'dpg-cuqbimlds78s739aoe60-a';
$usuario = 'task_manager_4t2m_user';
$contraseña = '5lv05n1QZsYHolEl9lJqI91Ulx129At4';
$base_de_datos = 'task_manager_4t2m';

// Crear conexión
$conn = pg_connect("host=$host dbname=$base_de_datos user=$usuario password=$contraseña");

// Comprobar la conexión
if (!$conn) {
    die("Conexión fallida: " . pg_last_error());
}
echo "Conectado a la base de datos PostgreSQL correctamente";
?>
