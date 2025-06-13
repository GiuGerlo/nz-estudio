<?php
// Iniciar sesión solo si es necesario y si no se mandaron headers
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

// Detectar entorno (local o producción)
$isLocal = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']) || 
           (function_exists('str_contains') && str_contains($_SERVER['HTTP_HOST'], 'local'));

if ($isLocal) {
    // Configuración para entorno local
    $server = 'localhost:3307';
    $username = 'root';
    $password = '';
    $database = 'nz-estudio';
} else {
    // Configuración para entorno de producción
    $server = 'localhost';
    $username = 'u692790713_alpaweb';
    $database = 'u692790713_alpaweb';
    $password = 'Ramcc202323@'; 
}

// Crear conexión con MySQL
$db = new mysqli($server, $username, $password, $database);

// Verificar la conexión
if ($db->connect_error) {
    die("❌ Error de conexión a la base de datos: " . $db->connect_error);
}

// Evitar problemas con acentos y caracteres especiales
$db->set_charset("utf8mb4");

// Configuración de Google Maps
define('GOOGLE_MAPS_API_KEY', 'AIzaSyAD4aOZDL-d6jLIq8_HfHdReWIrQEgMVBE');
?>
