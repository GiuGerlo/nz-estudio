<?php
session_start();

// Lista de páginas que no requieren autenticación
$public_pages = ['login.php', 'auth.php', 'logout.php'];
$current_page = basename($_SERVER['PHP_SELF']);

// Si la página actual no es pública y el usuario no está autenticado, redirigir al login
if (!in_array($current_page, $public_pages) && !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Si el usuario está autenticado y trata de acceder al login, redirigir a inicio
if (($current_page === 'login.php' || $current_page === 'auth.php') && isset($_SESSION['user_id'])) {
    header('Location: inicio');
    exit;
}
?>
