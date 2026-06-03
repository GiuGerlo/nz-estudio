<?php
// Iniciar sesión solo si es necesario y si no se mandaron headers
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

require_once __DIR__ . '/env.php';

// Detectar entorno (local o producción)
$isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1']) ||
           (function_exists('str_contains') && str_contains($_SERVER['HTTP_HOST'] ?? '', 'local'));

if ($isLocal) {
    $server   = env('DB_LOCAL_HOST', 'localhost:3307');
    $username = env('DB_LOCAL_USER', 'root');
    $password = env('DB_LOCAL_PASS', '');
    $database = env('DB_LOCAL_NAME', 'nz-estudio');
} else {
    $server   = env('DB_PROD_HOST');
    $username = env('DB_PROD_USER');
    $password = env('DB_PROD_PASS');
    $database = env('DB_PROD_NAME');
}

// Crear conexión con MySQL
$db = new mysqli($server, $username, $password, $database);

// Verificar la conexión
if ($db->connect_error) {
    die("❌ Error de conexión a la base de datos: " . $db->connect_error);
}

// Evitar problemas con acentos y caracteres especiales
$db->set_charset("utf8mb4");

// Constantes de servicios externos
define('GOOGLE_MAPS_API_KEY', env('GOOGLE_MAPS_API_KEY', ''));
define('GOOGLE_ANALYTICS_ID', env('GOOGLE_ANALYTICS_ID', ''));
