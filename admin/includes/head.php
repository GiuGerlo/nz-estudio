<?php
// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Obtener la página actual para marcar el menú activo
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - NZ Estudio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Favicons -->
    <link href="../assets/img/logo.ico" rel="icon">
    <link href="../assets/img/logo.png" rel="apple-touch-icon">
    <link rel="stylesheet" href="assets/css/main.css">
    <?php if (isset($includeDataTablesStyles) && $includeDataTablesStyles): ?>
        <!-- DataTables CSS -->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <?php endif; ?>

    <!-- jQuery (necesario para DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="px-3 mb-4 sidebar-brand">
            <a href="admin.php"><img src="../assets/img/logo.png" alt="NZ Estudio" class="img-fluid" style="width: 80px;"></a>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'admin.php') ? 'active' : ''; ?>" href="admin.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'propiedades.php') ? 'active' : ''; ?>" href="propiedades.php">
                    <i class="fas fa-building"></i>
                    <span>Propiedades</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'vendidas.php') ? 'active' : ''; ?>" href="vendidas.php">
                    <i class="fas fa-check-circle"></i>
                    <span>Vendidas</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'categorias.php') ? 'active' : ''; ?>" href="categorias.php">
                    <i class="fas fa-tags"></i>
                    <span>Categorías</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg mb-4 rounded">
            <div class="container-fluid">
                <button class="btn d-lg-none toggle-sidebar" type="button">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="navbar-brand mb-0 h1">Panel de Administración</span>
            </div>
        </nav>