<?php
require_once '../config/config.php';

// Obtener estadísticas para el dashboard
$totalPropiedades = $db->query("SELECT COUNT(*) as total FROM propiedades")->fetch_assoc()['total'];
$totalCategorias = $db->query("SELECT COUNT(*) as total FROM tipos_propiedad")->fetch_assoc()['total'];
$totalImagenes = $db->query("SELECT COUNT(*) as total FROM imagenes_propiedades")->fetch_assoc()['total'];

// Obtener últimas propiedades agregadas
$ultimasPropiedades = $db->query("SELECT id, titulo, localidad FROM propiedades ORDER BY id DESC LIMIT 5");

// Incluir cabecera
include_once 'includes/head.php';
?>

        <!-- Dashboard Content -->
        <div class="container-fluid px-0">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="mb-4">Dashboard</h2>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-6 col-lg-3 mb-3 mb-lg-0">
                    <div class="card stat-card card-accent-blue">
                        <i class="fas fa-building text-muted"></i>
                        <div class="stat-number"><?php echo $totalPropiedades; ?></div>
                        <div class="stat-label">Propiedades Activas</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-3 mb-lg-0">
                    <div class="card stat-card card-accent-orange">
                        <i class="fas fa-tags text-muted"></i>
                        <div class="stat-number"><?php echo $totalCategorias; ?></div>
                        <div class="stat-label">Categorías</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card card-accent-purple">
                        <i class="fas fa-images text-muted"></i>
                        <div class="stat-number"><?php echo $totalImagenes; ?></div>
                        <div class="stat-label">Imágenes</div>
                    </div>
                </div>
            </div>

            <!-- Recent Properties -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Últimas Propiedades</h5>
                            <a href="propiedades.php" class="btn btn-sm btn-custom-blue">Ver Todas</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Título</th>
                                            <th>Localidad</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($ultimasPropiedades && $ultimasPropiedades->num_rows > 0): ?>
                                            <?php while ($propiedad = $ultimasPropiedades->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $propiedad['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($propiedad['titulo']); ?></td>
                                                    <td><?php echo htmlspecialchars($propiedad['localidad']); ?></td>
                                                    <td>
                                                        <a href="editar_propiedad.php?id=<?php echo $propiedad['id']; ?>" class="btn btn-sm btn-custom-blue"><i class="fas fa-edit"></i></a>
                                                        <a href="ver_propiedad.php?id=<?php echo $propiedad['id']; ?>" class="btn btn-sm btn-custom-blue"><i class="fas fa-eye"></i></a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">No hay propiedades disponibles</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Acciones Rápidas</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="nueva_propiedad.php" class="btn btn-custom-blue w-100 py-3">
                                        <i class="fas fa-plus-circle mb-2 d-block" style="font-size: 24px;"></i>
                                        Nueva Propiedad
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="nueva_categoria.php" class="btn btn-custom-blue w-100 py-3">
                                        <i class="fas fa-folder-plus mb-2 d-block" style="font-size: 24px;"></i>
                                        Nueva Categoría
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="subir_imagenes.php" class="btn btn-custom-blue w-100 py-3">
                                        <i class="fas fa-upload mb-2 d-block" style="font-size: 24px;"></i>
                                        Subir Imágenes
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="../index.php" target="_blank" class="btn btn-custom-blue w-100 py-3">
                                        <i class="fas fa-globe mb-2 d-block" style="font-size: 24px;"></i>
                                        Ver Sitio Web
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
// Incluir pie de página
include_once 'includes/footer.php';
?>