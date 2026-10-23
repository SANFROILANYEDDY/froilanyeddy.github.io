<?php
// Datos de configuración del servidor de base de datos
$host = 'localhost';
$dbname = 'study_boost_2'; // Nombre de la base de datos identificado en el sistema
$username = 'root';       // Usuario por defecto en entornos locales como XAMPP o WAMP
$password = '';           // Contraseña por defecto (generalmente vacía en entornos locales)

try {
    // Establecer conexión usando PDO con codificación utf8mb4 para soportar acentos y emojis
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Configurar el manejo de errores para que lance excepciones en caso de fallos
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Mensaje opcional de comprobación (puedes comentarlo o borrarlo en producción)
    // echo "¡Conexión exitosa a la base de datos de Study Boost!";

} catch (PDOException $e) {
    // Capturar y mostrar el error si la conexión falla
    die("Error al conectar con la base de datos: " . $e->getMessage());
}
?>